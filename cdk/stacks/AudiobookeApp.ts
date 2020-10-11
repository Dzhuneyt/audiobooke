import {App, Stack, StackProps} from "@aws-cdk/core";
import {AudiobookeNetworking} from './AudiobookeNetworking';
import {AudibleProcessorAwsBatchQueue} from "./constructs/AudibleProcessorAwsBatchQueue";
import {AudiobookeECS} from "./constructs/AudiobookeECS";
import {AudiobookeEcsTasks} from "./constructs/AudiobookeEcsTasks";

export class AudiobookeApp extends Stack {
    constructor(scope: App, id: string, props: StackProps) {
        super(scope, id, props);

        const networking = new AudiobookeNetworking(this, `networking`);

        const awsBatch = new AudibleProcessorAwsBatchQueue(this, 'queue', {
            vpc: networking.vpc
        });

        const ecsCluster = new AudiobookeECS(this, 'ecs', {
            vpc: networking.vpc
        });
        new AudiobookeEcsTasks(this, 'ecs-tasks', {
            cluster: ecsCluster.cluster,
            vpc: networking.vpc,
            jobQueue: awsBatch.jobQueue,
        });
    }
}
