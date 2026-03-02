<?php

namespace PTPKP\JasperCliBridge;

class JasperReportService
{
    /**
     * Configuration instance
     */
    protected Configuration $config;

    /**
     * Constructor
     */
    public function __construct(?Configuration $config = null)
    {
        $this->config = $config ?? new Configuration();
    }

    /**
     * Get the configuration instance
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * Set the configuration instance
     */
    public function setConfig(Configuration $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Generate a report from JRXML template
     *
     * @param string $templatePath Path to the .jrxml template file
     * @param string $dataSource Either path to JSON data file or 'db' for database connection
     * @param array $parameters Report parameters (optional)
     * @param string|null $outputPath Custom output path (optional, auto-generated if null)
     * @return string Path to the generated PDF file
     * @throws JasperException
     */
    public function generateReport(
        string $templatePath,
        string $dataSource = 'db',
        array $parameters = [],
        ?string $outputPath = null
    ): string {
        // Validate configuration
        $this->config->validate();

        // Resolve template path
        $jrxmlFile = $this->resolveTemplatePath($templatePath);
        
        if (!file_exists($jrxmlFile)) {
            throw new JasperException("JRXML template not found: {$jrxmlFile}");
        }

        // Generate output path if not provided
        if ($outputPath === null) {
            $outputPath = $this->generateOutputPath($templatePath);
        }

        // Ensure output directory exists
        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Create parameters file if parameters are provided
        $paramsFile = null;
        if (!empty($parameters)) {
            $paramsFile = $this->createParametersFile($parameters);
        }

        try {
            // Build and execute the command
            $command = $this->buildCommand($jrxmlFile, $dataSource, $outputPath, $paramsFile);
            $output = [];
            $exitCode = 0;

            exec($command . ' 2>&1', $output, $exitCode);

            // Clean up parameters file
            if ($paramsFile && file_exists($paramsFile)) {
                unlink($paramsFile);
            }

            // Check for errors
            if ($exitCode !== 0) {
                $errorOutput = implode("\n", $output);
                throw new JasperException(
                    "JasperReports CLI failed with exit code {$exitCode}: {$errorOutput}"
                );
            }

            if (!file_exists($outputPath)) {
                $cliOutput = implode("\n", $output);
                throw new JasperException(
                    "Output file not generated at: {$outputPath}. CLI output: {$cliOutput}"
                );
            }

            return $outputPath;
        } catch (JasperException $e) {
            // Clean up parameters file on error
            if ($paramsFile && file_exists($paramsFile)) {
                unlink($paramsFile);
            }
            throw $e;
        }
    }

    /**
     * Check if a template exists
     */
    public function templateExists(string $templateName): bool
    {
        try {
            $templatePath = $this->resolveTemplatePath($templateName);
            return file_exists($templatePath);
        } catch (JasperException $e) {
            return false;
        }
    }

    /**
     * List all available templates in the templates directory
     */
    public function listTemplates(): array
    {
        $templatesPath = $this->config->getTemplatesPath();
        
        if (empty($templatesPath) || !is_dir($templatesPath)) {
            return [];
        }

        $templates = [];
        foreach (scandir($templatesPath) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'jrxml') {
                $templates[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        return $templates;
    }

    /**
     * Resolve template path
     */
    protected function resolveTemplatePath(string $templatePath): string
    {
        // If it's already an absolute path and exists, use it
        if (file_exists($templatePath)) {
            return realpath($templatePath);
        }

        // If templates path is configured, try relative to templates directory
        $templatesPath = $this->config->getTemplatesPath();
        if (!empty($templatesPath)) {
            // Add .jrxml extension if not present
            if (pathinfo($templatePath, PATHINFO_EXTENSION) !== 'jrxml') {
                $templatePath .= '.jrxml';
            }
            
            $fullPath = $templatesPath . '/' . $templatePath;
            if (file_exists($fullPath)) {
                return realpath($fullPath);
            }
        }

        // Return the original path (validation will catch if it doesn't exist)
        return $templatePath;
    }

    /**
     * Generate output path for the report
     */
    protected function generateOutputPath(string $templatePath): string
    {
        $outputDir = $this->config->getOutputPath();
        $templateName = pathinfo($templatePath, PATHINFO_FILENAME);
        $timestamp = time();
        
        return "{$outputDir}/{$templateName}_{$timestamp}.pdf";
    }

    /**
     * Create a temporary JSON file with parameters
     */
    protected function createParametersFile(array $parameters): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'jasper_params_') . '.json';
        $jsonContent = json_encode($parameters, JSON_PRETTY_PRINT);
        
        if ($jsonContent === false) {
            throw new JasperException('Failed to encode parameters to JSON: ' . json_last_error_msg());
        }
        
        if (file_put_contents($tempFile, $jsonContent) === false) {
            throw new JasperException("Failed to write parameters file: {$tempFile}");
        }
        
        return $tempFile;
    }

    /**
     * Build the command to execute
     */
    protected function buildCommand(
        string $jrxmlFile,
        string $dataSource,
        string $outputPath,
        ?string $paramsFile
    ): string {
        $parts = [];
        
        // Java executable
        $parts[] = escapeshellarg($this->config->getJavaExecutable());
        
        // JVM options
        foreach ($this->config->getJvmOptions() as $option) {
            $parts[] = $option;
        }
        
        // JAR execution
        $parts[] = '-jar';
        $parts[] = escapeshellarg($this->config->getJarPath());
        
        // Arguments for the CLI
        $parts[] = escapeshellarg($jrxmlFile);
        $parts[] = escapeshellarg($dataSource);
        $parts[] = escapeshellarg($outputPath);
        
        if ($paramsFile !== null) {
            $parts[] = escapeshellarg($paramsFile);
        }
        
        return implode(' ', $parts);
    }

    /**
     * Get Java version information
     */
    public function getJavaVersion(): string
    {
        $command = escapeshellarg($this->config->getJavaExecutable()) . ' -version 2>&1';
        $output = [];
        exec($command, $output);
        
        return implode("\n", $output);
    }

    /**
     * Check if Java is available
     */
    public function isJavaAvailable(): bool
    {
        $command = 'command -v ' . escapeshellarg($this->config->getJavaExecutable()) . ' 2>&1';
        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);
        
        return $exitCode === 0;
    }
}
