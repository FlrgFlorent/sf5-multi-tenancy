<?php

namespace App\Services;

class Password
{

    /**
     * @return string
     * @var int $longueur must be greater than 8 to be GDPR and security good
     */
    public static function generate($longueur = 8)
    {

        $passwordArray = array();

        $nombreChiffre = $nombreMajuscule = round($longueur * 0.2, 0, PHP_ROUND_HALF_DOWN);
        $nombreMinuscule = $longueur - $nombreMajuscule - $nombreChiffre;

        $passwordArray[] = substr(str_shuffle("0123456789"), 0, $nombreChiffre);
        $passwordArray[] = substr(str_shuffle("ABCDEFGHIJKLMNOPSQRSTUVWXYZ"), 0, $nombreMajuscule);
        $passwordArray[] = substr(str_shuffle("abcdefghijklmnopqrstuvwyz"), 0, $nombreMinuscule);

        $password = implode('', $passwordArray);
        $password = str_shuffle($password);

        return $password;
    }

}
