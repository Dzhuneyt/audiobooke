import {BuildSpec, Cache, ComputeType, LocalCacheMode, PipelineProject} from '@aws-cdk/aws-codebuild';
import {Artifact} from '@aws-cdk/aws-codepipeline';
import {CodeBuildAction, GitHubSourceAction, GitHubTrigger} from '@aws-cdk/aws-codepipeline-actions';
import {PolicyStatement} from '@aws-cdk/aws-iam';
import {Construct, Duration, SecretValue} from '@aws-cdk/core';
import * as codepipeline from '@aws-cdk/aws-codepipeline';

interface Props {
    branchName: string;
}

export class Pipeline extends Construct {
    constructor(scope: Construct, id: string, props: Props) {
        super(scope, id);

        const branchName = props.branchName;

        const pipeline = new codepipeline.Pipeline(this, 'audiobooke-ci-' + branchName, {
            restartExecutionOnUpdate: false,
            pipelineName: `audiobooke-ci-${branchName}`,
        });

        const sourceArtifact = new Artifact('source_code');
        const sourceStage = pipeline.addStage({
            stageName: 'Source',
            actions: [ // optional property
                new GitHubSourceAction({
                    // Generated at https://github.com/settings/tokens with name "Audiobooke CodePipeline CI"
                    oauthToken: SecretValue.plainText("93dfd8e403a8d06bd07c1f28233ccdc1d12438f9"),
                    output: sourceArtifact,
                    owner: 'Dzhuneyt',
                    repo: 'Audiobooke',
                    actionName: "Pull",
                    trigger: GitHubTrigger.WEBHOOK,
                    branch: branchName,
                })
            ],
        });

        const codeBuildProjectForCdkDeploy = new PipelineProject(this, 'deploy-infrastructure', {
            cache: Cache.local(LocalCacheMode.CUSTOM, LocalCacheMode.SOURCE, LocalCacheMode.DOCKER_LAYER),
            timeout: Duration.minutes(30),
            environment: {
                privileged: true,

                // This CodeBuild builds Docker containers and one of them does an Angular build
                // so more RAM is needed
                computeType: ComputeType.MEDIUM,
            },
            buildSpec: BuildSpec.fromObject({
                version: '0.2',
                phases: {
                    build: {
                        commands: [
                            // Install dependencies for CDK
                            'cd ${CODEBUILD_SRC_DIR}/cdk && npm i --no-audit',

                            // Build Lambdas
                            'cd ${CODEBUILD_SRC_DIR}/cdk && npm run webpack',

                            // Deploy CDK stacks
                            'cd ${CODEBUILD_SRC_DIR}/cdk && npm run deploy:app',
                        ]
                    }
                }
            }),
            environmentVariables: {
                ENV_NAME: {
                    value: Pipeline.getEnvNameFromGitBranch(branchName),
                },
                DOMAIN: {
                    value: Pipeline.getDomainForBranch(branchName),
                }
            }
        });
        codeBuildProjectForCdkDeploy.addToRolePolicy(new PolicyStatement({
            sid: "Admin",
            actions: ["*"],
            resources: ["*"],
        }));
        const stageInfrastructure = pipeline.addStage({
            stageName: 'Infrastructure',
            placement: {
                justAfter: sourceStage
            },
            actions: [
                new CodeBuildAction({
                    actionName: 'Deploy',
                    project: codeBuildProjectForCdkDeploy,
                    input: sourceArtifact,
                })
            ],
        });
    }

    private static getEnvNameFromGitBranch(branchName: string) {
        switch (branchName) {
            case 'master':
                return 'prod';
        }
        return branchName;
    }

    private static getDomainForBranch(branchName: string) {
        switch (branchName) {
            case 'master':
                return 'audiobooke.com';
            default:
                return `${branchName}.audiobooke.com`;
        }
    }
}
