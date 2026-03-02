# JasperReports CLI Bridge - PHP Library Project

## 📋 Project Overview

This is a **PHP library** that wraps the JasperReports Java CLI, making it easy to generate PDF reports from JRXML templates in PHP applications. The library is designed to be published on **Packagist.org** for public use.

### Package Information
- **Package Name:** `ptpkp/jasper-cli-bridge`
- **Namespace:** `PTPKP\JasperCliBridge`
- **License:** MIT
- **PHP Version:** 8.0+
- **Java Version:** 17+

---

## 🏗️ Project Structure

```
jasper-cli-bridge/
├── src/                          # PHP source code
│   ├── Configuration.php         # Configuration management class
│   ├── JasperException.php       # Custom exception class
│   ├── JasperReportService.php   # Main service class
│   └── Laravel/                  # Laravel integration
│       ├── ServiceProvider.php   # Laravel service provider
│       └── Facade.php            # Laravel facade
│
├── config/                       # Configuration files
│   └── jasper-cli-bridge.php     # Laravel config file (publishable)
│
├── examples/                     # Usage examples
│   ├── basic-php-example.php     # Plain PHP usage
│   └── laravel-controller-example.php  # Laravel examples
│
├── src/main/java/                # Java CLI source code
│   └── com/klinikcare/jaspercli/
│       └── Main.java             # Java CLI entry point
│
├── target/                       # Maven build output (after build)
│   └── jasper-cli-bridge-1.0.0-jar-with-dependencies.jar
│
├── composer.json                 # PHP package definition
├── pom.xml                       # Maven (Java) build config
├── build.sh                      # Build script for Java CLI
│
├── README.md                     # Main documentation
├── INSTALL.md                    # Installation guide
├── CHANGELOG.md                  # Version history
├── CONTRIBUTING.md               # Contribution guidelines
├── LICENSE                       # MIT License
│
├── .gitignore                    # Git ignore rules
└── .gitattributes                # Git attributes
```

---

## 🔑 Key Components

### 1. Configuration Class (`src/Configuration.php`)

**Purpose:** Manages all configuration settings for the library.

**Key Properties:**
- `jar_path` - Path to the Java CLI JAR file
- `templates_path` - Directory containing JRXML templates
- `output_path` - Directory for generated PDFs
- `java_executable` - Java command/path
- `jvm_options` - Array of JVM options
- `timeout` - Execution timeout in seconds

**Usage:**
```php
$config = new Configuration([
    'templates_path' => '/path/to/templates',
    'output_path' => '/path/to/output',
    'jvm_options' => ['-Xmx512m'],
]);
```

### 2. JasperReportService Class (`src/JasperReportService.php`)

**Purpose:** Main class for generating reports.

**Key Methods:**
- `generateReport($templatePath, $dataSource, $parameters, $outputPath)` - Generate PDF
- `templateExists($templateName)` - Check if template exists
- `listTemplates()` - List all available templates
- `getJavaVersion()` - Get Java version info
- `isJavaAvailable()` - Check if Java is available

**Usage:**
```php
$service = new JasperReportService($config);
$pdfPath = $service->generateReport('invoice', 'db', ['id' => 123]);
```

### 3. JasperException Class (`src/JasperException.php`)

**Purpose:** Custom exception for JasperReports-related errors.

### 4. Laravel Service Provider (`src/Laravel/ServiceProvider.php`)

**Purpose:** Integrates the library with Laravel.

**Features:**
- Auto-discovery support (Laravel 5.5+)
- Configuration publishing
- Dependency injection support
- Service container bindings

**Registered Services:**
- `Configuration::class` - Singleton
- `JasperReportService::class` - Singleton
- `'jasper'` - Alias
- `'jasper.reports'` - Alias

### 5. Laravel Facade (`src/Laravel/Facade.php`)

**Purpose:** Provides convenient static access in Laravel.

