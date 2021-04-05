<?php

namespace App\Messenger\Serializer;

use App\Messenger\Message\BuildTenantMessage;
use App\Repository\Main\TenantRepository;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

/**
 * Class JsonSerializer
 * @package App\Messenger\Serializer
 */
class JsonSerializer implements SerializerInterface
{

	private TenantRepository $tenantRepository;

    /**
     * JsonSerializer constructor.
     * @param TenantRepository $tenantRepository
     */
	public function __construct(
		TenantRepository $tenantRepository
	) {
		$this->tenantRepository = $tenantRepository;
	}

	/**
	 * @param array $encodedEnvelope
	 * @return Envelope
	 */
	public function decode(
		array $encodedEnvelope
	): Envelope
	{
		$body = json_decode($encodedEnvelope['body'], true);

		if (!$body) {
			throw new MessageDecodingFailedException('The body is not a valid JSON.');
		}

		$type = $body['type'] ?? '';
		switch ($type) {
			case 'BuildTenantMessage':
				$message = new BuildTenantMessage($this->tenantRepository->find($body['idTenant']));
				break;
			default:
				throw new MessageDecodingFailedException("The type '$type' is not supported.");
		}

		return new Envelope($message);
	}

	/**
	 * @param Envelope $envelope
	 * @return array
	 */
	public function encode(
		Envelope $envelope
	): array
	{
		$message = $envelope->getMessage();

		if ($message instanceof BuildTenantMessage) {
			return [
				'body' => json_encode([
					'type' => 'BuildTenantMessage',
					'idTenant' => $message->getTenant()->getId(),
				]),
				'headers' => [
				],
			];
		}

		return [];
	}
}