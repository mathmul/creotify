#!/usr/bin/env sh
set -e

APP_DIR=/var/www/html
COMPOSER_HOME=/home/www-data/.composer

export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_PROCESS_TIMEOUT=1200
export COMPOSER_HOME
mkdir -p "$COMPOSER_HOME"

cd "$APP_DIR"

# ---------- Writable dirs ----------
mkdir -p var/cache var/log
chown -R www-data:www-data var "$COMPOSER_HOME"
chmod -R 775 var || true

# ---------- Safe composer install ----------
composer_safe_install() {
  local tries=0
  until [ $tries -ge 3 ]
  do
    if su-exec www-data:www-data composer install --prefer-dist --no-interaction --no-progress; then
      return 0
    fi
    echo "Composer install failed; clearing cache and retrying ($((tries+1))/3)…"
    su-exec www-data:www-data composer clear-cache || true
    sleep 2
    tries=$((tries+1))
  done
  return 1
}

if [ ! -f vendor/autoload.php ]; then
  echo "Installing Composer dependencies…"
  composer_safe_install || { echo "Composer install failed after retries"; exit 1; }
fi

# ---------- Database readiness (optional wait) ----------
if [ -n "$DATABASE_URL" ]; then
  echo "Waiting for database to be ready..."
  php -r "
    \$timeout = 30;
    \$start = time();
    do {
        try {
            new PDO(getenv('DATABASE_URL'));
            exit(0);
        } catch (Exception \$e) {
            sleep(1);
        }
    } while (time() - \$start < \$timeout);
    exit(1);
  " || echo 'DB still unavailable, continuing anyway...'
fi

# ---------- Cache & logs ----------
su-exec www-data:www-data bin/console cache:clear || true
su-exec www-data:www-data bin/console cache:warmup || true

# ---------- Permissions fix ----------
chown -R www-data:www-data var vendor

# ---------- Start PHP-FPM ----------
exec "$@"
