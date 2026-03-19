#!/bin/sh
set -e

# Garante permissões corretas (pode ter sido montado como volume)
mkdir -p /var/www/html/admin/data /var/www/html/images/pascoa2026
chown -R www-data:www-data /var/www/html/admin/data /var/www/html/images/pascoa2026
chmod 755 /var/www/html/admin/data /var/www/html/images/pascoa2026

# Popula o banco se ainda estiver vazio
if [ ! -f /var/www/html/admin/data/upup.db ]; then
    echo "[entrypoint] Banco não encontrado — rodando seed..."
    python3 /var/www/html/admin/seed_pascoa2026.py || true
    chown www-data:www-data /var/www/html/admin/data/upup.db 2>/dev/null || true
fi

# Inicia PHP-FPM em background
php-fpm -D

# Inicia nginx em foreground
exec nginx -g "daemon off;"
