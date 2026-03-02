<?php

namespace PTPKP\JasperCliBridge;

class Configuration
{
    /**
     * Path to the JasperReports CLI JAR file
     */
    protected string $jarPath;

    /**
     * Path to JRXML templates directory
     */
    protected string $templatesPath;

    /**
     * Path to output directory for generated reports
     */
    protected string $outputPath;

    /**
     * Java executable path (defaults to 'java')
     */
    protected string $javaExecutable = 'java';

    /**
     * Additional JVM options
     */
    protected array $jvmOptions = [];

    /**
     * Timeout for Java process execution (in seconds, 0 = no timeout)
     */
    protected int $timeout = 60;

    public function __construct(array $config = [])
    {
        $this->jarPath = $config['jar_path'] ?? $this->getDefaultJarPath();
        $this->templatesPath = $config['templates_path'] ?? '';
        $this->outputPath = $config['output_path'] ?? sys_get_temp_dir() . '/jasper-reports';
        $this->javaExecutable = $config['java_executable'] ?? 'java';
        $this->jvmOptions = $config['jvm_options'] ?? [];
        $this->timeout = $config['timeout'] ?? 60;

        // Ensure output directory exists
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
    }

    /**
     * Get the default JAR path from the package
     */
    protected function getDefaultJarPath(): string
    {
        $packageDir = dirname(__DIR__);
        return $packageDir . '/target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar';
    }

    public function getJarPath(): string
    {
        return $this->jarPath;
    }

    public function setJarPath(string $jarPath): self
    {
        $this->jarPath = $jarPath;
        return $this;
    }

    public function getTemplatesPath(): string
    {
        return $this->templatesPath;
    }

    public function setTemplatesPath(string $templatesPath): self
    {
        $this->templatesPath = $templatesPath;
        return $this;
    }

    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    public function setOutputPath(string $outputPath): self
    {
        $this->outputPath = $outputPath;
        if (!is_dir($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
        return $this;
    }

    public function getJavaExecutable(): string
    {
        return $this->javaExecutable;
    }

    public function setJavaExecutable(string $javaExecutable): self
    {
        $this->javaExecutable = $javaExecutable;
        return $this;
    }

    public function getJvmOptions(): array
    {
        return $this->jvmOptions;
    }

    public function setJvmOptions(array $jvmOptions): self
    {
        $this->jvmOptions = $jvmOptions;
        return $this;
    }

    public function addJvmOption(string $option): self
    {
        $this->jvmOptions[] = $option;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Validate the configuration
     */
    public function validate(): void
    {
        if (!file_exists($this->jarPath)) {
            throw new JasperException(
                "JAR file not found at: {$this->jarPath}. " .
                "Please build the JAR using 'mvn clean package' or set the correct path."
            );
        }

        if (!is_writable($this->outputPath)) {
            throw new JasperException(
                "Output directory is not writable: {$this->outputPath}"
            );
        }
    }
}
