#!/bin/bash

cd /var/www/admin/

while [[ true ]]; do
	bash scrap.sh
	sleep 1200
done