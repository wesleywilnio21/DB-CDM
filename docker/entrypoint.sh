#!/usr/bin/env sh
set -eu

cd /var/www/html

# Allow disabling DB setup for specific environments.
if [ "${SKIP_DB_SETUP:-0}" != "1" ]; then
  echo "Running Laravel migrations and seeders..."

  attempts=0
  max_attempts="${DB_SETUP_MAX_ATTEMPTS:-10}"
  delay_seconds="${DB_SETUP_RETRY_DELAY:-5}"

  until php artisan migrate --force && php artisan db:seed --force; do
    attempts=$((attempts + 1))

    if [ "$attempts" -ge "$max_attempts" ]; then
      echo "Database setup failed after $attempts attempts."
      exit 1
    fi

    echo "Database not ready. Retrying in ${delay_seconds}s... (${attempts}/${max_attempts})"
    sleep "$delay_seconds"
  done
fi

echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
