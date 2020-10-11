#!/usr/bin/env bash

$(dirname "$0")/wait_for_mysql.sh && php yii migrate --interactive=0
