#!/bin/bash
docker compose build #--no-cache
docker compose down
docker compose up -d
