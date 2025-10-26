#!/usr/bin/env bash
set -e

echo "🕐 Waiting for PostgreSQL to accept connections..."
until pg_isready -U "$POSTGRES_USER" >/dev/null 2>&1; do
  sleep 1
done

echo "✅ PostgreSQL is ready. Creating databases if they do not exist..."

for db in creotify creotify_test; do
  db_exists=$(psql -U "$POSTGRES_USER" -tAc "SELECT 1 FROM pg_database WHERE datname='${db}'")
  if [ "$db_exists" != "1" ]; then
    echo "📦 Creating database '${db}'..."
    createdb -U "$POSTGRES_USER" -O "$POSTGRES_USER" "$db"
  else
    echo "✔️ Database '${db}' already exists, skipping."
  fi
done

echo "🎉 Database initialization completed successfully."
