<?php

/**
 * Property-Based Tests for Russian Email Template Parity
 * 
 * **Feature: railway-deploy-russian, Property 3: Email Template Parity**
 * **Validates: Requirements 3.3, 4.3**
 * 
 * Property: *For any* email template file present in lang/en_us/, there SHALL exist 
 * a corresponding Russian template file in lang/ru_ru/ with the same filename.
 */

class RussianEmailTemplateParityTest extends TestBase
{
    private const ENGLISH_TEMPLATES_DIR = ROOT_DIR . 'lang/en_us/';
    private const RUSSIAN_TEMPLATES_DIR = ROOT_DIR . 'lang/ru_ru/';

    private array $englishTemplates = [];
    private array $russianTemplates = [];

    public function setUp(): void
    {
        parent::setUp();
        
        // Get list of English templates
        if (is_dir(self::ENGLISH_TEMPLATES_DIR)) {
            $this->englishTemplates = array_filter(
                scandir(self::ENGLISH_TEMPLATES_DIR),
                fn($file) => str_ends_with($file, '.tpl')
            );
        }
        
        // Get list of Russian templates
        if (is_dir(self::RUSSIAN_TEMPLATES_DIR)) {
            $this->russianTemplates = array_filter(
                scandir(self::RUSSIAN_TEMPLATES_DIR),
                fn($file) => str_ends_with($file, '.tpl')
            );
        }
    }

    /**
     * **Feature: railway-deploy-russian, Property 3: Email Template Parity**
     * **Validates: Requirements 3.3, 4.3**
     * 
     * Property test: For any English email template, a corresponding Russian template exists
     * 
     * @dataProvider englishTemplatesProvider
     */
    public function testRussianTemplateExistsForEnglishTemplate(string $templateName): void
    {
        $russianTemplatePath = self::RUSSIAN_TEMPLATES_DIR . $templateName;
        
        $this->assertFileExists(
            $russianTemplatePath,
            "Russian email template missing: '$templateName'"
        );
    }

