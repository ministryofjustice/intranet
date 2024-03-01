#!/usr/bin/env bash

read -pr "v = Clean volumes. vr = Clean volumes, rebuild and run. n =  Nuke all local Docker images [v/vr/n] " -e CAN_CLEAN

if [[ "$CAN_CLEAN" = "v" ]]; then

	echo "Removing volumes..."
	# sleep to attempt to dodge race condition
	sleep 2
	docker-compose down -v

elif [[ "$CAN_CLEAN" =~ "vr" ]]; then

	echo "Removing volumes and restarting..."
	sleep 2
	docker-compose down -v
	dory up
	docker-compose up --build

elif [[ "$CAN_CLEAN" =~ "n" ]]; then

	echo "Nuking all local images, stopping first..."
	docker container stop "$(docker container ls -aq)"
	echo "Removing..."
	docker rmi "$(docker images -q)" --force

fi

exit 0;
