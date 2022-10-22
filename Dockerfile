FROM php:8.1

RUN apt-get update; \
    apt-get install -y \
        apt-transport-https \
        git \
        gnupg \
        libicu-dev \
        libpng-dev \
        libzip-dev \
        nodejs \
        unzip \
        zip \
        zlib1g-dev \
        zsh \
        wget \
        yarn; \
    apt --yes --quiet autoremove --purge; \
    rm -rf  /var/lib/apt/lists/* /tmp/* /var/tmp/* \
                /usr/share/doc/* /usr/share/groff/* /usr/share/info/* /usr/share/linda/* \
                /usr/share/lintian/* /usr/share/locale/* /usr/share/man/*

RUN docker-php-ext-install \
    intl \
    bcmath \
    pdo \
    pdo_mysql \
    zip

RUN docker-php-ext-enable \
    intl \
    bcmath \
    pdo \
    pdo_mysql \
    zip

RUN pecl install pcov && docker-php-ext-enable pcov

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash; \
    apt update; \
    apt install symfony-cli

WORKDIR /srv/bundle

COPY . .

RUN git config --global user.email "test@example.com"
RUN symfony server:ca:install

RUN composer self-update
RUN composer install

VOLUME /srv/app
