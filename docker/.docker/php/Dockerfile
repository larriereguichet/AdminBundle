FROM debian:stretch-slim

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && \
    apt-get --no-install-recommends --no-install-suggests --yes --quiet install \
        apt-transport-https \
        bash-completion \
        ca-certificates \
        curl \
        git \
        gnupg \
        imagemagick \
        less \
        make \
        mysql-client \
        nano \
        perceptualdiff \
        procps \
        ssh-client \
        sudo \
        unzip \
        vim \
        zsh \
        wget && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf  /var/lib/apt/lists/* /tmp/* /var/tmp/* \
            /usr/share/doc/* /usr/share/groff/* /usr/share/info/* /us

RUN wget -O sury.gpg https://packages.sury.org/php/apt.gpg && apt-key add sury.gpg && rm sury.gpg
COPY php-sury.list /etc/apt/sources.list.d/sury.list

# Install PHP with some extensions
RUN apt-get update && \
    apt-get --no-install-recommends --no-install-suggests --yes --quiet install \
        php7.4-cli \
        php7.4-apcu \
        php7.4-mbstring \
        php7.4-curl \
        php7.4-ctype \
        php7.4-gd \
        php7.4-imagick \
        php7.4-intl \
        php7.4-json \
        php7.4-bcmath \
        php7.4-mysql \
        php7.4-xdebug \
        php7.4-xml \
        php7.4-zip \
        php7.4-ldap && \
    apt-get clean && apt-get --yes --quiet autoremove --purge && \
    rm -rf  /var/lib/apt/lists/* /tmp/* /var/tmp/* \
            /usr/share/doc/* /usr/share/groff/* /usr/share/info/* /usr/share/linda/* \
            /usr/share/lintian/* /usr/share/locale/* /usr/share/man/*

COPY symfony.ini /usr/local/etc/php/conf.d/symfony.ini

# Add a "docker" user
RUN useradd docker --shell /bin/bash --create-home \
  && usermod --append --groups sudo docker \
  && echo 'ALL ALL = (ALL) NOPASSWD: ALL' >> /etc/sudoers \
  && echo 'docker:secret' | chpasswd

# Install composer
USER docker
RUN curl -sSL https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
RUN sudo chown docker:docker /usr/local/bin/composer
RUN chmod 777 /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# Install Symfony CLI
WORKDIR /home/docker
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN export PATH="$HOME/.symfony/bin:$PATH"
RUN sudo mv /home/docker/.symfony/bin/symfony /usr/local/bin/symfony
RUN sudo chmod 777 /usr/local/bin/symfony
RUN chown docker:docker /usr/local/bin/symfony

# Install Server Certificates
RUN symfony server:ca:install

RUN mkdir -p /home/docker/www
WORKDIR /home/docker/www
RUN git config --global user.email "you@example.com"
RUN git config --global user.name "Your Name"
RUN symfony new admin_test --full --version=${SYMFONY_VERSION}

# Copy "entrypoint"
COPY ./entrypoint.sh /home/docker/
RUN sudo chown docker:docker /home/docker/entrypoint.sh
RUN chmod a+x /home/docker/entrypoint.sh
