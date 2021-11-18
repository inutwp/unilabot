#!/bin/bash

cd /var/www/admin/

actionScrap() {
	curl \
	-X "GET" \
	-H "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9" \
	-H "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.69 Safari/537.36" \
	-o homepage.txt https://www.unila.ac.id \
	--connect-timeout 30 \
	--max-time 45 \
	--silent

	RESULT=$?
	echo ${RESULT}
}

actionGetAnnouncements() {
	php checkannouncements.php
}

CHECKSCRAP="$(actionScrap)" 
if [[ ${CHECKSCRAP} -gt 0 ]]; then
	RETRY=0
	until [[ ${RETRY} -ge 3 ]]
	do
		RESULT="$(actionScrap)"
		if [[ ${RESULT} -gt 0 ]]; then
			echo "Retry Scraping ${RETRY}"
			actionScrap
			(( RETRY++ ))
		else
			actionGetAnnouncements
			break
		fi
	done
else
	actionGetAnnouncements
fi
