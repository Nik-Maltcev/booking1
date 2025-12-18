<?php

/**
 * Property-Based Tests for Railway Environment Variable Configuration
 * 
 * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
 * **Validates: Requirements 1.5**
 * 
 * Property: *For any* environment variable defined in Railway, when the application 
 * reads configuration, it SHALL use the environment variable value instead of the 
 * default config value.
 */

require_once(ROOT_DIR . 'lib/Common/Helpers/namespace.php');

class RailwayEnvConfigTest extends TestBase
{
    /**
     * Store original environment values for cleanup
     */
    private array $originalEnvValues = [];

    /**
     * Railway to LibreBooking environment variable mappings
     */
    private const RAILWAY_MAPPINGS = [
        'MYSQL_HOST' => 'LB_DATABASE_HOSTSPEC',
        'MYSQLHOST' => 'LB_DATABASE_HOSTSPEC',
        'MYSQL_DATABASE' => 'LB_DATABASE_NAME',
        'MYSQLDATABASE' => 'LB_DATABASE_NAME',
        'MYSQL_USER' => 'LB_DATABASE_USER',
        'MYSQLUSER' => 'LB_DATABASE_USER',
        'MYSQL_PASSWORD' => 'LB_DATABASE_PASSWORD',
        'MYSQLPASSWORD' => 'LB_DATABASE_PASSWORD',
        'RAILWAY_PUBLIC_DOMAIN' => 'LB_SCRIPT_URL',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->originalEnvValues = [];
    }

    public function tearDown(): void
    {
        // Restore original environment values
        foreach ($this->originalEnvValues as $key => $value) {
            if ($value === false) {
                putenv($key);
            } else {
                putenv("$key=$value");
            }
        }
        
        // Clear any test environment variables
        foreach (array_keys(self::RAILWAY_MAPPINGS) as $key) {
            putenv($key);
        }
        
        parent::tearDown();
    }

    /**
     * Helper to set environment variable and track for cleanup
     */
    private function setEnvVar(string $key, string $value): void
    {
        if (!isset($this->originalEnvValues[$key])) {
            $this->originalEnvValues[$key] = getenv($key);
        }
        putenv("$key=$value");
    }

    /**
     * Helper to clear environment variable
     */
    private function clearEnvVar(string $key): void
    {
        if (!isset($this->originalEnvValues[$key])) {
            $this->originalEnvValues[$key] = getenv($key);
        }
        putenv($key);
    }

