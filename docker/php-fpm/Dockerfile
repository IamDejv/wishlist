FROM php:8.0-fpm


RUN apt-get update && apt-get upgrade -yq && apt-get install -yq \
	libgd-dev \
	libssl-dev \
	libcurl4-openssl-dev \
	libjpeg62-turbo-dev \
	libmcrypt-dev \
	mariadb-server \
	&& apt-get clean all \
	&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ \
		--with-jpeg=/usr/include/ \
		--enable-gd
RUN docker-php-ext-install pdo pdo_mysql

COPY ./config/php /usr/local/etc/php/conf.d
COPY ./config/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.conf
RUN mkdir -p /var/log/php/

VOLUME ["/app"]
WORKDIR /

