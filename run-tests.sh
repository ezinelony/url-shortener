#!/usr/bin/env bash

CUR_DIR=`pwd`


function startService
 {
    vt=`nc -z localhost 9000 || echo "not-connected"`
    if [ "$vt" != "not-connected" ];
     then
        echo -e "\033[0;31m Port 9000 is already in use, kill it before running this script\033[0m";
        exit 1;
    fi
   ENV_DATABASE_FILE="${CUR_DIR}/config/data/database.test.db" php -d variables_order=EGPCS -S localhost:9000 -t src src/index.php 1>/dev/null & export SERVICE_PID=$!
 }

echo "Running unit tests ... ================================="

$CUR_DIR/vendor/bin/phpunit --colors ./test

echo "Finished running unit tests ... ================================="

echo "Behat Tests setup ... ================================= "

echo "Running Migration ... "
ENV_DATABASE_FILE="${CUR_DIR}/config/data/database.test.db"  ${CUR_DIR}/config/migrations/migrate.sh

echo "Starting Service for testing... uses it's own database instance";

startService


echo "Finished Behat Tests setup ======================================="

echo "Running integration tests ...From ${CUR_DIR}"

cd ${CUR_DIR}/test/Integration/
ENV_DATABASE_FILE="${CUR_DIR}/config/data/database.test.db" ${CUR_DIR}/vendor/bin/behat

cd $CUR_DIR

echo "killing Service $SERVICE_PID ..."

kill -9 $SERVICE_PID
