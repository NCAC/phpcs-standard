#!/bin/bash -e

ENV_FILE="/workspace/.env"
rm -f "$ENV_FILE"

# define current wsl IP
WSL_IP=$(ip route | grep default | awk '{print $3}')

echo "APP_NAME=ncac-phpcs-standard" >> "$ENV_FILE"
echo "PHP_VERSION=7.4.33" >> "$ENV_FILE"
echo "APP_ROOT=/workspace" >> "$ENV_FILE"
echo "WSL_IP=$WSL_IP" >> "$ENV_FILE"
echo "XDEBUG_PORT=9047" >> "$ENV_FILE"
