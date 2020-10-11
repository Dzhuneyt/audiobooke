#!/usr/bin/env node
import 'source-map-support/register';
import * as cdk from '@aws-cdk/core';
import {Tags} from "@aws-cdk/core";
import {AudiobookeApp} from "./stacks/AudiobookeApp";
import {AudiobookeCI} from './stacks/AudiobookeCI';
import env from './util/env';

// Load stuff from .env
require('dotenv').config();

if (!process.env.ENV_NAME) {
    throw new Error('process.env.ENV_NAME is not defined');
}

try {
    const app = new cdk.App();

    new AudiobookeApp(app, `Audiobooke-app-${process.env.ENV_NAME}`, {
        env,
    });

    new AudiobookeCI(app, 'CI', {
        env,
        stackName: 'Audiobooke-CI',
    });

    Tags.of(app).add('app', 'audiobooke');
    Tags.of(app).add('environment', process.env.ENV_NAME);
} catch (e) {
    console.log(e);
    throw e;
}
