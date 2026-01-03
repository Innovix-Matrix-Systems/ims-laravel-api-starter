#!/bin/sh

# Wait for database script with better error handling
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo "${BLUE}🔍 Checking database connectivity...${NC}"

# Database connection parameters
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-laravel}
DB_USERNAME=${DB_USERNAME:-laravel}
DB_PASSWORD=${DB_PASSWORD:-secret}

# Maximum wait time (60 seconds)
MAX_ATTEMPTS=30
ATTEMPT=0

# Function to test database connection
test_db_connection() {
    if command -v mysql >/dev/null 2>&1; then
        # Use mysql client if available
        mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" "$DB_DATABASE" >/dev/null 2>&1
    else
        # Use PHP PDO as fallback
        php -r "
            try {
                \$pdo = new PDO('mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_DATABASE', '$DB_USERNAME', '$DB_PASSWORD');
                \$pdo->query('SELECT 1');
                exit(0);
            } catch (Exception \$e) {
                exit(1);
            }
        " >/dev/null 2>&1
    fi
}

# Wait for database to be ready
while ! test_db_connection; do
    ATTEMPT=$((ATTEMPT + 1))
    
    if [ $ATTEMPT -ge $MAX_ATTEMPTS ]; then
        echo "${RED}❌ Database connection failed after $MAX_ATTEMPTS attempts (60 seconds)${NC}"
        echo "${RED}Configuration:${NC}"
        echo "  Host: $DB_HOST"
        echo "  Port: $DB_PORT"
        echo "  Database: $DB_DATABASE"
        echo "  Username: $DB_USERNAME"
        echo ""
        echo "${YELLOW}💡 Troubleshooting tips:${NC}"
        echo "1. Ensure the database container is running: docker-compose ps"
        echo "2. Check database logs: docker-compose logs db"
        echo "3. Verify environment variables in .env file"
        echo "4. Ensure database service is healthy: docker-compose ps"
        exit 1
    fi
    
    echo "${YELLOW}🔄 Database not ready, waiting 2 seconds... (attempt $ATTEMPT/$MAX_ATTEMPTS)${NC}"
    sleep 2
done

echo "${GREEN}✅ Database connection successful!${NC}"
