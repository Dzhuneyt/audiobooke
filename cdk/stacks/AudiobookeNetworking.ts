import {InstanceType, NatProvider, Vpc} from '@aws-cdk/aws-ec2';
import {App, Construct, NestedStack, NestedStackProps, Stack, StackProps} from "@aws-cdk/core";

export class AudiobookeNetworking extends NestedStack {
    public vpc: Vpc;

    constructor(scope: Construct, id: string, props?: NestedStackProps) {
        super(scope, id, props);

        // Configure the `natGatewayProvider` when defining a Vpc
        const natGatewayProvider = NatProvider.instance({
            instanceType: new InstanceType('t3.micro'),
        });
        this.vpc = new Vpc(this, 'audiobooke', {
            cidr: "10.0.0.0/16",
            natGatewayProvider,
            natGateways: 1,
        });
    }
}
