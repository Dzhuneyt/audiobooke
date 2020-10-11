# REST APIs


## 1. Development
### 1.1 Getting started
* `php init` to create the necessary config files (select 0 for Development)
* `php composer.phar install` to pull all dependencies
* To configure your DB connection credentials and other important parameters, clone .env-dist to .env and edit it
* `php yii migrate`
* To use the built-in lite server (`http://localhost:8080`), use `php yii serve`. This lite server is a "good enough" companion for the frontend lite server (see /apps/frontend/README.md)

## 2. Testing

1. It is recommended to create separate DBs for testing and development purposes.
2. Configure the test DB's name and connection credentials in `common/config/tests-local.php`
2. Run tests `./vendor/bin/phpunit -c ./tests/functional/phpunit.xml` 

## 4. Fixtures
### 4.1 Generating fixture files (rarely used)

* Refer to the [Yii2 Faker Guide](https://github.com/yiisoft/yii2-faker/blob/master/docs/guide/basic-usage.md) for more information
* This will ONLY create the fixture files (that most likely need to be further adjusted after generation). This will NOT load the fixtures into the current database connection.
* Useful for testing


    php yii fixture/generate author --count=100
    php yii fixture/generate audiobook --count=2000
    
### 4.2 Load fixtures to database

WARNING: This will overwrite any existing data in the tables where the fixtures are being imported. BACKUP!

To import fixtures from static files into the current DB connection, use:

    php yii fixture/load "*"

## Production

The production website is deployed using a Docker Swarm based stack. Each app is a dedicated microservice. The microservices communicate with each other through Docker's overlay network.

The production deploy is entirely automated. All code that reaches the "master" branch, trigger an automated Continuous Integration workflow on CircleCI.com that:
1. Executes backend and frontend tests to confirm for potential regressions
2. Builds the Docker images for each microservice
3. Pushes the Docker images to AWS ECR (Elastic Container Service)

This concludes the **Continuous Integration** cycle.

Then, the **Continuous Deployment** cycle kicks in. There are a couple of DigitalOcean.com containers that participate in a Docker Swarm. There is a cron job on one of the manager nodes that periodically pulls new Docker Images from the AWS ECR and redeploys the stack of microservices (if there are ny changes).
