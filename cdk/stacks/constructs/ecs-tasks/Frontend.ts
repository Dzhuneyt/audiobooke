import {ImportedTargetGroupBase} from '@aws-cdk/aws-elasticloadbalancingv2/lib/shared/imported';
import {Construct} from "@aws-cdk/core";
import {
    AwsLogDriver, Cluster,
    ContainerDefinition,
    ContainerImage,
    Ec2Service,
    Ec2TaskDefinition,
    NetworkMode, PropagatedTagSource
} from "@aws-cdk/aws-ecs";
import * as path from "path";
import {RetentionDays} from "@aws-cdk/aws-logs";
import {SubnetType} from "@aws-cdk/aws-ec2";
import {BaseTask} from './BaseTask';

interface Props {
    cluster: Cluster;
}

export class Frontend extends BaseTask {
    public taskDefinition: Ec2TaskDefinition;
    public container: ContainerDefinition;
    public service: Ec2Service;

    constructor(scope: Construct, id: string, props: Props) {
        super(scope, id);

        this.taskDefinition = new Ec2TaskDefinition(this, 'task-definition', {
            networkMode: NetworkMode.AWS_VPC,
        });

        this.container = this.taskDefinition.addContainer('frontend', {
            image: ContainerImage.fromAsset(path.resolve(__dirname, '../../../..', 'apps/frontend'), {
                file: 'Dockerfile-prod'
            }),
            memoryLimitMiB: 256,
            memoryReservationMiB: 128,
            logging: new AwsLogDriver({
                logRetention: RetentionDays.ONE_WEEK,
                streamPrefix: "frontend",
            })
        });
        this.container.addPortMappings({
            containerPort: 4000,
        });

        this.service = new Ec2Service(this, 'service', {
            serviceName: `frontend`,
            propagateTags: PropagatedTagSource.SERVICE,
            enableECSManagedTags: true,
            cluster: props.cluster,
            taskDefinition: this.taskDefinition,
            minHealthyPercent: process.env.ENV_NAME === 'prod' ? 100 : 0,
            placementStrategies: this.getPlacementStrategies(),
            vpcSubnets: {
                subnetType: SubnetType.PRIVATE,
            }
        });
        if (process.env.ENV_NAME === 'prod') {
            this.service.autoScaleTaskCount({
                maxCapacity: 3,
            }).scaleOnMemoryUtilization('scale', {
                targetUtilizationPercent: 80,
            })
        }
    }
}
