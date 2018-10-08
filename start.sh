#!/usr/bin/env bash

echo "Installing dependencies"
make deps
echo "Spinning up the stack"
docker-compose up -d
echo "Sleeping 20 seconds while rabbitMQ starts"
sleep 20
echo "Executing the db service"
make run_server