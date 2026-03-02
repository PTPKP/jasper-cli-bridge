# Installation Guide

This guide will help you install and configure JasperReports CLI Bridge in your PHP or Laravel application.

## Prerequisites

Before installing, ensure you have:

1. **PHP 8.0 or higher**
   ```bash
   php -v
   ```

2. **Composer** (PHP package manager)
   ```bash
   composer --version
   ```

3. **Java 17 or newer**
   ```bash
   java -version
   ```

4. **Maven** (for building the Java CLI)
   ```bash
   mvn -version
   ```

## Installation Steps

### Step 1: Install via Composer

```bash
composer require ptpkp/jasper-cli-bridge
```

### Step 2: Build the Java CLI

Navigate to the package directory and build the JAR file:

```bash
cd vendor/ptpkp/jasper-cli-bridge
./build.sh
```

Or manually with Maven:

```bash
cd vendor/ptpkp/jasper-cli-bridge
mvn clean package
```

The JAR file will be created at:
```
vendor/ptpkp/jasper-cli-bridge/target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar
```

### Step 3: Create Required Directories

Create directories for templates and output:

```bash
# For plain PHP
mkdir -p storage/jasper/templates
mkdir -p storage/jasper/reports

# For Laravel
mkdir -p storage/app/jasper/templates
mkdir -p storage/app/jasper/reports
chmod -R 755 storage/app/jasper
```

## Laravel-Specific Setup

### Step 1: Publish Configuration

```bash
php artisan vendor:publish --tag=jasper-config
```

This creates `config/jasper-cli-bridge.php`.

### Step 2: Configure Environment

Add these variables to your `.env` file:

```env
# Path to JAR file (optional, auto-detected if null)
JASPER_CLI_JAR_PATH=

# Path to JRXML templates
JASPER_CLI_TEMPLATES_PATH="${APP_DIR}/storage/app/jasper/templates"

# Path for generated reports
JASPER_CLI_OUTPUT_PATH="${APP_DIR}/storage/app/jasper/reports"

# Java executable path
JASPER_CLI_JAVA_EXECUTABLE=java

# Timeout in seconds
JASPER_CLI_TIMEOUT=60
```

### Step 3: Register Service Provider (Optional)

The service provider is auto-discovered in Laravel 5.5+. For older versions, add to `config/app.php`:

```php
'providers' => [
    // ...
    PTPKP\JasperCliBridge\Laravel\ServiceProvider::class,
],

'aliases' => [
    // ...
    'Jasper' => PTPKP\JasperCliBridge\Laravel\Facade::class,
],
```

## Plain PHP Setup

### Step 1: Include Composer Autoloader

```php
require_once __DIR__ . '/vendor/autoload.php';
```

### Step 2: Create Configuration

```php
use PTPKP\JasperCliBridge\Configuration;
use PTPKP\JasperCliBridge\JasperReportService;

$config = new Configuration([
    'jar_path' => __DIR__ . '/vendor/ptpkp/jasper-cli-bridge/target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar',
    'templates_path' => __DIR__ . '/storage/jasper/templates',
    'output_path' => __DIR__ . '/storage/jasper/reports',
    'java_executable' => 'java',
]);

$jasper = new JasperReportService($config);
```

## Verification

### Test Java Installation

```bash
java -version
```

Expected output (Java 17+):
```
openjdk version "17.0.x" ...
```

### Test JAR File

```bash
java -jar vendor/pkp/jasper-cli-bridge/target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar
```

### Test PHP Integration

Create a test file `test-jasper.php`:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use PTPKP\JasperCliBridge\JasperReportService;
use PTPKP\JasperCliBridge\Configuration;

$config = new Configuration();
$jasper = new JasperReportService($config);

echo "Java Available: " . ($jasper->isJavaAvailable() ? 'Yes' : 'No') . "\n";
echo "Java Version:\n" . $jasper->getJavaVersion() . "\n";
```

Run:
```bash
php test-jasper.php
```

## Troubleshooting

### Issue: "JAR file not found"

**Solution:** Build the JAR file:
```bash
cd vendor/ptpkp/jasper-cli-bridge
mvn clean package
```

### Issue: "Java not found"

**Solution:** 
1. Install Java 17+ from [Adoptium](https://adoptium.net/)
2. Add Java to your PATH
3. Or specify full path in configuration:
   ```php
   'java_executable' => '/usr/lib/jvm/java-17-openjdk/bin/java'
   ```

### Issue: "Permission denied"

**Solution:** Make directories writable:
```bash
chmod -R 755 storage/app/jasper
chown -R www-data:www-data storage/app/jasper  # For web server
```

### Issue: "Maven not found"

**Solution:** Install Maven:
```bash
# Ubuntu/Debian
sudo apt install maven

# macOS
brew install maven

# Windows
# Download from https://maven.apache.org/download.cgi
```

## Next Steps

1. Add your JRXML template files to the templates directory
2. See [examples/](examples/) for usage examples
3. Read the [README.md](README.md) for detailed API documentation

## Support

If you encounter issues:

1. Check the [Troubleshooting](#troubleshooting) section
2. Review the [examples/](examples/) directory
3. Create an issue on [GitHub](https://github.com/ptpkp/jasper-cli-bridge/issues)
