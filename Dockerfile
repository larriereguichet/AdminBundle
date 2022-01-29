FROM php:8.0

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -; \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list;

RUN apt-get update; \
    apt-get install -y \
        git \
        zlib1g-dev \
        libzip-dev \
        libpng-dev \
        nodejs \
        zsh \
        wget \
        yarn; \
    apt --yes --quiet autoremove --purge; \
    rm -rf  /var/lib/apt/lists/* /tmp/* /var/tmp/* \
                /usr/share/doc/* /usr/share/groff/* /usr/share/info/* /usr/share/linda/* \
                /usr/share/lintian/* /usr/share/locale/* /usr/share/man/*

RUN docker-php-ext-install zip; \
    docker-php-ext-install pdo_mysql;

RUN docker-php-ext-configure zip

COPY .docker/php/composer_install.sh .
RUN chmod +x ./composer_install.sh; \
    ./composer_install.sh; \
    mv composer.phar /usr/local/bin/composer; \
    rm composer_install.sh

RUN wget https://get.symfony.com/cli/installer -O - | bash; \
    mv /root/.symfony/bin/symfony /usr/local/bin/symfony; \
    chmod +x /usr/local/bin/symfony;

WORKDIR /srv/app

RUN git config --global user.email "test@example.com"
RUN symfony new /srv/app --debug --webapp

RUN symfony server:ca:install

VOLUME /srv/app
