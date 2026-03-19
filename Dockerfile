FROM php:8.2-fpm-alpine

# Extensões necessárias
RUN docker-php-ext-install pdo pdo_sqlite

# Nginx
RUN apk add --no-cache nginx

# Configura nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/html

# Copia o site
COPY . .

# Permissões
RUN mkdir -p admin/data images/pascoa2026 \
    && chown -R www-data:www-data admin/data images/pascoa2026 \
    && chmod 755 admin/data images/pascoa2026

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
