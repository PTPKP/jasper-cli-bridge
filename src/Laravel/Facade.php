<?php

namespace PTPKP\JasperCliBridge\Laravel;

use Illuminate\Support\Facades\Facade as BaseFacade;
use PTPKP\JasperCliBridge\JasperReportService;

/**
 * @method static string generateReport(string $templatePath, string $dataSource = 'db', array $parameters = [], ?string $outputPath = null)
 * @method static bool templateExists(string $templateName)
 * @method static array listTemplates()
 * @method static string getJavaVersion()
 * @method static bool isJavaAvailable()
 * @method static \PTPKP\JasperCliBridge\Configuration getConfig()
 * @method static \PTPKP\JasperCliBridge\JasperReportService setConfig(\PTPKP\JasperCliBridge\Configuration $config)
 *
 * @see JasperReportService
 */
class Facade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'jasper';
    }
}
