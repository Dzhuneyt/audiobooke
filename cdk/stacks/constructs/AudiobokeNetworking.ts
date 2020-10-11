import {Construct, Stack, StackProps} from "@aws-cdk/core";
import {InstanceType, NatProvider, Vpc} from "@aws-cdk/aws-ec2";

export class AudiobokeNetworking extends Construct {
    public vpc: Vpc;

    constructor(scope: Construct, id: string) {
        super(scope, id);

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