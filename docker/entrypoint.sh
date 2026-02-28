#!/bin/bash
set -e

echo "=== Jessica MLM - Entrypoint ==="

# Write .env.local from Docker environment variables
# (Apache doesn't forward container env vars to PHP)
echo "Writing .env.local from Docker environment..."
cat > .env.local << ENVEOF
APP_ENV=${APP_ENV:-prod}
APP_DEBUG=${APP_DEBUG:-0}
APP_SECRET=${APP_SECRET}
DATABASE_URL=${DATABASE_URL}
REDIS_URL=${REDIS_URL}
MAILER_DSN=${MAILER_DSN:-null://null}
API_KEY_DOHONE=${API_KEY_DOHONE}
API_URL_PAY_IN=${API_URL_PAY_IN}
HASH_CODE_DOHONE=${HASH_CODE_DOHONE}
ENVEOF

# Wait for database to be ready
echo "Waiting for database..."
max_tries=30
count=0
until php bin/console doctrine:query:sql "SELECT 1" --env=prod --no-debug > /dev/null 2>&1; do
    count=$((count + 1))
    if [ $count -ge $max_tries ]; then
        echo "ERROR: Database not available after $max_tries attempts"
        break
    fi
    echo "  Attempt $count/$max_tries..."
    sleep 2
done
echo "Database ready."

# Run one-time migration scripts if marker doesn't exist
if [ ! -f var/.migration_v6_done ]; then
    echo "Running Symfony 6.4 migration tasks..."

    # Migrate roles from CSV to JSON (safe: only updates rows NOT already in JSON)
    php bin/console doctrine:query:sql "UPDATE user SET roles = CONCAT('[\"', REPLACE(roles, ',', '\",\"'), '\"]') WHERE roles IS NOT NULL AND roles != '' AND roles NOT LIKE '[%'" --env=prod --no-debug 2>/dev/null || true
    php bin/console doctrine:query:sql "UPDATE user SET roles = '[]' WHERE roles IS NULL OR roles = ''" --env=prod --no-debug 2>/dev/null || true
    echo "  Roles migrated to JSON format."

    # Update schema (safe non-destructive changes only)
    php bin/console doctrine:schema:update --force --no-debug --env=prod 2>/dev/null || true
    echo "  Database schema updated."

    # Mark migration as done
    touch var/.migration_v6_done
    echo "  Migration marker created."
fi

# Run Doctrine migrations if any pending
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod --no-debug 2>/dev/null || true

# Clear and warm up Symfony cache
php bin/console cache:clear --env=prod --no-debug 2>/dev/null || true
php bin/console cache:warmup --env=prod --no-debug 2>/dev/null || true
echo "Cache ready."

# Fix permissions
chown -R www-data:www-data var/

echo "=== Application ready ==="

# Start Apache
exec apache2-foreground
