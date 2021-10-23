# Symfony 5.2 multi-tenancy configuration #

The application generates a dynamic and secure MYSQL user with rights only on its own database which is created at the same time.

The application has only one controller to explain how the database change works.

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

Exemple:

```
docker exec -i sf5multitenancy_phpfpm bash -c "php bin/console doctrine:schema:update --force tenant=TENANT_ID_HERE"
```

#### Check DataBases with Adminer

You can look databases here : http://sf5-multitenancy.localhost:9002/?server=mariadb&username=root

## Test operation in "useful" condition

To test the controller, all you have to do is create 2 tenants (with the command above), update the schema of the 2 databases (with the command above), then in each database, simply create a user thanks to Adminer.

Then to see that the controller is working correctly, open your browser on the 2 links, and you will see the users according to the tenant specified in the URL.

- Link 1 : http://sf5-multitenancy.localhost/?tenant_id=1
- Link 2 : http://sf5-multitenancy.localhost/?tenant_id=2

I think for a SaaS platform it is enough to retrieve the tenant ID based on the logged in user rather than the URL but I wasn't going to go on a user login for such a small POC.

