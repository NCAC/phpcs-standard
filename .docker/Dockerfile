ARG PHP_VERSION=8.2.35
ARG COMPOSER_VERSION=2.10.3
#
FROM composer:${COMPOSER_VERSION} AS composer
#
FROM php:${PHP_VERSION}-cli-bookworm
#
ARG XDEBUG_VERSION=3.5.3
ARG APP_ROOT
ARG XDEBUG_PORT
ARG WSL_IP
#
ENV COMPOSER_ALLOW_SUPERUSER=1 \
  DEBIAN_FRONTEND=noninteractive \
  LANG=fr_FR.UTF-8 \
  TZ=Europe/Paris \
  APP_ROOT=$APP_ROOT \
  XDEBUG_PORT=$XDEBUG_PORT \
  WSL_IP=$WSL_IP
#
#
RUN apt-get update \
  && apt-get upgrade -y \
  && apt-get install -y \
  python3-requests \
  libgcc1 \
  libgssapi-krb5-2 \
  libstdc++6 \
  procps \
  libcurl4 \
  wget \
  libicu-dev \
  vim nano \
  libxml2-dev \
  git \
  zlib1g-dev libzip-dev zip unzip \
  libssl-dev libssl-doc libsasl2-dev \
  locales \
  libmcrypt-dev \
  libonig-dev \
  locate \
  lsb-release \
  dnsutils iproute2 inetutils-ping netcat-traditional \
  jq \
  rsync \
  yamllint \
  # Xdebug
  && pecl install xdebug-${XDEBUG_VERSION} \
  && docker-php-ext-enable xdebug \
  # php-xml & zip
  && docker-php-ext-install xml zip \
  # timezone
  && ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone \
  # Clean up
  && apt-get autoremove -y && apt-get clean -y && rm -rf /var/lib/apt/lists/*
#
# Xdebug CLI config pour debug VSCode
# Copie le template de config Xdebug CLI
COPY .docker/php-config/xdebug-cli.ini.temp /tmp/xdebug-cli.ini.temp
#
# Install gettext-base to use envsubst
RUN apt-get update && apt-get install -y --no-install-recommends gettext
# Génère la config Xdebug CLI avec les variables d'environnement
RUN envsubst < /tmp/xdebug-cli.ini.temp > /usr/local/etc/php/conf.d/99-xdebug-cli.ini
#
#
# Install composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
#
# Install Node.js 24.22.1 and npm (direct binary download to avoid NodeSource GPG issues)
RUN apt-get update \
  && apt-get install -y curl xz-utils ca-certificates \
  && curl -fsSL https://nodejs.org/dist/v24.22.1/node-v24.22.1-linux-x64.tar.xz -o node.tar.xz \
  && echo "6c172a9ab6718f7b801fb5a7bd486af2ee5e32758086a04b84fcce2aa77095e6  node.tar.xz" | sha256sum -c - \
  && tar -xJf node.tar.xz -C /usr/local --strip-components=1 \
  && rm node.tar.xz \
  && npm install -g pnpm@10.34.5 tsx \
  && apt-get autoremove -y && apt-get clean -y && rm -rf /var/lib/apt/lists/*

ENV PATH="/usr/local/bin:${PATH}"

# start with the user root
USER root

# Install CodeGraph CLI (analyse de code statique pour PHP, JS, etc.)
RUN curl -fsSL https://raw.githubusercontent.com/colbymchenry/codegraph/main/install.sh | sh

ENV PATH="${APP_ROOT}/vendor/bin:/root/.local/bin:${PATH}"
