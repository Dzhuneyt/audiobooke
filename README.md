Audiobooke
===

The social network for audiobook fans.

The app consists of:
- Angular based frontend (TypeScript, JavaScript)
- A set of REST APIs on top of Yii 2 framework (PHP).

### Requirements

- NodeJS and NPM
- Docker and Docker Compose

### Getting started

1. `npm install`
2. `npm run dev`

## Deployment

The infrastructure is managed by an AWS CDK app, located in the /cdk subfolder.

Steps on how to deploy it:
1. `cd cdk`
2. `npm install`
3. Edit the .env file and define some basic variables like "ENV_NAME" (used to prefix all infrastructure with a common name like: develop, staging, production)
4. `npm run deploy:app`

Now you have a deployed VPC, subnets, ECS cluster and ECS services for backend and frontend. At this point, the backend is not yet working, because it's trying to connect to a non-existing MySQL database. Feel free to provision one manually through the AWS console or through another AWS CDK stack and define its credentials in the AWS Secrets Manager secret. The backend is trying to pick the credentials from ther, so if you place valid RDS credentials there - the backend will start communicating with that database.

### Contributors

[<img alt="Dzhuneyt - Software Development" src="https://github.com/Dzhuneyt.png?size=40">](https://dzhuneyt.com)
