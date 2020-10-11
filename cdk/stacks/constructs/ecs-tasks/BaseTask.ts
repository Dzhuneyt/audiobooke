import {PlacementStrategy} from '@aws-cdk/aws-ecs';
import {Construct} from '@aws-cdk/core';

export class BaseTask extends Construct {
    protected getPlacementStrategies() {
        const strategyForLoadBalancing = [
            // Prefer spreading across more instances
            PlacementStrategy.spreadAcrossInstances(),
            PlacementStrategy.randomly(),
        ];
        const strategyForCostOptimization = [
            // Prefer clustering containers into fewer instances
            PlacementStrategy.packedByMemory(),
            PlacementStrategy.spreadAcrossInstances(),
        ];

        if (process.env.ENV_NAME === 'prod') {
            return strategyForLoadBalancing;
        }
        return strategyForCostOptimization;
    }
}
