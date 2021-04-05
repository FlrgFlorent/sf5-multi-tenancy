# Symfony 5.2 multi-tenancy configuration #

The application generates a dynamic and secure MYSQL user with rights only on its own database which is created at the same time.

The application does not have a front template. There are only entities, listener, and a tenant build service.

## Installation with Docker ##

See `.env` file, there are environment variables for the tenants build service (TENANT_DATABASE_HOST, etc...)

#### Run console commands for Docker & Symfony install

```
docker-compose up -d
docker exec -i sf5multitenancy_phpfpm bash -c "composer install"
```

#### Run console commands for Main DataBase

```
docker exec -i sf5multitenancy_phpfpm bash -c "php bin/console doctrine:schema:update --force"
```

#### Run console commands for create a Tenant

```
docker exec -i sf5multitenancy_phpfpm bash -c "php bin/console app:create-tenant TENANT_NAME_HERE"
docker exec -i sf5multitenancy_phpfpm bash -c "php bin/console messenger:consume async_priority_high -vv"
```

FYI : Tenant Name is Unique

FYI2 : If you don't wan't Symfony Messenger for async creation, in `config/packages/messenger.yaml`, comment on line 13

#### Command For play with Tenant DataBase

exemple:

```
docker exec -i sf5multitenancy_phpfpm bash -c "php bin/console doctrine:schema:update --force tenant=TENANT_ID_HERE"
```

#### Check DataBases with Adminer

You can look databases here : http://localhost:9002/?server=mariadb&username=root

