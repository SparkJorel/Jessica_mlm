#!/bin/bash
set -e

# Clear and warm up Symfony cache
php bin/console cache:clear --env=prod --no-debug 2>/dev/null || true
chown -R www-data:www-data var/

# Start Apache
exec apache2-foreground
