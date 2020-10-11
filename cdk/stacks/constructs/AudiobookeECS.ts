import {Construct, Duration, StackProps} from "@aws-cdk/core";
import {InstanceType, SubnetType, Vpc} from "@aws-cdk/aws-ec2";
import {Cluster} from "@aws-cdk/aws-ecs";
import {Metric} from "@aws-cdk/aws-cloudwatch";
import {UpdateType} from "@aws-cdk/aws-autoscaling";

interface Props extends StackProps {
    vpc: Vpc,
}

export class AudiobookeECS extends Construct {
    public cluster: Cluster;

    constructor(scope: Construct, id: string, props: Props) {
        super(scope, id);

        // Create an ECS cluster
        this.cluster = new Cluster(this, 'Cluster', {
            vpc: props.vpc,
        });

        // Add capacity to it
        const capacity = this.cluster.addCapacity('DefaultAutoScalingGroupCapacity', {
            updateType: UpdateType.ROLLING_UPDATE,
            spotInstanceDraining: true,
            instanceType: new InstanceType("c5.large"),
            allowAllOutbound: true,
            associatePublicIpAddress: true,
            spotPrice: '0.04',
            minCapacity: process.env.ENV_NAME === 'prod' ? 1 : 1,
            maxCapacity: process.env.ENV_NAME === 'prod' ? 3 : 2,
            vpcSubnets: {
                subnetType: SubnetType.PUBLIC
            },
            // Give instances this much time to drain running tasks when an instance is
            // terminated. This is the default, turn this off by specifying 0 or
            // change the timeout up to 900 seconds.
            taskDrainTime: Duration.seconds(process.env.ENV_NAME === 'prod' ? 60 : 0),
            cooldown: Duration.seconds(process.env.ENV_NAME === 'prod' ? 45 : 1),
        });


        capacity.scaleToTrackMetric('minimumReservation', {
            metric: this.cluster.metricMemoryReservation(),
            targetValue: process.env.ENV_NAME === 'prod' ? 60 : 85,
        });

    }
}
