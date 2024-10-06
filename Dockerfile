FROM php:8.2-apache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y libzip-dev zip
RUN docker-php-ext-install zip

WORKDIR /var/www/html

COPY . .

COPY .docker/domain-for-sale.conf /etc/apache2/sites-enabled/000-default.conf

RUN chown -R www-data:www-data /var/www/html

USER www-data

ENV APP_ENV=prod

RUN composer install --no-interaction --no-dev --optimize-autoloader

RUN php bin/console asset-map:compile

EXPOSE 80

CMD ["apache2-foreground"]
