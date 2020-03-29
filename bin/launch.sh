#!/usr/bin/env bash

if [[ ! -f ".env" ]]; then
    cp .env.example .env
fi

docker-compose up -d

sleep 2
python -m webbrowser http://$(grep SERVER_NAME .env | xargs | cut -d "=" -f 2)
