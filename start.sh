#!/usr/bin/env bash

echo "Installing API dependencies"
cd api
composer install
echo "Installing DB service dependencies"
cd ../db_service/
composer install
echo "Spinning up the local stack"
cd ..
docker-compose up -d
echo "Sleeping 20 seconds while rabbitMQ starts"
sleep 20
echo "Executing the db service"
make run_server