    /**
     * Data provider that returns all English email template filenames
     */
    public static function englishTemplatesProvider(): array
    {
        $englishDir = ROOT_DIR . 'lang/en_us/';
        $testCases = [];
        
        if (is_dir($englishDir)) {
            $templates = array_filter(
                scandir($englishDir),
                fn($file) => str_ends_with($file, '.tpl')
            );
            
            foreach ($templates as $template) {
                $testCases[$template] = [$template];
            }
        }
        
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 3: Email Template Parity**
     * **Validates: Requirements 3.3, 4.3**
     * 
     * Property test: Russian email templates are not empty
     * 
     * @dataProvider russianTemplatesProvider
     */
    public function testRussianTemplateIsNotEmpty(string $templateName): void
    {
        $russianTemplatePath = self::RUSSIAN_TEMPLATES_DIR . $templateName;
        
        $this->assertFileExists($russianTemplatePath);
        
        $content = file_get_contents($russianTemplatePath);
        $this->assertNotEmpty(
            trim($content),
            "Russian email template '$templateName' should not be empty"
        );
    }

    /**
     * Data provider that returns all Russian email template filenames
     */
    public static function russianTemplatesProvider(): array
    {
        $russianDir = ROOT_DIR . 'lang/ru_ru/';
        $testCases = [];
        
        if (is_dir($russianDir)) {
            $templates = array_filter(
                scandir($russianDir),
                fn($file) => str_ends_with($file, '.tpl')
            );
            
            foreach ($templates as $template) {
                $testCases[$template] = [$template];
            }
        }
        
        return $testCases;
    }

    /**
     * **Feature: railway-deploy-russian, Property 3: Email Template Parity**
     * **Validates: Requirements 3.3, 4.3**
     * 
     * Property test: Russian templates contain Cyrillic characters (actual Russian text)
     * 
     * @dataProvider russianTemplatesProvider
     */
    public function testRussianTemplateContainsCyrillicText(string $templateName): void
    {
        $russianTemplatePath = self::RUSSIAN_TEMPLATES_DIR . $templateName;
        
        $this->assertFileExists($russianTemplatePath);
        
        $content = file_get_contents($russianTemplatePath);
        
        // Check that template contains Cyrillic characters (Russian text)
        $this->assertMatchesRegularExpression(
            '/[\p{Cyrillic}]/u',
            $content,
            "Russian email template '$templateName' should contain Cyrillic (Russian) text"
        );
    }

    /**
     * **Feature: railway-deploy-russian, Property 3: Email Template Parity**
     * **Validates: Requirements 3.3, 4.3**
     * 
     * Property test: Russian templates preserve Smarty template variables
     * 
     * @dataProvider templateVariablesProvider
     */
    public function testRussianTemplatePreservesSmartyVariables(string $templateName, array $expectedVariables): void
    {
        $russianTemplatePath = self::RUSSIAN_TEMPLATES_DIR . $templateName;
        
        if (!file_exists($russianTemplatePath)) {
            $this->markTestSkipped("Russian template '$templateName' does not exist");
        }
        
        $content = file_get_contents($russianTemplatePath);
        
        foreach ($expectedVariables as $variable) {
            $this->assertStringContainsString(
                $variable,
                $content,
                "Russian template '$templateName' should contain Smarty variable '$variable'"
            );
        }
    }

    /**
     * Data provider for template variables test
     * Returns template names with their expected Smarty variables
     */
    public static function templateVariablesProvider(): array
    {
        return [
            'ReservationCreated.tpl' => [
                'ReservationCreated.tpl',
                ['{$StartDate}', '{$EndDate}', '{$Title}', '{$ReferenceNumber}']
            ],
            'ReservationDeleted.tpl' => [
                'ReservationDeleted.tpl',
                ['{$StartDate}', '{$EndDate}', '{$ReferenceNumber}']
            ],
            'AccountActivation.tpl' => [
                'AccountActivation.tpl',
                ['{$FirstName}', '{$AppTitle}', '{$ActivationUrl}']
            ],
            'ResetPassword.tpl' => [
                'ResetPassword.tpl',
                ['{$FullName}', '{$AppTitle}', '{$TemporaryPassword}']
            ],
            'StartReminderEmail.tpl' => [
                'StartReminderEmail.tpl',
                ['{$ResourceName}', '{$StartDate}', '{$ReferenceNumber}']
            ],
            'EndReminderEmail.tpl' => [
                'EndReminderEmail.tpl',
                ['{$ResourceName}', '{$EndDate}', '{$ReferenceNumber}']
            ],
        ];
    }

    /**
     * **Feature: railway-deploy-russian, Property 3: Email Template Parity**
     * **Validates: Requirements 3.3, 4.3**
     * 
     * Property test: Russian templates directory exists
     */
    public function testRussianTemplatesDirectoryExists(): void
    {
        $this->assertDirectoryExists(
            self::RUSSIAN_TEMPLATES_DIR,
            "Russian email templates directory should exist at lang/ru_ru/"
        );
    }

    /**
     * **Feature: railway-deploy-russian, Property 3: Email Template Parity**
     * **Validates: Requirements 3.3, 4.3**
     * 
     * Property test: Count of Russian templates matches English templates
     */
    public function testRussianTemplateCountMatchesEnglish(): void
    {
        $englishCount = count($this->englishTemplates);
        $russianCount = count($this->russianTemplates);
        
        $this->assertEquals(
            $englishCount,
            $russianCount,
            "Russian template count ($russianCount) should match English template count ($englishCount)"
        );
    }

    /**
     * Summary test: List any missing Russian templates
     */
    public function testSummaryOfMissingTemplates(): void
    {
        $missingTemplates = array_diff($this->englishTemplates, $this->russianTemplates);
        
        if (!empty($missingTemplates)) {
            $this->addWarning(
                "Missing Russian templates: " . implode(', ', $missingTemplates)
            );
        }
        
        $this->assertEmpty(
            $missingTemplates,
            "All English templates should have Russian counterparts. Missing: " . 
            implode(', ', $missingTemplates)
        );
    }
}
