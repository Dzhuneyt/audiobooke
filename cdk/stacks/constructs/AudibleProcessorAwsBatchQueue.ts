import {Construct} from "@aws-cdk/core";
import {AllocationStrategy, ComputeEnvironment, ComputeResourceType, JobDefinition, JobQueue} from "@aws-cdk/aws-batch";
import {InstanceClass, InstanceSize, InstanceType, SubnetType, Vpc} from "@aws-cdk/aws-ec2";
import {ContainerImage} from "@aws-cdk/aws-ecs";

interface Props {
    vpc: Vpc;
}

export class AudibleProcessorAwsBatchQueue extends Construct {
    public readonly computeEnv: ComputeEnvironment;
    public readonly jobQueue: JobQueue;

    constructor(scope: Construct, id: string, props: Props) {
        super(scope, id);

        this.computeEnv = new ComputeEnvironment(this, 'compute-env', {
            managed: true,
            enabled: true,
            computeResources: {
                type: ComputeResourceType.SPOT,
                allocationStrategy: AllocationStrategy.SPOT_CAPACITY_OPTIMIZED,
                bidPercentage: 100,
                instanceTypes: [
                    new InstanceType("optimal")
                ],
                minvCpus: 0,
                maxvCpus: 5,
                vpc: props.vpc,
                vpcSubnets: {
                    subnetType: SubnetType.PUBLIC,
                },
            }
        });
        this.jobQueue = new JobQueue(this, 'job-queue', {
            computeEnvironments: [
                {
                    computeEnvironment: this.computeEnv,
                    order: 1,
                }
            ]
        });
    }
}