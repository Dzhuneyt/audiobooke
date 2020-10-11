#!/usr/bin/env bash

if [[ -z "${DB_HOST}" ]]; then
   #do what you want
   echo DB_HOST env variable is empty. This container can not execute migrations. Check the .env file.
   exit 1
fi

dockerize -wait tcp://${DB_HOST}:3306 -timeout 120s -wait-retry-interval=3s && sleep 3