**Usage:**
```php
use JasperBridge\CliBridge\Laravel\Facade as Jasper;

$pdf = Jasper::generateReport('invoice', 'db', ['id' => 123]);
```

---

## 🚀 How It Works

### Workflow

1. **PHP Code** → Calls `JasperReportService::generateReport()`
2. **Configuration** → Loads settings (paths, Java config)
3. **Template Resolution** → Finds the .jrxml template file
4. **Parameter Handling** → Creates temporary JSON file for parameters
5. **Command Building** → Constructs Java CLI command
6. **Java Execution** → Runs: `java -jar jasper-cli-bridge.jar template.jrxml data.json output.pdf params.json`
7. **PDF Generation** → Java CLI compiles JRXML and generates PDF
8. **Returns Path** → Returns path to generated PDF file
9. **Cleanup** → Removes temporary parameter files

### Data Flow

```
PHP Application
    ↓
JasperReportService (PHP)
    ↓
exec() Shell Command
    ↓
Java CLI (jasper-cli-bridge.jar)
    ↓
JasperReports Library (Java)
    ↓
PDF Output File
```

---

## 📦 Composer Configuration

The `composer.json` includes:

### Dependencies
- **require:** PHP 8.0+, ext-json
- **require-dev:** PHPUnit, PHPStan (for testing)

### Autoloading
- **PSR-4:** `JasperBridge\CliBridge\` → `src/`
- **Tests:** `JasperBridge\CliBridge\Tests\` → `tests/`

### Laravel Auto-Discovery
```json
"extra": {
    "laravel": {
        "providers": [
            "PTPKP\\JasperCliBridge\\Laravel\\ServiceProvider"
        ],
        "aliases": {
            "Jasper": "PTPKP\\JasperCliBridge\\Laravel\\Facade"
        }
    }
}
```

This makes the package **automatically register** in Laravel without manual configuration.

---

## 🛠️ Development Setup

### Prerequisites
```bash
# Check versions
php -v          # Should be 8.0+
java -version   # Should be 17+
mvn -version    # Should be 3.6+
composer -v     # Should be 2.0+
```

### Initial Setup
```bash
# 1. Clone/navigate to project
cd jasper-cli-bridge

# 2. Install PHP dependencies
composer install

# 3. Build Java CLI
./build.sh
# OR
mvn clean package

# 4. Create test directories
mkdir -p storage/jasper/templates
mkdir -p storage/jasper/reports
```

### Building the JAR
```bash
# Option 1: Use build script
./build.sh

# Option 2: Manual Maven build
mvn clean package

# Verify JAR exists
ls -lh target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar
```

### Testing
```bash
# Run PHP tests (when implemented)
composer test

# Manual test with example
php examples/basic-php-example.php
```

---

## 📝 Configuration Options

### Laravel (.env)
```env
JASPER_CLI_JAR_PATH=/custom/path/to/jar
JASPER_CLI_TEMPLATES_PATH=/app/storage/jasper/templates
JASPER_CLI_OUTPUT_PATH=/app/storage/jasper/reports
JASPER_CLI_JAVA_EXECUTABLE=/usr/bin/java
JASPER_CLI_TIMEOUT=60
```

### PHP (Direct Configuration)
```php
$config = new Configuration([
    'jar_path' => '/path/to/jar',
    'templates_path' => '/path/to/templates',
    'output_path' => '/path/to/output',
    'java_executable' => 'java',
    'jvm_options' => ['-Xmx512m', '-Dfile.encoding=UTF-8'],
    'timeout' => 120,
]);
```

---

## 🎯 Next Steps for Development

### Immediate Tasks
- [ ] Implement PHPUnit tests
- [ ] Add PHPStan static analysis
- [ ] Create sample JRXML templates for testing
- [ ] Add GitHub Actions CI/CD
- [ ] Create GitHub repository

### Before Publishing to Packagist
- [ ] Test with multiple PHP versions (8.0, 8.1, 8.2, 8.3)
- [ ] Test with multiple Java versions (17, 21)
- [ ] Test Laravel integration (9.x, 10.x, 11.x)
- [ ] Add badges to README (build status, downloads, version)
- [ ] Create releases and tags (v1.0.0)

### Publishing Process
```bash
# 1. Create GitHub repository
git init
git add .
git commit -m "Initial release v1.0.0"
git remote add origin https://github.com/YOUR-USERNAME/jasper-cli-bridge.git
git push -u origin main

