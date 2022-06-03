FROM php:8.1-rc-cli-alpine3.15
COPY . /usr/src/phpapps
ADD ./php.ini /usr/local/etc/php/php.ini
CMD ["/bin/sh"]