    /**
     * Generate random string for testing
     */
    private function generateRandomString(int $length = 16): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $result;
    }

    /**
     * Generate random hostname for testing
     */
    private function generateRandomHostname(): string
    {
        return $this->generateRandomString(8) . '.railway.internal';
    }

    /**
     * Generate random database name for testing
     */
    private function generateRandomDatabaseName(): string
    {
        return 'railway_' . $this->generateRandomString(8);
    }

    /**
     * Generate random domain for testing
     */
    private function generateRandomDomain(): string
    {
        return $this->generateRandomString(12) . '.up.railway.app';
    }

    /**
     * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
     * **Validates: Requirements 1.5**
     * 
     * Property test: For any MYSQL_HOST value, env('LB_DATABASE_HOSTSPEC') returns that value
     * 
     * @dataProvider mysqlHostProvider
     */
    public function testMysqlHostMapsToLbDatabaseHostspec(string $testHost): void
    {
        // Clear any existing LB_ variable to ensure Railway mapping is used
        $this->clearEnvVar('LB_DATABASE_HOSTSPEC');
        $this->setEnvVar('MYSQL_HOST', $testHost);

        $result = env('LB_DATABASE_HOSTSPEC');

        $this->assertEquals(
            $testHost,
            $result,
            "MYSQL_HOST='$testHost' should map to LB_DATABASE_HOSTSPEC"
        );
    }

    /**
     * Data provider for MYSQL_HOST property test - generates 100 random hostnames
     */
    public static function mysqlHostProvider(): array
    {
        $testCases = [];
        for ($i = 0; $i < 100; $i++) {
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $hostname = '';
            for ($j = 0; $j < 8; $j++) {
                $hostname .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $hostname .= '.railway.internal';
            $testCases["host_$i"] = [$hostname];
        }
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
     * **Validates: Requirements 1.5**
     * 
     * Property test: For any MYSQL_DATABASE value, env('LB_DATABASE_NAME') returns that value
     * 
     * @dataProvider mysqlDatabaseProvider
     */
    public function testMysqlDatabaseMapsToLbDatabaseName(string $testDatabase): void
    {
        $this->clearEnvVar('LB_DATABASE_NAME');
        $this->setEnvVar('MYSQL_DATABASE', $testDatabase);

        $result = env('LB_DATABASE_NAME');

        $this->assertEquals(
            $testDatabase,
            $result,
            "MYSQL_DATABASE='$testDatabase' should map to LB_DATABASE_NAME"
        );
    }

    /**
     * Data provider for MYSQL_DATABASE property test - generates 100 random database names
     */
    public static function mysqlDatabaseProvider(): array
    {
        $testCases = [];
        for ($i = 0; $i < 100; $i++) {
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789_';
            $dbname = 'railway_';
            for ($j = 0; $j < 8; $j++) {
                $dbname .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $testCases["db_$i"] = [$dbname];
        }
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
     * **Validates: Requirements 1.5**
     * 
     * Property test: For any MYSQL_USER value, env('LB_DATABASE_USER') returns that value
     * 
     * @dataProvider mysqlUserProvider
     */
    public function testMysqlUserMapsToLbDatabaseUser(string $testUser): void
    {
        $this->clearEnvVar('LB_DATABASE_USER');
        $this->setEnvVar('MYSQL_USER', $testUser);

        $result = env('LB_DATABASE_USER');

        $this->assertEquals(
            $testUser,
            $result,
            "MYSQL_USER='$testUser' should map to LB_DATABASE_USER"
        );
    }

    /**
     * Data provider for MYSQL_USER property test - generates 100 random usernames
     */
    public static function mysqlUserProvider(): array
    {
        $testCases = [];
        for ($i = 0; $i < 100; $i++) {
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789_';
            $username = '';
            for ($j = 0; $j < 12; $j++) {
                $username .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $testCases["user_$i"] = [$username];
        }
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
     * **Validates: Requirements 1.5**
     * 
     * Property test: For any MYSQL_PASSWORD value, env('LB_DATABASE_PASSWORD') returns that value
     * 
     * @dataProvider mysqlPasswordProvider
     */
    public function testMysqlPasswordMapsToLbDatabasePassword(string $testPassword): void
    {
        $this->clearEnvVar('LB_DATABASE_PASSWORD');
        $this->setEnvVar('MYSQL_PASSWORD', $testPassword);

        $result = env('LB_DATABASE_PASSWORD');

        $this->assertEquals(
            $testPassword,
            $result,
            "MYSQL_PASSWORD should map to LB_DATABASE_PASSWORD"
        );
    }

    /**
     * Data provider for MYSQL_PASSWORD property test - generates 100 random passwords
     */
    public static function mysqlPasswordProvider(): array
    {
        $testCases = [];
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        for ($i = 0; $i < 100; $i++) {
            $password = '';
            $length = random_int(8, 32);
            for ($j = 0; $j < $length; $j++) {
                $password .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $testCases["pass_$i"] = [$password];
        }
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
     * **Validates: Requirements 1.5**
     * 
     * Property test: For any RAILWAY_PUBLIC_DOMAIN value, env('LB_SCRIPT_URL') returns 
     * that value with https:// prefix
     * 
     * @dataProvider railwayDomainProvider
     */
    public function testRailwayPublicDomainMapsToLbScriptUrl(string $testDomain): void
    {
        $this->clearEnvVar('LB_SCRIPT_URL');
        $this->setEnvVar('RAILWAY_PUBLIC_DOMAIN', $testDomain);

        $result = env('LB_SCRIPT_URL');

        $this->assertEquals(
            'https://' . $testDomain,
            $result,
            "RAILWAY_PUBLIC_DOMAIN='$testDomain' should map to LB_SCRIPT_URL with https:// prefix"
        );
    }

    /**
     * Data provider for RAILWAY_PUBLIC_DOMAIN property test - generates 100 random domains
     */
    public static function railwayDomainProvider(): array
    {
        $testCases = [];
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        for ($i = 0; $i < 100; $i++) {
            $subdomain = '';
            for ($j = 0; $j < 12; $j++) {
                $subdomain .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $domain = $subdomain . '.up.railway.app';
            $testCases["domain_$i"] = [$domain];
        }
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
     * **Validates: Requirements 1.5**
     * 
     * Property test: Alternative Railway variable names (without underscore) also work
     * For any MYSQLHOST value, env('LB_DATABASE_HOSTSPEC') returns that value
     * 
     * @dataProvider mysqlhostAlternativeProvider
     */
    public function testMysqlhostAlternativeMapsToLbDatabaseHostspec(string $testHost): void
    {
        $this->clearEnvVar('LB_DATABASE_HOSTSPEC');
        $this->clearEnvVar('MYSQL_HOST');
        $this->setEnvVar('MYSQLHOST', $testHost);

        $result = env('LB_DATABASE_HOSTSPEC');

        $this->assertEquals(
            $testHost,
            $result,
            "MYSQLHOST='$testHost' should map to LB_DATABASE_HOSTSPEC"
        );
    }

    /**
     * Data provider for MYSQLHOST alternative property test - generates 100 random hostnames
     */
    public static function mysqlhostAlternativeProvider(): array
    {
        $testCases = [];
        for ($i = 0; $i < 100; $i++) {
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $hostname = '';
            for ($j = 0; $j < 8; $j++) {
                $hostname .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $hostname .= '.railway.internal';
            $testCases["althost_$i"] = [$hostname];
        }
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
     * **Validates: Requirements 1.5**
     * 
     * Property test: MYSQL_HOST takes precedence over MYSQLHOST when both are set
     */
    public function testMysqlHostTakesPrecedenceOverMysqlhost(): void
    {
        $this->clearEnvVar('LB_DATABASE_HOSTSPEC');
        
        $primaryHost = 'primary.railway.internal';
        $alternativeHost = 'alternative.railway.internal';
        
        $this->setEnvVar('MYSQL_HOST', $primaryHost);
        $this->setEnvVar('MYSQLHOST', $alternativeHost);

        $result = env('LB_DATABASE_HOSTSPEC');

        $this->assertEquals(
            $primaryHost,
            $result,
            "MYSQL_HOST should take precedence over MYSQLHOST"
        );
    }

    /**
     * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
     * **Validates: Requirements 1.5**
     * 
     * Property test: LB_ variables take precedence over Railway variables
     */
    public function testLbVariablesTakePrecedenceOverRailwayVariables(): void
    {
        $lbHost = 'lb-configured.example.com';
        $railwayHost = 'railway.internal';
        
        $this->setEnvVar('LB_DATABASE_HOSTSPEC', $lbHost);
        $this->setEnvVar('MYSQL_HOST', $railwayHost);

        $result = env('LB_DATABASE_HOSTSPEC');

        $this->assertEquals(
            $lbHost,
            $result,
            "LB_DATABASE_HOSTSPEC should take precedence over MYSQL_HOST"
        );
    }

    /**
     * **Feature: railway-deploy-russian, Property 1: Environment Variable Configuration**
     * **Validates: Requirements 1.5**
     * 
     * Property test: Default value is returned when no environment variable is set
     */
    public function testDefaultValueReturnedWhenNoEnvVarSet(): void
    {
        $this->clearEnvVar('LB_DATABASE_HOSTSPEC');
        $this->clearEnvVar('MYSQL_HOST');
        $this->clearEnvVar('MYSQLHOST');

        $defaultValue = 'default.host.com';
        $result = env('LB_DATABASE_HOSTSPEC', $defaultValue);

        $this->assertEquals(
            $defaultValue,
            $result,
            "Default value should be returned when no environment variable is set"
        );
    }
}
