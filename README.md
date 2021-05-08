Audiobooke
===

A portal for fans of Audiobooks. Powered by Librivox.

*Main features:*
* Browse and download 10,000+ free audiobooks for offline playback
* Add audiobooks to favorites

*Upcoming features:*
* Review audiobooks
* Personal collections

Technologies
---
<a href="https://angular.io"><img src="https://angular.io/assets/images/logos/angular/angular.png" alt="Angular Logo" width="100"/></a> <a href="https://www.yiiframework.com/"><img src="https://i.imgur.com/3rXK75a.png" alt="Yii2 Logo" width="100"/></a> <a href="https://www.typescriptlang.org/"><img src="https://i.imgur.com/vr2wa7m.png" alt="TypeScript Logo" width="100"/></a> <a href="https://docker.com"><img src="https://i.imgur.com/650z1vI.png" alt="Docker Logo" width="100"/></a> <a href="https://aws.amazon.com"><img src="https://i.imgur.com/EATjrBz.png" alt="AWS Logo" width="100"/></a> 





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
