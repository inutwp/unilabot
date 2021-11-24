#!/bin/bash

clear

cd ${WORK_DIR}

update_project()
{
	git checkout dev
	git fetch origin dev
	git pull origin dev
}

update_project
IS_SUCCESS_UPDATE_PROJECT=$?
if [[ ${IS_SUCCESS_UPDATE_PROJECT} -gt 0 ]]; then
	echo "Failed Update Local Repo, Check Manually"
	exit 1
fi

if [[ "$1" == "down" ]]; then
	echo "Down Service...."
	docker-compose down --remove-orphans
	ISSUCCESSDOWN=$?
	if [[ ${ISSUCCESSDOWN} -eq 0 ]]; then
		echo "Success Down Image Container"
	fi
elif [[ "$1" == "up" ]]; then
	# echo "Down Service...."
	# docker-compose down --remove-orphans 2> /dev/null
	echo "Pull Image"
	docker-compose -f docker-compose.dev.yml pull -q
	# echo "Build Image.... "
	# docker-compose build --no-cache
	# ISSUCCESSBUILD=$?
	# if [[ ${ISSUCCESSBUILD} -gt 0 ]]; then
	# 	echo "Failed Build Image"
	# 	exit
	# fi
	sleep 1
	echo "Up Service...."
	docker-compose -f docker-compose.dev.yml up -d --remove-orphans
	ISSUCCESSUP=$?
	if [[ ${ISSUCCESSUP} -gt 0 ]]; then
		echo "Failed Up Image"
		exit 1
	fi
fi