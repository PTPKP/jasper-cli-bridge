<?php

/**
 * Basic PHP Example - Using JasperReports CLI Bridge
 * 
 * This example demonstrates how to use the library in a plain PHP application
 * (without Laravel).
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PTPKP\JasperCliBridge\Configuration;
use PTPKP\JasperCliBridge\JasperReportService;
use PTPKP\JasperCliBridge\JasperException;

try {
    // Step 1: Create configuration
    $config = new Configuration([
        'templates_path' => __DIR__ . '/templates',
        'output_path' => __DIR__ . '/output',
        'java_executable' => 'java',
        'jvm_options' => ['-Xmx256m'],
        'timeout' => 60,
    ]);

    // Step 2: Create service instance
    $jasper = new JasperReportService($config);

    // Step 3: Check if Java is available
    if (!$jasper->isJavaAvailable()) {
        throw new Exception('Java is not installed or not in PATH');
    }

    echo "Java Version:\n";
    echo $jasper->getJavaVersion() . "\n\n";

    // Step 4: List available templates
    echo "Available Templates:\n";
    $templates = $jasper->listTemplates();
    if (empty($templates)) {
        echo "  No templates found in: {$config->getTemplatesPath()}\n";
        echo "  Please add .jrxml templates to the templates directory.\n\n";
    } else {
        foreach ($templates as $template) {
            echo "  - {$template}\n";
        }
        echo "\n";
    }

    // Step 5: Generate a report (example with JSON data)
    if (!empty($templates)) {
        $templateName = $templates[0];
        
        // Create sample JSON data
        $jsonData = [
            'title' => 'Sample Report',
            'date' => date('Y-m-d'),
            'items' => [
                ['name' => 'Item 1', 'quantity' => 5, 'price' => 10.50],
                ['name' => 'Item 2', 'quantity' => 3, 'price' => 25.00],
                ['name' => 'Item 3', 'quantity' => 2, 'price' => 15.75],
            ]
        ];
        
        $jsonFile = tempnam(sys_get_temp_dir(), 'jasper_data_') . '.json';
        file_put_contents($jsonFile, json_encode($jsonData));
        
        echo "Generating report: {$templateName}\n";
        
        $pdfPath = $jasper->generateReport(
            $templateName,
            $jsonFile,
            ['report_title' => 'My Custom Report'],
        );
        
        // Clean up JSON file
        unlink($jsonFile);
        
        echo "✓ Report generated successfully!\n";
        echo "  Output: {$pdfPath}\n";
        echo "  Size: " . round(filesize($pdfPath) / 1024, 2) . " KB\n";
    }

} catch (JasperException $e) {
    echo "JasperReports Error: {$e->getMessage()}\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    exit(1);
}
