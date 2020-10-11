import {Construct, Duration, SecretValue, Stack, StackProps} from '@aws-cdk/core';
import {Pipeline} from './constructs/ci/Pipeline';

interface CiProps extends StackProps {

}

export class AudiobookeCI extends Stack {
    constructor(scope: Construct, id: string, props: CiProps) {
        super(scope, id, props);

        [
            "master",
            "develop"
        ].forEach(branchName => {
            this.createCiForBranch(branchName);
        });

    }

    private createCiForBranch(branchName: string) {
        new Pipeline(this, branchName, {
            branchName: branchName,
        })
    }
}
