FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    nginx nodejs npm git unzip libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql zip

WORKDIR /var/www/html
COPY . .

RUN npm install && npm run build
RUN composer install --no-dev --optimize-autoloader

COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]