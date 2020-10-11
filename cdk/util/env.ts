import {Environment} from '@aws-cdk/core';

const accounts = {
    personal: '216987438199',
    company_personal: '347315207830',
};

export default {
    account: accounts.company_personal,
    region: 'us-east-1',
} as Environment;
