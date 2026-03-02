# Quick Start Guide

Get up and running with JasperReports CLI Bridge in 5 minutes.

## 🚀 Installation (3 steps)

### 1. Install Package
```bash
composer require ptpkp/jasper-cli-bridge
```

### 2. Build Java CLI
```bash
cd vendor/ptpkp/jasper-cli-bridge
./build.sh
```

### 3. Create Directories
```bash
mkdir -p storage/jasper/templates storage/jasper/reports
```

## 💻 Basic Usage

### Plain PHP
```php
<?php
require 'vendor/autoload.php';

use PTPKP\JasperCliBridge\Configuration;
use PTPKP\JasperCliBridge\JasperReportService;

// Setup
$config = new Configuration([
    'templates_path' => __DIR__ . '/storage/jasper/templates',
    'output_path' => __DIR__ . '/storage/jasper/reports',
]);

$jasper = new JasperReportService($config);

// Generate PDF
$pdf = $jasper->generateReport(
    'my-template',  // Template name (my-template.jrxml)
    'db',           // Use database connection
    [               // Parameters
        'invoice_id' => 12345,
        'date' => date('Y-m-d'),
    ]
);

echo "PDF created: $pdf\n";
```

## 🎨 Laravel Usage

### 1. Publish Config
```bash
php artisan vendor:publish --tag=jasper-config
```

### 2. Build JAR (One-time setup)
```bash
php artisan jasper:build
```

### 3. Configure (.env)
```env
JASPER_CLI_TEMPLATES_PATH=/app/storage/jasper/templates
JASPER_CLI_OUTPUT_PATH=/app/storage/jasper/reports
```

### 4. Use Facade
```php
use PTPKP\JasperCliBridge\Laravel\Facade as Jasper;

// Generate report
$pdf = Jasper::generateReport('invoice', 'db', ['id' => 123]);

// Download response
return response()->download($pdf, 'invoice.pdf')
    ->deleteFileAfterSend(true);
```

### 5. Use in Controller
```php
use PTPKP\JasperCliBridge\JasperReportService;

class ReportController extends Controller
{
    public function show(JasperReportService $jasper, $id)
    {
        $pdf = $jasper->generateReport('report', 'db', ['id' => $id]);
        return response()->file($pdf);
    }
}
```

## 📋 Common Tasks

### Check if Template Exists
```php
if ($jasper->templateExists('invoice')) {
    echo "Template found!";
}
```

### List All Templates
```php
$templates = $jasper->listTemplates();
foreach ($templates as $template) {
    echo "- $template\n";
}
```

### Use JSON Data
```php
$data = ['items' => [...]];
$jsonFile = tempnam(sys_get_temp_dir(), 'data') . '.json';
file_put_contents($jsonFile, json_encode($data));

$pdf = $jasper->generateReport('report', $jsonFile, []);
unlink($jsonFile);
```

### Custom Output Path
```php
$pdf = $jasper->generateReport(
    'invoice',
    'db',
    ['id' => 123],
    '/custom/path/invoice-123.pdf'  // Custom output
);
```

## 🔧 Configuration

### All Options
```php
$config = new Configuration([
    'jar_path' => '/path/to/jasper-cli-bridge.jar',
    'templates_path' => '/path/to/templates',
    'output_path' => '/path/to/output',
    'java_executable' => 'java',
    'jvm_options' => ['-Xmx512m'],
    'timeout' => 60,
]);
```

### JVM Memory Options
```php
$config->setJvmOptions([
    '-Xmx1024m',              // Max memory
    '-Xms256m',               // Initial memory
    '-Dfile.encoding=UTF-8',  // File encoding
]);
```

## 🐛 Troubleshooting

### Java not found?
```bash
java -version  # Check if installed
# Install: sudo apt install openjdk-17-jdk (Ubuntu)
```

### JAR not found?
```bash
# For Laravel
php artisan jasper:build

# Or manually
cd vendor/ptpkp/jasper-cli-bridge
mvn clean package
```

### Permission denied?
```bash
chmod -R 755 storage/jasper
chmod +x vendor/pkp/jasper-cli-bridge/build.sh
```

## 📚 Full Documentation

- [Installation Guide](INSTALL.md) - Detailed setup
- [README](README.md) - Complete API reference
- [Examples](examples/) - More code examples
- [Project Info](PROJECT.md) - Development details

## 🆘 Need Help?

- Check [INSTALL.md](INSTALL.md) for detailed installation steps
- See [examples/](examples/) for more usage examples
- Read [README.md](README.md) for full documentation
- Create an issue on GitHub

---

**That's it!** You're ready to generate PDF reports with JasperReports. 🎉
