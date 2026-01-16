
# Bu script sadece test veritabanını oluşturur
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE testing;
    GRANT ALL PRIVILEGES ON DATABASE testing TO $POSTGRES_USER;
EOSQL

echo "Test db created!"