# 2. Create version tag
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0

# 3. Submit to Packagist
# Go to https://packagist.org/packages/submit
# Enter repository URL: https://github.com/YOUR-USERNAME/jasper-cli-bridge
```

---

## 🔧 Common Development Commands

```bash
# Install dependencies
composer install

# Update dependencies
composer update

# Build Java CLI
mvn clean package

# Run tests
composer test

# Static analysis
vendor/bin/phpstan analyse

# Format code (if phpcs is installed)
vendor/bin/phpcbf

# Check coding standards
vendor/bin/phpcs
```

---

## 📚 Usage Examples

### Plain PHP
```php
require 'vendor/autoload.php';

use PTPKP\JasperCliBridge\Configuration;
use PTPKP\JasperCliBridge\JasperReportService;

$config = new Configuration([
    'templates_path' => __DIR__ . '/templates',
    'output_path' => __DIR__ . '/output',
]);

$jasper = new JasperReportService($config);

$pdf = $jasper->generateReport('invoice', 'db', [
    'invoice_id' => 12345,
    'customer_name' => 'John Doe'
]);

echo "PDF generated: $pdf\n";
```

### Laravel Facade
```php
use PTPKP\JasperCliBridge\Laravel\Facade as Jasper;

// Generate report
$pdf = Jasper::generateReport('invoice', 'db', ['id' => 123]);

// Check template
if (Jasper::templateExists('invoice')) {
    echo "Template found!";
}

// List templates
$templates = Jasper::listTemplates();
```

### Laravel Controller
```php
namespace App\Http\Controllers;

use PTPKP\JasperCliBridge\JasperReportService;

class ReportController extends Controller
{
    public function invoice(JasperReportService $jasper, $id)
    {
        $pdf = $jasper->generateReport('invoice', 'db', ['id' => $id]);
        
        return response()->download($pdf, 'invoice.pdf')
            ->deleteFileAfterSend(true);
    }
}
```

---

## 🐛 Troubleshooting

### JAR not found
```bash
cd jasper-cli-bridge
./build.sh
```

### Java not found
```bash
# Check Java installation
java -version

# If not installed
# Ubuntu/Debian: sudo apt install openjdk-17-jdk
# macOS: brew install openjdk@17
# Windows: Download from https://adoptium.net/
```

### Permission issues
```bash
chmod -R 755 storage/jasper
chmod +x build.sh
```

### Maven issues
```bash
# Clear Maven cache
rm -rf ~/.m2/repository

# Rebuild
mvn clean package -U
```

---

## 📖 Documentation Files

- **README.md** - Main documentation with API reference
- **INSTALL.md** - Step-by-step installation guide
- **CHANGELOG.md** - Version history and changes
- **CONTRIBUTING.md** - How to contribute to the project
- **LICENSE** - MIT License text
- **PROJECT.md** - This file (development overview)

---

## 🤝 Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

## 📄 License

MIT License - See [LICENSE](LICENSE) file.

---

## 🔗 Resources

- **JasperReports:** https://community.jaspersoft.com/
- **Packagist:** https://packagist.org/
- **PSR-4 Autoloading:** https://www.php-fig.org/psr/psr-4/
- **Laravel Package Development:** https://laravel.com/docs/packages
- **Maven:** https://maven.apache.org/

---

**Last Updated:** 2026-03-02
**Version:** 1.0.0
**Status:** Ready for initial release
