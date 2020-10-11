import {JobDefinition, JobQueue} from "@aws-cdk/aws-batch";
import {SubnetType} from "@aws-cdk/aws-ec2";
import {
    AwsLogDriver,
    Cluster,
    ContainerDefinition,
    ContainerImage,
    Ec2Service,
    Ec2TaskDefinition,
    NetworkMode,
    PropagatedTagSource, Secret
} from "@aws-cdk/aws-ecs";
import {PolicyStatement} from "@aws-cdk/aws-iam";
import {RetentionDays} from "@aws-cdk/aws-logs";
import * as sm from "@aws-cdk/aws-secretsmanager";
import {Queue} from '@aws-cdk/aws-sqs';
import {Construct, Duration, Stack} from "@aws-cdk/core";
import * as path from "path";
import {BaseTask} from './BaseTask';
import sqs = require('@aws-cdk/aws-sqs');

interface Props {
    cluster: Cluster;

    // AWS Batch related
    jobQueue: JobQueue;
}

export class Backend extends BaseTask {
    public taskDefinition: Ec2TaskDefinition;
    public container: ContainerDefinition;
    public service: Ec2Service;
    private readonly secrets: { [key: string]: Secret };
    private queueSearches: Queue;
    private props: Props;

    constructor(scope: Construct, id: string, props: Props) {
        super(scope, id);

        this.props = props;

        this.queueSearches = new sqs.Queue(this, 'searches');

        this.secrets = this.getContainerSecrets();

        this.createTaskDefinition();
        this.createBackendContainer();
        this.createMigrationsContainer();
        this.createService();
    }

    private getContainerSecrets(): {
        [key: string]: Secret;
    } {
        const envs: {
            [key: string]: Secret;
        } = {};

        const secret = new sm.Secret(this, 'db-creds', {
            secretName: `/audiobooke/${process.env.ENV_NAME}`,
            generateSecretString: {
                secretStringTemplate: JSON.stringify({
                    DB_HOST: 'TO_BE_DEFINED',
                    DB_USER: 'syscdk',
                    DB_NAME: process.env.ENV_NAME === 'prod' ? 'audiobooke' : 'audiobooke_' + process.env.ENV_NAME,
                }),
                generateStringKey: 'DB_PASS'
            }
        });
        [
            'DB_HOST',
            'DB_USER',
            'DB_PASS',
            'DB_NAME',
        ].forEach(name => {
            envs[name] = Secret.fromSecretsManager(secret, name);
        });

        return envs;
    }

    private createTaskDefinition() {
        this.taskDefinition = new Ec2TaskDefinition(this, 'task-definition', {
            networkMode: NetworkMode.AWS_VPC,
        });
        this.taskDefinition.addToTaskRolePolicy(new PolicyStatement({
            actions: [
                "batch:SubmitJob"
            ],
            resources: [
                this.props.jobQueue.jobQueueArn,
            ]
        }));

        // Allow the backend to write to the SQS queue
        this.taskDefinition.addToTaskRolePolicy(new PolicyStatement({
            actions: ["sqs:SendMessage"],
            resources: [this.queueSearches.queueArn],
        }));
    }

    private createBackendContainer() {
        this.container = this.taskDefinition.addContainer('backend', {
            image: ContainerImage.fromAsset(path.resolve(__dirname, '../../../..', 'apps/backend'), {
                file: 'Dockerfile'
            }),
            memoryLimitMiB: 256,
            memoryReservationMiB: 128,
            logging: new AwsLogDriver({
                logRetention: RetentionDays.ONE_WEEK,
                streamPrefix: "backend",
            }),
            secrets: {
                ...this.secrets,
            },
            environment: {
                YII_ENV: 'dev',

                // Google SSO login
                GOOGLE_CLIENT_ID: '927616647800-606qkruh4m6qbbej7juen35rf2nlq1lm.apps.googleusercontent.com',
                GOOGLE_CLIENT_SECRET: 'qs1qdYYnJj9h5XBw5zF9Yk9Q',
                GOOGLE_SSO_REDIRECT_URL: process.env.GOOGLE_SSO_REDIRECT_URL as string,

                // AWS Batch related
                JOB_QUEUE_ARN: this.props.jobQueue.jobQueueArn,
                JOB_REGION: Stack.of(this).region,

                // SQS related
                QUEUE_URL: this.queueSearches.queueUrl,
                QUEUE_REGION: Stack.of(this).region,
            },
        });
        this.container.addPortMappings({
            containerPort: 80,
        });
    }

    private createMigrationsContainer() {
        // Backend migrations container
        // It will wait for MySQL to be available, execute migrations and die
        // Since it's marked as "essential=false", ECS will not kill the entire Service
        // and the standard backend container will keep running
        this.taskDefinition.addContainer('migrations', {
            image: ContainerImage.fromAsset(path.resolve(__dirname, '../../../..', 'apps/backend'), {
                file: 'Dockerfile'
            }),
            command: ["scripts/migrate.sh"],
            essential: false,
            memoryLimitMiB: 256,
            memoryReservationMiB: 128,
            logging: new AwsLogDriver({
                logRetention: process.env.ENV_NAME === 'prod' ? RetentionDays.THREE_MONTHS : RetentionDays.ONE_WEEK,
                streamPrefix: "migrations",
            }),
            secrets: {
                ...this.secrets,
            },
        });
    }

    private createService() {
        this.service = new Ec2Service(this, 'service', {
            serviceName: `backend`,
            propagateTags: PropagatedTagSource.SERVICE,
            enableECSManagedTags: true,
            cluster: this.props.cluster,
            taskDefinition: this.taskDefinition,
            desiredCount: process.env.ENV_NAME === 'prod' ? 2 : 1,
            // Leave at least 1 instance during new version deployment
            minHealthyPercent: process.env.ENV_NAME === 'prod' ? 50 : 0,
            placementStrategies: this.getPlacementStrategies(),
            vpcSubnets: {
                subnetType: SubnetType.PRIVATE,
            }
        });

        if (process.env.ENV_NAME === 'prod') {
            this.service.autoScaleTaskCount({
                maxCapacity: 3,
            }).scaleOnMemoryUtilization('memory', {
                targetUtilizationPercent: 70,
                scaleOutCooldown: Duration.seconds(60),
                scaleInCooldown: Duration.seconds(60),
            });
        }
    }
}
