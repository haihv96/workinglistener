FROM php:7.4-fpm
RUN apt-get update
RUN apt-get install -y software-properties-common
RUN apt-get install -y zlib1g-dev
RUN apt-get install -y procps
RUN apt-get install -y libfreetype6-dev
RUN apt-get install -y libjpeg62-turbo-dev
RUN apt-get install -y libpng-dev
RUN apt-get install -y ffmpeg
RUN apt-get install -y libzip-dev
#RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install zip pdo_mysql gd exif bcmath
RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Xdebug installation
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

#crontab for schedule tasks
#RUN apt-get -y install cron
#COPY ./cron.d /etc/cron.d

# Supervisord installation
RUN apt-get install -y --no-install-recommends \
    supervisor
RUN pecl install mongodb
RUN docker-php-ext-enable mongodb
RUN pecl install redis \
    && docker-php-ext-enable redis

COPY ./xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./supervisord.conf /etc/supervisor/supervisord.conf
COPY ./supervisord_configs/ /etc/supervisor/conf.d

#php ini upload file
COPY ./php_conf/upload.ini /usr/local/etc/php/conf.d/upload.ini
COPY ./php_conf/memory.ini /usr/local/etc/php/conf.d/memory.ini

#onstartup script commands
COPY ./startup.sh /usr/local/bin/startup.sh
RUN chmod 777 /usr/local/bin/startup.sh
CMD ["/bin/sh", "-c", "/usr/local/bin/startup.sh"]
