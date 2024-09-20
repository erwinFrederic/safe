## ersit
## 19092024

FROM php:8.2-apache

# This Gist file accompanies my article on Medium for creating a PHP, MySQL and Redis development environment
#   on macOS. This Dockerfile will create an APACHE, PHP 7.2 server that includes the Xdebug, Igbinary and
#   Redis PHP extensions from PECL. It will also create PHP.ini overrides that will point session management
#   to the Redis server created in this same article.
#
#   The article can be found here:
#   https://medium.com/@crmcmullen/php-how-to-run-your-entire-development-environment-in-docker-containers-on-macos-787784e94f9a

# run non-interactive. Suppresses prompts and just accepts defaults automatically.
ENV DEBIAN_FRONTEND=noninteractive

# update OS and install utils
RUN apt-get update; \
    apt-get -yq upgrade; \
    apt-get install -y --no-install-recommends \
    apt-utils \
    nano; \
    apt-get -yq autoremove; \
    apt-get clean; \
    apt-get install -y git; \
    rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip

# make sure custom log directories exist
RUN mkdir /usr/local/log; \
    mkdir /usr/local/log/apache2; \
    mkdir /usr/local/log/php; \
    chmod -R ug+w /usr/local/log

# create official PHP.ini file
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# install MySQLi
RUN docker-php-ext-install mysqli pdo pdo_mysql zip bcmath

# update PECL and install xdebug, igbinary and redis w/ igbinary enabled
RUN pecl channel-update pecl.php.net; \
    pecl install xdebug-3.2.1; \
    pecl install igbinary-3.2.14; \
    pecl bundle redis-5.3.7 && cd redis && phpize && ./configure --enable-redis-igbinary && make && make install; \
    docker-php-ext-enable xdebug igbinary redis

# Delete the resulting ini files created by the PECL install commands
RUN rm -rf /usr/local/etc/php/conf.d/docker-php-ext-igbinary.ini; \
    rm -rf /usr/local/etc/php/conf.d/docker-php-ext-redis.ini; \
    rm -rf /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Add PHP config file to conf.d
RUN { \
        echo 'short_open_tag = Off'; \
        echo 'expose_php = Off'; \
        echo 'error_reporting = E_ALL & ~E_STRICT'; \
        echo 'display_errors = On'; \
        echo 'error_log = /usr/local/log/php/php_errors.log'; \
        echo 'upload_tmp_dir = /tmp/'; \
        echo 'allow_url_fopen = on'; \
        echo '[xdebug]'; \
        echo 'zend_extension="xdebug.so"'; \
        echo 'xdebug.remote_enable = 1'; \
        echo 'xdebug.remote_port = 9001'; \
        echo 'xdebug.remote_autostart = 1'; \
        echo 'xdebug.remote_connect_back = 0'; \
        echo 'xdebug.remote_host = host.docker.internal'; \
        echo 'xdebug.idekey = VSCODE'; \
        echo '[redis]'; \
        echo 'extension="igbinary.so"'; \
        echo 'extension="redis.so"'; \
        echo 'session.save_handler = "redis"'; \
        echo 'session.save_path = "tcp://redis-localhost:6379?weight=1&timeout=2.5"'; \
    } > /usr/local/etc/php/conf.d/php-config.ini

# Manually set up the apache environment variables
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/usr/local/log/apache2

# Configure apache mods
RUN a2enmod rewrite

# Add ServerName parameter
RUN echo "ServerName localhost" | tee /etc/apache2/conf-available/servername.conf
RUN a2enconf servername

# Update the default apache site with the config we created.
RUN { \
        echo '<VirtualHost *:80>'; \
        echo '    ServerAdmin your_email@example.com'; \
        echo '    DocumentRoot /var/www/html/public'; \
        echo '    <Directory /var/www/html/public>'; \
        echo '        Options Indexes FollowSymLinks MultiViews'; \
        echo '        AllowOverride All'; \
        echo '        Order deny,allow'; \
        echo '        Allow from all'; \
        echo '    </Directory>'; \
        echo '    ErrorLog /usr/local/log/apache2/error.log'; \
        echo '    CustomLog /usr/local/log/apache2/access.log combined' ; \
        echo '</VirtualHost>'; \
    } > /etc/apache2/sites-enabled/000-default.conf

# Install project dependencies
WORKDIR /var/www/html
COPY . /var/www/html
RUN touch tst
RUN dir
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer install
RUN mv .env.example .env
RUN php artisan key:generate; \
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"; \
    php artisan jwt:secret

EXPOSE 80
