<?php

/**
 * Railway environment variable mappings to LibreBooking variables.
 * Maps Railway-provided MySQL variables to LibreBooking's expected format.
 * Also supports Railway's alternative variable names (without underscore).
 */
define('RAILWAY_ENV_MAPPINGS', [
    // Database mappings - Railway provides these when MySQL is provisioned
    'LB_DATABASE_HOSTSPEC' => ['MYSQL_HOST', 'MYSQLHOST'],
    'LB_DATABASE_NAME' => ['MYSQL_DATABASE', 'MYSQLDATABASE'],
    'LB_DATABASE_USER' => ['MYSQL_USER', 'MYSQLUSER'],
    'LB_DATABASE_PASSWORD' => ['MYSQL_PASSWORD', 'MYSQLPASSWORD'],
    // Script URL mapping - Railway provides public domain
    'LB_SCRIPT_URL' => ['RAILWAY_PUBLIC_DOMAIN'],
]);

if (!function_exists('env')) {
    /**
     * Get the value of an environment variable, or return default.
     * Supports Railway environment variable mappings for MySQL and public domain.
     * 
     * Priority order:
     * 1. Direct LB_ environment variable (explicit override)
     * 2. Railway environment variable mapping (MYSQL_*, RAILWAY_*)
     * 3. Default value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        // First, check if the key is set directly (allows explicit overrides)
        $directValue = getEnvValue($key);
        if ($directValue !== null) {
            return $directValue;
        }

        // Second, check Railway mappings for this key
        if (isset(RAILWAY_ENV_MAPPINGS[$key])) {
            foreach (RAILWAY_ENV_MAPPINGS[$key] as $railwayKey) {
                $railwayValue = getEnvValue($railwayKey);
                if ($railwayValue !== null) {
                    // Special handling for RAILWAY_PUBLIC_DOMAIN - prepend https://
                    if ($railwayKey === 'RAILWAY_PUBLIC_DOMAIN') {
                        return 'https://' . $railwayValue;
                    }
                    return $railwayValue;
                }
            }
        }

        // Return default if nothing found
        return $default;
    }
}

if (!function_exists('getEnvValue')) {
    /**
     * Get raw environment variable value from various sources.
     *
     * @param string $key
     * @return mixed|null
     */
    function getEnvValue(string $key)
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        $value = getenv($key);

        return $value === false ? null : $value;
    }
}
