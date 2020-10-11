import {ICertificate} from '@aws-cdk/aws-certificatemanager/lib/certificate';
import {CfnOutput, Construct, Duration, StackProps} from "@aws-cdk/core";
import {Cluster} from "@aws-cdk/aws-ecs";
import {
    ApplicationLoadBalancer,
    ApplicationProtocol,
    ApplicationTargetGroup, ListenerAction,
    ListenerCertificate, Protocol,
    TargetType
} from "@aws-cdk/aws-elasticloadbalancingv2";
import {SubnetType, Vpc} from "@aws-cdk/aws-ec2";
import {Frontend} from "./ecs-tasks/Frontend";
import {Backend} from "./ecs-tasks/Backend";
import {Certificate} from "@aws-cdk/aws-certificatemanager";
import {JobDefinition, JobQueue} from "@aws-cdk/aws-batch";

interface Props extends StackProps {
    cluster: Cluster;
    vpc: Vpc;

    // AWS Batch related
    jobQueue: JobQueue;
}

export class AudiobookeEcsTasks extends Construct {
    private frontend: Frontend;
    private props: Props;
    private backend: Backend;
    private applicationLoadBalancer: ApplicationLoadBalancer;

    constructor(scope: Construct, id: string, props: Props) {
        super(scope, id);
        this.props = props;

        this.frontend = new Frontend(this, 'frontend', {
            cluster: props.cluster,
        });
        this.backend = new Backend(this, 'backend', {
            cluster: props.cluster,
            jobQueue: props.jobQueue,
        });

        this.createLoadBalancer();
    }

    private createLoadBalancer() {
        this.applicationLoadBalancer = new ApplicationLoadBalancer(this, 'LB', {
            internetFacing: true,
            vpc: this.props.vpc,
            vpcSubnets: {
                // Attach the load balancer to public subnets only,
                // because it's internet facing
                subnetType: SubnetType.PUBLIC,
                onePerAz: false,
            },
        });

        new CfnOutput(this, 'alb_url', {
            value: this.applicationLoadBalancer.loadBalancerDnsName
        });

        // Redirect all traffic to frontend container by default
        const targetGroupFrontend = new ApplicationTargetGroup(this, 'tg-frontend', {
            targets: [this.frontend.service],
            port: 4000,
            protocol: ApplicationProtocol.HTTP,
            targetType: TargetType.IP,
            vpc: this.props.vpc,
            deregistrationDelay: Duration.seconds(10),
            healthCheck: {
                port: '4000',
                path: '/',
                interval: Duration.seconds(30),
                protocol: Protocol.HTTP,
            }
        });
        // Redirect all traffic from route "/v1/*" to backend container
        const targetGroupBackend = new ApplicationTargetGroup(this, 'tg-backend', {
            targets: [this.backend.service],
            port: 80,
            protocol: ApplicationProtocol.HTTP,
            targetType: TargetType.IP,
            vpc: this.props.vpc,
            deregistrationDelay: Duration.seconds(10),
            healthCheck: {
                path: '/v1/audiobooks',
                protocol: Protocol.HTTP,
                healthyThresholdCount: 5
            }
        });

        const cert = this.getCertificate();

        if (cert) {
            // If there is a Certificate, listen on HTTPs using this certificate
            // and redirect HTTP traffic to HTTPs
            this.applicationLoadBalancer.addListener('listener-https', {
                protocol: ApplicationProtocol.HTTPS,
                certificates: [
                    ListenerCertificate.fromCertificateManager(cert)
                ],
                defaultTargetGroups: [
                    targetGroupFrontend
                ]
            }).addTargetGroups('backend-tg', {
                pathPatterns: ['/v1/*'],
                targetGroups: [targetGroupBackend],
                priority: 2,
            })

            // Redirect HTTP traffic to HTTPs
            this.applicationLoadBalancer.addListener('listener-http', {
                protocol: ApplicationProtocol.HTTP,
                defaultAction: ListenerAction.redirect({
                    protocol: "HTTPS",
                    port: "433",
                    permanent: true,
                })
            });

        } else {
            // No need for HTTPs listener or certificate on dev environments
            this.applicationLoadBalancer.addListener('listener-http', {
                protocol: ApplicationProtocol.HTTP,
                defaultTargetGroups: [
                    targetGroupFrontend
                ]
            }).addTargetGroups('backend-tg', {
                pathPatterns: ['/v1/*'],
                targetGroups: [targetGroupBackend],
                priority: 2,
            });
        }
    }

    private getCertificate(): ICertificate | undefined {
        switch (process.env.ENV_NAME) {
            case 'prod':
                return Certificate.fromCertificateArn(this, 'certificate', 'arn:aws:acm:us-east-1:216987438199:certificate/503245ec-9692-4eba-952d-fa13218132e6');
            case 'dev':
            default:
                return;
        }
    }

    //
    // private attachLoadBalancerToRoute53() {
    //     new route53.AaaaRecord(this, 'Alias', {
    //         zone: this.props.domain,
    //         target: route53.RecordTarget.fromAlias(new LoadBalancerTarget(this.applicationLoadBalancer))
    //     });
    //     new route53.ARecord(this, 'Alias-A', {
    //         zone: this.props.domain,
    //         target: route53.RecordTarget.fromAlias(new LoadBalancerTarget(this.applicationLoadBalancer))
    //     });
    // }
}
