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
	COMPOSE="docker-compose -f ${COMPOSE_FILE}"
	echo "Pull Image...."
	${COMPOSE} pull
	echo "Up Service...."
	${COMPOSE} up -d --remove-orphans
	ISSUCCESSUP=$?
	if [[ ${ISSUCCESSUP} -gt 0 ]]; then
		echo "Failed Up Image"
		exit 1
	else
		echo "Clear Redundant"
		docker system prune -f
	fi
fi