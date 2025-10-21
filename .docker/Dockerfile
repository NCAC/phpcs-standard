ARG PHP_VERSION=7.4.33
#
FROM php:${PHP_VERSION}-cli
#
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
  && pecl install xdebug-3.1.5 \
  && docker-php-ext-enable xdebug \
  # php-xml & zip
  && docker-php-ext-install xml zip \
  # timezone
  && ln -snf /usr/share/zoneinfo/${TIME_ZONE} /etc/localtime && echo ${TIME_ZONE} > /etc/timezone \
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
COPY --from=composer:2.1 /usr/bin/composer /usr/local/bin/composer
#
# Installer Node.js, pnpm & tsx globalement
RUN curl -fsSL https://deb.nodesource.com/setup_23.x | bash - \
  && apt-get install -y nodejs \
  && npm install -g pnpm tsx

ENV PATH="/usr/local/bin:${PATH}"

# start with the user root
USER root

ENV PATH="${APP_ROOT}/vendor/bin:/root/.local/bin:${PATH}"
