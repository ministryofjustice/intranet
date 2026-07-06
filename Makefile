.DEFAULT_GOAL := d-shell

##  █░█  ▄▀█  █▀█  █  ▄▀█  █▄▄  █░░  █▀▀  █▀
##  ▀▄▀  █▀█  █▀▄  █  █▀█  █▄█  █▄▄  ██▄  ▄█
##  populate as needed for testing
##  ... never commit!
ACF_PRO_LICENSE := ***
ACF_PRO_PASS    := ***
AS3CF_PRO_USER  := ***
AS3CF_PRO_PASS  := ***

# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

kube := kind
k8s_prt := 8080:80
k8s_nsp := default
k8s_pod := kubectl -n $(k8s_nsp) get pod -l app=intranet-local -o jsonpath="{.items[0].metadata.name}"

# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░

init: setup key-gen run

d-compose: local-stop
	docker compose up -d nginx phpmyadmin opensearch-dashboard wp-cron php-fpm redis

d-shell: setup key-gen dory d-compose composer

setup:
	@chmod +x ./bin/*
	@[ -f "./.env" ] || cp .env.example .env
		@if grep -q 'ACF_PRO_LICENSE=license_placeholder' .env; then \
		printf "Please set ACF_PRO_LICENSE in the .env file. Then press Enter to continue..."; \
		read _; \
		printf "\n"; \
	fi

restart:
	@docker compose down php-fpm
	@make d-compose


#   █▄░█  █▀█  █▀▄  █▀▀
#   █░▀█  █▄█  █▄▀  ██▄

node-assets:
	npm install
	npm run watch

node-cdn:
	npm install
	npm run watch


#   █▀▀  █▀█  █▀▄▀█  █▀█  █▀█  █▀  █▀▀  █▀█
#   █▄▄  █▄█  █░▀░█  █▀▀  █▄█  ▄█  ██▄  █▀▄

composer-assets:
	@chmod +x ./bin/local-composer-assets.sh
	@docker compose exec php-fpm ./bin/local-composer-assets.sh ash

composer: composer-assets


#   █▀  █░█  █▀▀  █░░  █░░
#   ▄█  █▀█  ██▄  █▄▄  █▄▄
#   𝕆𝕡𝕖𝕟 𝕒 𝕓𝕒𝕤𝕙 𝕤𝕙𝕖𝕝𝕝 𝕠𝕟 𝕥𝕙𝕖 𝕣𝕦𝕟𝕟𝕚𝕟𝕘 𝕡𝕙𝕡 𝕔𝕠𝕟𝕥𝕒𝕚𝕟𝕖𝕣
bash:
	docker compose exec php-fpm bash

bash-nginx:
	docker compose exec --workdir /var/www/html nginx ash

bash-cron:
	docker compose exec wp-cron ash

node:
	docker compose exec --workdir /node node ash


# Remove ignored git files – e.g. composer dependencies and built theme assets
# But keep .env file, .idea directory (PhpStorm config), and uploaded media files
clean:
	@if [ -d ".git" ]; then git clean -xdf --exclude ".env" --exclude ".idea" --exclude "public/app/uploads"; fi
	@clear

# Remove all ignored git files (including media files)
deep-clean:
	@if [ -d ".git" ]; then git clean -xdf --exclude ".idea"; fi

# Remove ALL docker images on the system
docker-clean:
	bin/local-docker-clean.sh


#   ▄▀█  █▀▀  ▀█▀  █  █▀█  █▄░█
#   █▀█  █▄▄  ░█░  █  █▄█  █░▀█
#   ℝ𝕦𝕟 𝕥𝕙𝕖 𝕒𝕡𝕡𝕝𝕚𝕔𝕒𝕥𝕚𝕠𝕟
run: local-stop dory up

up:
	docker compose up

down:
	docker compose down

# Launch the application, open browser, no stdout
launch: local-stop dory
	bin/local-launch.sh

# Start the Dory Proxy
dory:
	@chmod +x ./bin/local-dory-check.sh && ./bin/local-dory-check.sh

# Run tests
test:
	composer test

# Fix tests
test-fixes:
	composer test-fix

# RSA key generation
key-gen:
	@chmod +x ./bin/local-key-gen.sh && ./bin/local-key-gen.sh


# ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░  ░░
#
#   █▀█  █▀█  █▀█  █▀▄  █░█  █▀▀  ▀█▀  █  █▀█  █▄░█     █▀▄▀█  █▀█  █▀▀  █▄▀
#   █▀▀  █▀▄  █▄█  █▄▀  █▄█  █▄▄  ░█░  █  █▄█  █░▀█     █░▀░█  █▄█  █▄▄  █░█
##  𝕂𝟠𝕊 𝕕𝕖𝕡𝕝𝕠𝕪𝕞𝕖𝕟𝕥
#
#  █▄▄  █░█  █  █░░  █▀▄
#  █▄█  █▄█  █  █▄▄  █▄▀

build-nginx:
	@echo "\n-->  Building local Nginx  <---------------------------|\n"; sleep 3;
	docker image build -t intranet-nginx:latest \
		--build-arg AS3CF_PRO_USER="${AS3CF_PRO_USER}" --build-arg AS3CF_PRO_PASS="${AS3CF_PRO_PASS}" \
		--target build-nginx .

# FastCGI Process Manager for PHP
# https://www.php.net/manual/en/install.fpm.php
# https://www.plesk.com/blog/various/php-fpm-the-future-of-php-handling/
build-fpm:
	@echo "\n-->  Building local FPM  <---------------------------|\n"; sleep 3;
	docker image build -t intranet-fpm:latest \
		--build-arg AS3CF_PRO_USER="${AS3CF_PRO_USER}" --build-arg AS3CF_PRO_PASS="${AS3CF_PRO_PASS}" \
		--target build-fpm .

build-cron:
	@echo "\n-->  Building local CRON (runs wp-cron process)  <---------------------------|\n"; sleep 3;
	docker image build -t intranet-cron:latest \
		--target build-cron .

build: build-fpm build-nginx build-cron
	@if [ ${kube} == 'kind' ]; then \
  		kind load docker-image intranet-fpm:latest; \
  		kind load docker-image intranet-nginx:latest; \
  		kind load docker-image intranet-cron:latest; \
  	fi
	@echo "\n-->  Done.\n"


#   █▀▄  █▀▀  █▀█  █░░  █▀█  █▄█
#   █▄▀  ██▄  █▀▀  █▄▄  █▄█  ░█░

kube: local-kube-start clear cluster local-kube-build
	@if [ "${kube}" == 'kind' ]; then \
  		echo "\n-->  Verifying..."; \
  		echo "-->  Use ctrl + C to exit when ready\n"; \
  		kubectl get pods -w; \
  	fi

cluster:
	@if [ "${kube}" != 'kind' ]; then \
  		echo "\n-->  Please, activate the kind cluster to assist in local app development on Kubernetes"; \
		echo "-->  Amend the variable named 'kube' on line 14 in Makefile to read 'kind' (without quotes)"; \
		echo "-->  ... or, install kind from scratch: https://kind.sigs.k8s.io/docs/user/quick-start/#installation \n"; sleep 8; \
	fi
	@if [ "${kube}" == 'kind' ]; then \
  		kind create cluster --config=deploy/config/local/kube/cluster.yml; \
  		kubectl apply -f https://projectcontour.io/quickstart/contour.yaml; \
  	fi
	@if [ "${kube}" == 'kind' ]; then \
  		kubectl patch daemonsets -n projectcontour envoy -p '{"spec":{"template":{"spec":{"nodeSelector":{"ingress-ready":"true"},"tolerations":[{"key":"node-role.kubernetes.io/control-plane","operator":"Equal","effect":"NoSchedule"},{"key":"node-role.kubernetes.io/master","operator":"Equal","effect":"NoSchedule"}]}}}}'; \
  	fi

local-kube-start:
	@if [ -n "$(docker ps | grep dory_dnsmasq)" ]; then dory down; fi # lets make sure port 80 is free
	@docker container start kind-control-plane || kind create cluster

local-stop:
	@echo "\n-->  Checking if we should stop the kind-control-plane container..."
	@docker container stop kind-control-plane || true >/dev/null 2>&1
	@echo "-->  Done.\n"

deploy: clear
	@echo "\n-->  Local Kubernetes deployment  <---------------------------|\n"
	kubectl apply -f deploy/local

local-kube-build: build deploy
	@if [ "${kube}" == 'kind' ]; then \
  		echo "\n-->  Verifying..."; \
  		echo "-->  Use ctrl + C to exit when ready\n"; \
  		kubectl get pods -w; \
  	fi

clear:
	@clear


#   █░░  █▀█  █▀▀     █▀█  █░█  ▀█▀  █▀█  █░█  ▀█▀
#   █▄▄  █▄█  █▄█     █▄█  █▄█  ░█░  █▀▀  █▄█  ░█░

logs: clear logs-fpm-flash logs-nginx-flash
	@echo "\n---------------------------------------------\n"

log-nginx: clear
	@echo "\n-->  NGINX LOGS (tail)  <---------------------------|\n"
	@$(k8s_pod) | xargs -t kubectl logs -f -n $(k8s_nsp) -c nginx

log-fpm: clear
	@echo "\n-->  FPM PHP LOGS (tail)  <-------------------------|\n"
	@$(k8s_pod) | xargs kubectl logs -f -n $(k8s_nsp) -c fpm

logs-nginx-flash:
	@echo "\n-->  NGINX LOGS  <---------------------------|\n"
	@$(k8s_pod) | xargs kubectl logs -n $(k8s_nsp) -c nginx

logs-fpm-flash:
	@echo "\n-->  FPM PHP LOGS  <-------------------------|\n"
	@$(k8s_pod) | xargs kubectl logs -n $(k8s_nsp) -c fpm



#   █▄▀  █░█  █▄▄  █▀▀  █▀█  █▄░█  █▀▀  ▀█▀  █▀▀  █▀
#   █░█  █▄█  █▄█  ██▄  █▀▄  █░▀█  ██▄  ░█░  ██▄  ▄█

port-forward:
	@$(k8s_pod) | echo $$(cat -)" "$(k8s_prt) | xargs kubectl -n $(k8s_nsp) port-forward

unapply:
	@$(k8s_pod) | xargs kubectl -n $(k8s_nsp) delete pod

apply-production:
	kubectl apply -f deploy/production

