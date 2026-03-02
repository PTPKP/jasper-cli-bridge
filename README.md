# JasperReports CLI Bridge for PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ptpkp/jasper-cli-bridge.svg?style=flat-square)](https://packagist.org/packages/ptpkp/jasper-cli-bridge)
[![Total Downloads](https://img.shields.io/packagist/dt/ptpkp/jasper-cli-bridge.svg?style=flat-square)](https://packagist.org/packages/ptpkp/jasper-cli-bridge)
[![License](https://img.shields.io/packagist/l/ptpkp/jasper-cli-bridge.svg?style=flat-square)](https://packagist.org/packages/ptpkp/jasper-cli-bridge)

A PHP library that provides a lightweight bridge to JasperReports, enabling you to compile, fill, and export professional PDF reports using JRXML templates. This package includes both a Java CLI tool and a PHP wrapper for seamless integration into PHP/Laravel applications.

## Features

- 🚀 Easy integration with PHP and Laravel
- 📊 Generate PDF reports from JRXML templates
- 💾 Support for JSON data sources and database connections
- 🎨 Full JasperReports feature support (charts, subreports, barcodes, etc.)
- ⚡ Lightweight CLI bridge (no heavy JasperReports server needed)
- 🔧 Configurable and flexible
- 📦 Laravel service provider and facade included

## Requirements

- PHP 8.0 or higher
- Java 17 or newer
- Maven (for building the Java CLI)

## Installation

Install the package via Composer:

```bash
composer require ptpkp/jasper-cli-bridge
```

### Build the Java CLI

After installation, you need to build the Java CLI JAR file:

```bash
cd vendor/ptpkp/jasper-cli-bridge
mvn clean package
```

The JAR file will be created at `vendor/ptpkp/jasper-cli-bridge/target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar`.

## Usage

### Basic PHP Usage

```php
use PTPKP\JasperCliBridge\JasperReportService;
use PTPKP\JasperCliBridge\Configuration;

// Create configuration
$config = new Configuration([
    'templates_path' => '/path/to/templates',
    'output_path' => '/path/to/output',
]);

// Create service instance
$jasper = new JasperReportService($config);

// Generate a report
$pdfPath = $jasper->generateReport(
    'invoice_template',           // Template name (or full path to .jrxml)
    'db',                          // Data source: 'db' or path to JSON file
    ['invoice_id' => 12345],      // Report parameters (optional)
    '/custom/output/path.pdf'     // Custom output path (optional)
);

echo "Report generated: {$pdfPath}";
```

### Laravel Usage

#### 1. Publish Configuration

```bash
php artisan vendor:publish --tag=jasper-config
```

This creates `config/jasper-cli-bridge.php`.

#### 2. Configure Environment Variables

Add to your `.env` file:

```env
JASPER_CLI_TEMPLATES_PATH=/path/to/your/templates
JASPER_CLI_OUTPUT_PATH=/path/to/your/reports
JASPER_CLI_JAVA_EXECUTABLE=java
```

#### 3. Use the Facade

```php
use PTPKP\JasperCliBridge\Laravel\Facade as Jasper;

// Generate a report
$pdfPath = Jasper::generateReport('invoice', 'db', [
    'invoice_id' => 12345,
    'customer_name' => 'John Doe'
]);

// Check if template exists
if (Jasper::templateExists('invoice')) {
    // Template found
}

// List all templates
$templates = Jasper::listTemplates();
```

#### 4. Use Dependency Injection

```php
use PTPKP\JasperCliBridge\JasperReportService;

class InvoiceController extends Controller
{
    public function generate(JasperReportService $jasper, $invoiceId)
    {
        $pdfPath = $jasper->generateReport('invoice', 'db', [
            'invoice_id' => $invoiceId
        ]);
        
        return response()->file($pdfPath);
    }
}
```

## Configuration

### PHP Configuration

```php
use PTPKP\JasperCliBridge\Configuration;

$config = new Configuration([
    // Path to the JAR file (null = auto-detect from vendor)
    'jar_path' => '/custom/path/to/jasper-cli-bridge.jar',
    
    // Path to JRXML templates directory
    'templates_path' => '/path/to/templates',
    
    // Path for generated reports
    'output_path' => '/path/to/output',
    
    // Java executable (default: 'java')
    'java_executable' => '/usr/bin/java',
    
    // JVM options
    'jvm_options' => ['-Xmx512m', '-Dfile.encoding=UTF-8'],
    
    // Execution timeout in seconds (0 = no timeout)
    'timeout' => 120,
]);
```

### Laravel Configuration

Edit `config/jasper-cli-bridge.php`:

```php
return [
    'jar_path' => env('JASPER_CLI_JAR_PATH', null),
    'templates_path' => env('JASPER_CLI_TEMPLATES_PATH', storage_path('app/jasper/templates')),
    'output_path' => env('JASPER_CLI_OUTPUT_PATH', storage_path('app/jasper/reports')),
    'java_executable' => env('JASPER_CLI_JAVA_EXECUTABLE', 'java'),
    'jvm_options' => [
        // '-Xmx512m',
    ],
    'timeout' => env('JASPER_CLI_TIMEOUT', 60),
];
```

## CLI Usage

You can also use the Java CLI directly:

```bash
java -jar jasper-cli-bridge.jar <template.jrxml> <data.json|db> <output.pdf> [params.json]
```

**Parameters:**
- `<template.jrxml>`: Path to JRXML template file
- `<data.json|db>`: Path to JSON data file, or `db` for database connection
- `<output.pdf>`: Destination PDF file path
- `[params.json]`: Optional JSON file with report parameters

**Examples:**

```bash
# Generate from JSON data
java -jar jasper-cli-bridge.jar \
    /templates/invoice.jrxml \
    /data/invoice-data.json \
    /output/invoice.pdf \
    /data/params.json

# Generate from database (requires Laravel .env)
java -jar jasper-cli-bridge.jar \
    /templates/invoice.jrxml \
    db \
    /output/invoice.pdf \
    /data/params.json
```

## Directory Structure

```
jasper-cli-bridge/
├── config/
│   └── jasper-cli-bridge.php   # Laravel configuration
├── src/
│   ├── Configuration.php        # Configuration class
│   ├── JasperException.php      # Exception class
│   ├── JasperReportService.php  # Main service class
│   └── Laravel/
│       ├── Facade.php           # Laravel facade
│       └── ServiceProvider.php  # Laravel service provider
├── target/                      # Built JAR files (after mvn package)
├── composer.json
├── pom.xml                      # Maven configuration
└── README.md
```

## Examples

### Example 1: Generate Invoice PDF

```php
use PTPKP\JasperCliBridge\JasperReportService;
use PTPKP\JasperCliBridge\Configuration;

$config = new Configuration([
    'templates_path' => storage_path('app/reports/templates'),
    'output_path' => storage_path('app/reports/output'),
]);

$jasper = new JasperReportService($config);

$pdfPath = $jasper->generateReport('invoice', 'db', [
    'invoice_id' => 12345,
    'company_name' => 'My Company',
    'logo_path' => public_path('images/logo.png'),
]);

return response()->download($pdfPath, 'invoice.pdf')->deleteFileAfterSend();
```

### Example 2: Using JSON Data Source

```php
// Create JSON data file
$data = [
    'invoice' => [
        'id' => 12345,
        'date' => '2026-03-02',
        'customer' => 'John Doe',
        'items' => [
            ['name' => 'Product A', 'qty' => 2, 'price' => 50],
            ['name' => 'Product B', 'qty' => 1, 'price' => 75],
        ]
    ]
];

$jsonPath = tempnam(sys_get_temp_dir(), 'invoice_data') . '.json';
file_put_contents($jsonPath, json_encode($data));

// Generate report
$pdfPath = $jasper->generateReport(
    'invoice',
    $jsonPath,
    ['title' => 'Invoice']
);

// Clean up
unlink($jsonPath);
```

## Troubleshooting

### Java Not Found

Make sure Java 17+ is installed and in your PATH:

```bash
java -version
```

Or specify the full path in configuration:

```php
'java_executable' => '/usr/lib/jvm/java-17-openjdk/bin/java'
```

### Permission Issues

Ensure the output directory is writable:

```bash
chmod -R 755 storage/app/jasper
```

### JAR Not Found

Build the JAR file:

```bash
cd vendor/ptpkp/jasper-cli-bridge
mvn clean package
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

- [PTPKP Organization](https://github.com/ptpkp)
- [All Contributors](https://github.com/ptpkp/jasper-cli-bridge/contributors)

## Support

For bugs or feature requests, please create an issue on [GitHub](https://github.com/ptpkp/jasper-cli-bridge/issues).
