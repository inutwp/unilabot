#!/bin/bash

clear

if [[ "$1" == "down" ]]; then
	COMPOSE="docker-compose -f ${COMPOSE_FILE}"
	echo "Down Service...."
	${COMPOSE} down --remove-orphans
	IS_SUCCESS_DOWN=$?
	if [[ ${IS_SUCCESS_DOWN} -eq 0 ]]; then
		echo "Success Down Image Container"
	fi
elif [[ "$1" == "up" ]]; then
	COMPOSE="docker-compose -f ${COMPOSE_FILE}"
	echo "Pull Image...."
	${COMPOSE} pull
	echo "Up Service...."
	${COMPOSE} up -d --remove-orphans
	IS_SUCCESS_UP=$?
	if [[ ${IS_SUCCESS_UP} -gt 0 ]]; then
		echo "Failed Up Image"
		exit 1
	else
		echo "Clear Redundant"
		docker system prune -f
	fi
fi