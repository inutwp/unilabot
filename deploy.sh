#!/bin/sh

clear

if [[ "$1" == "down" ]]; then
	echo "Down Service...."
	docker-compose down --remove-orphans 2> /dev/nul
	ISSUCCESSDOWN=$?
	if [[ ${ISSUCCESSDOWN} -eq 0 ]]; then
		echo "Success Down Image Container"
	fi
elif [[ "$1" == "up" ]]; then
	echo "Down Service...."
	docker-compose down --remove-orphans
	echo "Build Image.... "
	docker-compose build --no-cache 2> /dev/null
	ISSUCCESSBUILD=$?
	if [[ ${ISSUCCESSBUILD} -gt 0 ]]; then
		echo "Failed Build Image"
	fi
	sleep 1
	echo "Up Service...."
	docker-compose up -d --force-recreate --remove-orphans 2> /dev/null
	ISSUCCESSUP=$?
	if [[ ${ISSUCCESSUP} -gt 0 ]]; then
		echo "Failed Up Image"
	fi
fi
