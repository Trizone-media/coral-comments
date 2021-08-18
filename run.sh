#!/bin/bash 
# Create directories to persist the data in MongoDB and Redis.
mkdir -p data/{mongo,redis} 
mkdir -p mongo-backup

# Create a secret using a tool like openssl.
export SIGNING_SECRET="$(openssl rand -base64 48)"

docker-compose up -d
