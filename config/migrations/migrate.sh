#!/bin/bash


DB_FILE="${ENV_DATABASE_FILE:-./config/data/database.db}"

echo "Creating Relations... on ${DB_FILE}"

sqlite3 "${DB_FILE}"  <  ./config/migrations/schema.sql

echo "Done Bye..."

#Run ENV_DATABASE_FILE="./config/data/database.test.db"  ./config/migrations/migrate.sh
#forintegrated test