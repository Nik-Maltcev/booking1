<?php

/**
 * Property-Based Tests for Russian Translation Completeness
 * 
 * **Feature: railway-deploy-russian, Property 2: Russian Translation Completeness**
 * **Validates: Requirements 3.2, 4.1, 4.2**
 * 
 * Property: *For any* string key present in the English language file (en_us.php), 
 * the Russian language file (ru_ru.php) SHALL contain a corresponding translation 
 * for that key.
 */

require_once(ROOT_DIR . 'lang/en_us.php');
require_once(ROOT_DIR . 'lang/ru_ru.php');

class RussianTranslationCompletenessTest extends TestBase
{
    private array $englishStrings = [];
    private array $russianStrings = [];
    private en_us $englishLang;
    private ru_ru $russianLang;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->englishLang = new en_us();
        $this->russianLang = new ru_ru();
        
        // Use reflection to access protected method
        $enReflection = new ReflectionMethod($this->englishLang, '_LoadStrings');
        $enReflection->setAccessible(true);
        $this->englishStrings = $enReflection->invoke($this->englishLang);
        
        $ruReflection = new ReflectionMethod($this->russianLang, '_LoadStrings');
        $ruReflection->setAccessible(true);
        $this->russianStrings = $ruReflection->invoke($this->russianLang);
    }

    /**
     * **Feature: railway-deploy-russian, Property 2: Russian Translation Completeness**
     * **Validates: Requirements 3.2, 4.1, 4.2**
     * 
     * Property test: For any string key in English, Russian file contains a translation
     * 
     * @dataProvider englishStringKeysProvider
     */
    public function testRussianTranslationExistsForEnglishKey(string $key): void
    {
        // Russian inherits from en_gb which inherits from Language
        // So we need to check if the key exists in Russian strings OR if it's inherited
        $russianHasKey = array_key_exists($key, $this->russianStrings);
        
        $this->assertTrue(
            $russianHasKey,
            "Russian translation missing for key: '$key' (English value: '{$this->englishStrings[$key]}')"
        );
    }

    /**
     * Data provider that returns all English string keys
     * This ensures we test every single key for translation completeness
     */
    public static function englishStringKeysProvider(): array
    {
        $englishLang = new en_us();
        $reflection = new ReflectionMethod($englishLang, '_LoadStrings');
        $reflection->setAccessible(true);
        $englishStrings = $reflection->invoke($englishLang);
        
        $testCases = [];
        foreach (array_keys($englishStrings) as $key) {
            $testCases[$key] = [$key];
        }
        
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 2: Russian Translation Completeness**
     * **Validates: Requirements 3.2, 4.1, 4.2**
     * 
     * Property test: Russian translations are not empty strings
     * 
     * @dataProvider russianStringKeysProvider
     */
    public function testRussianTranslationIsNotEmpty(string $key): void
    {
        $value = $this->russianStrings[$key] ?? '';
        
        $this->assertNotEmpty(
            trim($value),
            "Russian translation for key '$key' should not be empty"
        );
    }

    /**
     * Data provider that returns all Russian string keys
     */
    public static function russianStringKeysProvider(): array
    {
        $russianLang = new ru_ru();
        $reflection = new ReflectionMethod($russianLang, '_LoadStrings');
        $reflection->setAccessible(true);
        $russianStrings = $reflection->invoke($russianLang);
        
        $testCases = [];
        foreach (array_keys($russianStrings) as $key) {
            $testCases[$key] = [$key];
        }
        
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 2: Russian Translation Completeness**
     * **Validates: Requirements 3.2, 4.1, 4.2**
     * 
     * Property test: Russian date formats are properly configured
     */
    public function testRussianDateFormatsAreConfigured(): void
    {
        $ruReflection = new ReflectionMethod($this->russianLang, '_LoadDates');
        $ruReflection->setAccessible(true);
        $russianDates = $ruReflection->invoke($this->russianLang);
        
        $requiredDateKeys = [
            'general_date',
            'general_datetime',
            'short_datetime',
            'schedule_daily',
            'reservation_email',
        ];
        
        foreach ($requiredDateKeys as $key) {
            $this->assertArrayHasKey(
                $key,
                $russianDates,
                "Russian date format missing for key: '$key'"
            );
            $this->assertNotEmpty(
                $russianDates[$key],
                "Russian date format for '$key' should not be empty"
            );
        }
    }

    /**
     * **Feature: railway-deploy-russian, Property 2: Russian Translation Completeness**
     * **Validates: Requirements 3.2, 4.1, 4.2**
     * 
     * Property test: Russian date formats use European format (dd.mm.yyyy)
     */
    public function testRussianDateFormatsUseEuropeanFormat(): void
    {
        $ruReflection = new ReflectionMethod($this->russianLang, '_LoadDates');
        $ruReflection->setAccessible(true);
        $russianDates = $ruReflection->invoke($this->russianLang);
        
        // Check that general_date uses day.month.year format
        $generalDate = $russianDates['general_date'] ?? '';
        $this->assertStringContainsString(
            'd',
            $generalDate,
            "Russian general_date should contain day format"
        );
        $this->assertStringContainsString(
            'm',
            $generalDate,
            "Russian general_date should contain month format"
        );
        $this->assertStringContainsString(
            'Y',
            $generalDate,
            "Russian general_date should contain year format"
        );
        
        // Verify day comes before month (European format)
        $dayPos = strpos($generalDate, 'd');
        $monthPos = strpos($generalDate, 'm');
        $this->assertLessThan(
            $monthPos,
            $dayPos,
            "Russian date format should have day before month (European format)"
        );
    }

    /**
     * **Feature: railway-deploy-russian, Property 2: Russian Translation Completeness**
     * **Validates: Requirements 3.2, 4.1, 4.2**
     * 
     * Property test: Russian days of week are properly translated
     */
    public function testRussianDaysOfWeekAreTranslated(): void
    {
        $ruReflection = new ReflectionMethod($this->russianLang, '_LoadDays');
        $ruReflection->setAccessible(true);
        $russianDays = $ruReflection->invoke($this->russianLang);
        
        $this->assertArrayHasKey('full', $russianDays);
        $this->assertCount(7, $russianDays['full'], "Should have 7 full day names");
        
        // Check that days are in Russian (contain Cyrillic characters)
        foreach ($russianDays['full'] as $day) {
            $this->assertMatchesRegularExpression(
                '/[\p{Cyrillic}]/u',
                $day,
                "Day name '$day' should contain Cyrillic characters"
            );
        }
    }

    /**
     * **Feature: railway-deploy-russian, Property 2: Russian Translation Completeness**
     * **Validates: Requirements 3.2, 4.1, 4.2**
     * 
     * Property test: Russian months are properly translated
     */
    public function testRussianMonthsAreTranslated(): void
    {
        $ruReflection = new ReflectionMethod($this->russianLang, '_LoadMonths');
        $ruReflection->setAccessible(true);
        $russianMonths = $ruReflection->invoke($this->russianLang);
        
        $this->assertArrayHasKey('full', $russianMonths);
        $this->assertCount(12, $russianMonths['full'], "Should have 12 full month names");
        
        // Check that months are in Russian (contain Cyrillic characters)
        foreach ($russianMonths['full'] as $month) {
            $this->assertMatchesRegularExpression(
                '/[\p{Cyrillic}]/u',
                $month,
                "Month name '$month' should contain Cyrillic characters"
            );
        }
    }

    /**
     * **Feature: railway-deploy-russian, Property 2: Russian Translation Completeness**
     * **Validates: Requirements 3.2, 4.1, 4.2**
     * 
     * Property test: Russian HTML lang code is correct
     */
    public function testRussianHtmlLangCodeIsCorrect(): void
    {
        $ruReflection = new ReflectionMethod($this->russianLang, '_GetHtmlLangCode');
        $ruReflection->setAccessible(true);
        $langCode = $ruReflection->invoke($this->russianLang);
        
        $this->assertEquals(
            'ru',
            $langCode,
            "Russian HTML lang code should be 'ru'"
        );
    }

    /**
     * Summary test: Count missing translations
     * This is not a property test but provides useful diagnostic information
     */
    public function testSummaryOfMissingTranslations(): void
    {
        $missingKeys = array_diff_key($this->englishStrings, $this->russianStrings);
        $missingCount = count($missingKeys);
        
        // Log missing keys for debugging
        if ($missingCount > 0) {
            $this->addWarning(
                "Missing $missingCount translations: " . implode(', ', array_slice(array_keys($missingKeys), 0, 10)) . 
                ($missingCount > 10 ? '...' : '')
            );
        }
        
        // Allow some missing keys since Russian inherits from en_gb
        // The inheritance chain provides fallback translations
        $this->assertLessThanOrEqual(
            50, // Allow up to 50 missing keys that will fall back to English
            $missingCount,
            "Too many missing Russian translations: $missingCount keys"
        );
    }
}
