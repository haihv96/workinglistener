#!/bin/bash

# clear cache
php /var/www/html/artisan cache:clear
php /var/www/html/artisan config:clear
php /var/www/html/artisan view:clear
php /var/www/html/artisan route:clear

# chown for storage project
chown -R www-data:www-data /var/www/html/storage

# supervisor processing management
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
