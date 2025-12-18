#!/bin/bash
set -e

# LibreBooking Docker Entrypoint Script
# Handles container initialization for Railway deployment

echo "=== LibreBooking Container Initialization ==="

# Create necessary directories
echo "Creating directories..."
mkdir -p /var/log/librebooking/log
mkdir -p /var/www/html/uploads/images
mkdir -p /var/www/html/uploads/reservation
mkdir -p /var/www/html/tpl_c

# Set correct permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/log/librebooking
chown -R www-data:www-data /var/www/html/uploads
chown -R www-data:www-data /var/www/html/tpl_c
chmod -R 755 /var/log/librebooking
chmod -R 755 /var/www/html/uploads
chmod -R 755 /var/www/html/tpl_c

# Create config.php from environment variables if it doesn't exist
CONFIG_FILE="/var/www/html/config/config.php"

if [ ! -f "$CONFIG_FILE" ]; then
    echo "Creating configuration from environment variables..."
    
    # Use Railway environment variables or defaults
    DB_HOST="${MYSQL_HOST:-${MYSQLHOST:-localhost}}"
    DB_NAME="${MYSQL_DATABASE:-${MYSQLDATABASE:-librebooking}}"
    DB_USER="${MYSQL_USER:-${MYSQLUSER:-lb_user}}"
    DB_PASS="${MYSQL_PASSWORD:-${MYSQLPASSWORD:-password}}"
    DB_PORT="${MYSQL_PORT:-${MYSQLPORT:-3306}}"
    
    # Script URL from Railway public domain
    if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
        SCRIPT_URL="https://${RAILWAY_PUBLIC_DOMAIN}"
    else
        SCRIPT_URL="${LB_SCRIPT_URL:-}"
    fi
    
    # Default language (Russian for this deployment)
    DEFAULT_LANG="${LB_DEFAULT_LANGUAGE:-ru_ru}"
    
    # Default timezone
    DEFAULT_TZ="${LB_DEFAULT_TIMEZONE:-Europe/Moscow}"
    
    cat > "$CONFIG_FILE" << EOF
<?php
/**
 * LibreBooking Configuration
 * Auto-generated for Railway deployment
 */

return [
    'settings' => [
        'app.title' => '${LB_APP_TITLE:-LibreBooking}',
        'app.debug' => ${LB_DEBUG:-false},
        'admin.email' => '${LB_ADMIN_EMAIL:-admin@example.com}',
        'admin.email.name' => '${LB_ADMIN_EMAIL_NAME:-LB Administrator}',
        'company.name' => '${LB_COMPANY_NAME:-}',
        'company.url' => '${LB_COMPANY_URL:-}',
        
        'default.timezone' => '${DEFAULT_TZ}',
        'default.language' => '${DEFAULT_LANG}',
        
        'script.url' => '${SCRIPT_URL}',
        'install.password' => '${LB_INSTALL_PASSWORD:-}',
        'cache.templates' => true,
        'inactivity.timeout' => ${LB_INACTIVITY_TIMEOUT:-30},
        
        'database' => [
            'type' => 'mysql',
            'hostspec' => '${DB_HOST}',
            'name' => '${DB_NAME}',
            'user' => '${DB_USER}',
            'password' => '${DB_PASS}',
        ],
        
        'logging' => [
            'folder' => '/var/log/librebooking/log',
            'level' => '${LB_LOG_LEVEL:-ERROR}',
            'sql' => false,
        ],
        
        'uploads' => [
            'image.upload.directory' => 'Web/uploads/images',
            'image.upload.url' => 'uploads/images',
            'reservation.attachments.enabled' => ${LB_ATTACHMENTS_ENABLED:-false},
            'reservation.attachment.path' => 'uploads/reservation',
            'reservation.attachment.extensions' => 'txt,jpg,gif,png,doc,docx,pdf,xls,xlsx,ppt,pptx,csv',
        ],
        
        'phpmailer' => [
            'mailer' => '${LB_MAILER:-smtp}',
            'smtp.host' => '${LB_SMTP_HOST:-}',
            'smtp.port' => ${LB_SMTP_PORT:-587},
            'smtp.secure' => '${LB_SMTP_SECURE:-tls}',
            'smtp.auth' => ${LB_SMTP_AUTH:-true},
            'smtp.username' => '${LB_SMTP_USERNAME:-}',
            'smtp.password' => '${LB_SMTP_PASSWORD:-}',
        ],
        
        'email' => [
            'enabled' => ${LB_EMAIL_ENABLED:-true},
            'default.from.address' => '${LB_EMAIL_FROM_ADDRESS:-}',
            'default.from.name' => '${LB_EMAIL_FROM_NAME:-LibreBooking}',
        ],
        
        'registration' => [
            'allow.self.registration' => ${LB_ALLOW_REGISTRATION:-true},
            'captcha.enabled' => ${LB_CAPTCHA_ENABLED:-false},
            'require.email.activation' => ${LB_REQUIRE_EMAIL_ACTIVATION:-false},
        ],
        
        'privacy' => [
            'view.schedules' => ${LB_PUBLIC_SCHEDULES:-true},
            'view.reservations' => ${LB_PUBLIC_RESERVATIONS:-false},
            'allow.guest.reservations' => ${LB_GUEST_RESERVATIONS:-false},
        ],
        
        'api' => [
            'enabled' => ${LB_API_ENABLED:-false},
        ],
    ]
];
EOF
    
    echo "Configuration file created."
fi

# Wait for database to be ready
echo "Checking database connection..."
MAX_RETRIES=30
RETRY_COUNT=0

DB_HOST="${MYSQL_HOST:-${MYSQLHOST:-localhost}}"
DB_PORT="${MYSQL_PORT:-${MYSQLPORT:-3306}}"

while [ $RETRY_COUNT -lt $MAX_RETRIES ]; do
    if php -r "
        \$host = '${DB_HOST}';
        \$port = '${DB_PORT}';
        \$sock = @fsockopen(\$host, \$port, \$errno, \$errstr, 5);
        if (\$sock) { fclose(\$sock); exit(0); }
        exit(1);
    " 2>/dev/null; then
        echo "Database is reachable."
        break
    fi
    
    RETRY_COUNT=$((RETRY_COUNT + 1))
    echo "Waiting for database... (attempt $RETRY_COUNT/$MAX_RETRIES)"
    sleep 2
done

if [ $RETRY_COUNT -eq $MAX_RETRIES ]; then
    echo "WARNING: Could not connect to database after $MAX_RETRIES attempts."
    echo "The application may not function correctly."
fi

echo "=== Initialization Complete ==="
echo "Starting Apache..."

# Execute the main command
exec "$@"
