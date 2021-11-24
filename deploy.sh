#!/bin/bash

clear

cd ${WORK_DIR}

echo "Checkout to dev branch"
git checkout dev

echo "Fetching branch dev"
git fetch origin dev

echo "Pulling branch dev"
git pull origin dev
IS_SUCCESS_UPDATE_PROJECT=$?
if [[ ${IS_SUCCESS_UPDATE_PROJECT} -gt 0 ]]; then
	echo "Failed Update Local Repo, Check Manually"
	exit 1
fi
echo "Success update branch dev"

if [[ "$1" == "down" ]]; then
	echo "Down Service...."
	docker-compose down --remove-orphans
	ISSUCCESSDOWN=$?
	if [[ ${ISSUCCESSDOWN} -eq 0 ]]; then
		echo "Success Down Image Container"
	fi
elif [[ "$1" == "up" ]]; then
	echo "Pull Image...."
	docker-compose -f docker-compose.dev.yml pull -q
	sleep 1
	echo "Up Service...."
	docker-compose -f docker-compose.dev.yml up -d --remove-orphans
	ISSUCCESSUP=$?
	if [[ ${ISSUCCESSUP} -gt 0 ]]; then
		echo "Failed Up Image"
		exit 1
	fi
fi