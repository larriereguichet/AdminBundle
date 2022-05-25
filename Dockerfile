FROM php:8.1

RUN apt-get update; \
    apt-get install -y \
        git \
        zlib1g-dev \
        libzip-dev \
        libpng-dev \
        nodejs \
        unzip \
        zip \
        zsh \
        wget \
        yarn; \
    apt --yes --quiet autoremove --purge; \
    rm -rf  /var/lib/apt/lists/* /tmp/* /var/tmp/* \
                /usr/share/doc/* /usr/share/groff/* /usr/share/info/* /usr/share/linda/* \
                /usr/share/lintian/* /usr/share/locale/* /usr/share/man/*

RUN docker-php-ext-install zip pdo pdo_mysql;

RUN docker-php-ext-configure zip pdo pdo_mysql; \
    docker-php-ext-enable zip pdo pdo_mysql

RUN pecl install pcov && docker-php-ext-enable pcov

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

RUN wget https://get.symfony.com/cli/installer -O - | bash; \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony; \
    chmod +x /usr/local/bin/symfony;

WORKDIR /srv/app

COPY . .

RUN git config --global user.email "test@example.com"
RUN symfony server:ca:install

RUN composer self-update
RUN composer install

VOLUME /srv/app
