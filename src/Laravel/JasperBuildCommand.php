<?php

namespace PTPKP\JasperCliBridge\Laravel;

use Illuminate\Console\Command;

class JasperBuildCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'jasper:build';

    /**
     * The console command description.
     */
    protected $description = 'Build the JasperReports CLI Bridge JAR file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Building JasperReports CLI Bridge...');
        $this->newLine();

        $packageDir = dirname(__DIR__, 2);
        $buildScript = $packageDir . '/build.sh';

        if (!$this->validateBuildScript($buildScript)) {
            return self::FAILURE;
        }

        $this->info('Executing build script...');
        $this->newLine();

        $exitCode = $this->executeBuildScript($buildScript, $packageDir);

        return $this->handleBuildResult($exitCode, $packageDir);
    }

    /**
     * Validate the build script exists and is executable.
     */
    private function validateBuildScript(string $buildScript): bool
    {
        if (!file_exists($buildScript)) {
            $this->error("Build script not found at: {$buildScript}");
            return false;
        }

        if (!is_executable($buildScript)) {
            $this->warn('Build script is not executable. Attempting to make it executable...');
            @chmod($buildScript, 0755); // Suppressed as it's intended to make our own build script executable
        }

        return true;
    }

    /**
     * Execute the build script and stream output.
     */
    private function executeBuildScript(string $buildScript, string $workingDir): int
    {
        $process = proc_open(
            $buildScript,
            [
                0 => ['pipe', 'r'],  // stdin
                1 => ['pipe', 'w'],  // stdout
                2 => ['pipe', 'w'],  // stderr
            ],
            $pipes,
            $workingDir
        );

        if (!is_resource($process)) {
            $this->error('Failed to start build process');
            return 1;
        }

        fclose($pipes[0]);
        $this->streamProcessOutput($pipes[1], $pipes[2]);

        return proc_close($process);
    }

    /**
     * Stream process output to console.
     */
    private function streamProcessOutput($stdoutPipe, $stderrPipe): void
    {
        stream_set_blocking($stdoutPipe, false);
        stream_set_blocking($stderrPipe, false);

        while (!feof($stdoutPipe) || !feof($stderrPipe)) {
            $stdoutChunk = fread($stdoutPipe, 8192);
            $stderrChunk = fread($stderrPipe, 8192);

            if ($stdoutChunk !== false && $stdoutChunk !== '') {
                $this->line(rtrim($stdoutChunk));
            }

            if ($stderrChunk !== false && $stderrChunk !== '') {
                $this->error(rtrim($stderrChunk));
            }

            usleep(10000); // 10ms delay to prevent busy waiting
        }

        fclose($stdoutPipe);
        fclose($stderrPipe);
    }

    /**
     * Handle the build result and display appropriate messages.
     */
    private function handleBuildResult(int $exitCode, string $packageDir): int
    {
        $this->newLine();

        if ($exitCode === 0) {
            $this->displaySuccessMessage($packageDir);
            return self::SUCCESS;
        }

        $this->displayFailureMessage($exitCode);
        return self::FAILURE;
    }

    /**
     * Display success message and JAR file information.
     */
    private function displaySuccessMessage(string $packageDir): void
    {
        $this->info('✓ Build completed successfully!');

        $jarFile = $packageDir . '/target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar';
        if (file_exists($jarFile)) {
            $this->newLine();
            $this->line("JAR file location:");
            $this->line("  {$jarFile}");
            $this->newLine();
            $fileSize = $this->formatBytes(filesize($jarFile));
            $this->line("File size: {$fileSize}");
        }
    }

    /**
     * Display failure message with troubleshooting tips.
     */
    private function displayFailureMessage(int $exitCode): void
    {
        $this->error("✗ Build failed with exit code: {$exitCode}");
        $this->newLine();
        $this->line('Please check the output above for errors.');
        $this->line('Common issues:');
        $this->line('  - Maven not installed or not in PATH');
        $this->line('  - Java 17+ not installed or not in PATH');
        $this->line('  - Network issues downloading dependencies');
    }

    /**
     * Format bytes to human-readable size.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
