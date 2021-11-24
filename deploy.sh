#!/bin/bash

clear

if [[ "$1" == "down" ]]; then
	echo "Down Service...."
	docker-compose down --remove-orphans
	ISSUCCESSDOWN=$?
	if [[ ${ISSUCCESSDOWN} -eq 0 ]]; then
		echo "Success Down Image Container"
	fi
elif [[ "$1" == "up" ]]; then
	echo "Pull Image...."
	docker-compose -f ${COMPOSE_FILE} pull
	sleep 1
	echo "Up Service...."
	docker-compose -f ${COMPOSE_FILE} --remove-orphans
	ISSUCCESSUP=$?
	if [[ ${ISSUCCESSUP} -gt 0 ]]; then
		echo "Failed Up Image"
		exit 1
	fi
fi