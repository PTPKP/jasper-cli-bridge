<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JAR Path
    |--------------------------------------------------------------------------
    |
    | Path to the JasperReports CLI JAR file. If null, the package will use
    | the default path from the vendor directory:
    | vendor/ptpkp/jasper-cli-bridge/target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar
    |
    | You can set a custom path using the JASPER_CLI_JAR_PATH environment variable.
    |
    */
    'jar_path' => env('JASPER_CLI_JAR_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Templates Path
    |--------------------------------------------------------------------------
    |
    | Path to the directory containing JRXML template files.
    | By default, templates are stored in storage/app/jasper/templates
    |
    */
    'templates_path' => env('JASPER_CLI_TEMPLATES_PATH', storage_path('app/jasper/templates')),

    /*
    |--------------------------------------------------------------------------
    | Output Path
    |--------------------------------------------------------------------------
    |
    | Path to the directory where generated reports will be saved.
    | By default, reports are saved to storage/app/jasper/reports
    |
    */
    'output_path' => env('JASPER_CLI_OUTPUT_PATH', storage_path('app/jasper/reports')),

    /*
    |--------------------------------------------------------------------------
    | Java Executable
    |--------------------------------------------------------------------------
    |
    | Path to the Java executable. Defaults to 'java' which assumes Java is
    | in the system PATH. You can specify a full path if needed.
    |
    | Example: '/usr/lib/jvm/java-17-openjdk/bin/java'
    |
    */
    'java_executable' => env('JASPER_CLI_JAVA_EXECUTABLE', 'java'),

    /*
    |--------------------------------------------------------------------------
    | JVM Options
    |--------------------------------------------------------------------------
    |
    | Additional JVM options to pass to the Java process.
    | Common options:
    |   - Memory: '-Xmx512m', '-Xms256m'
    |   - Encoding: '-Dfile.encoding=UTF-8'
    |
    */
    'jvm_options' => [
        // Example: '-Xmx512m',
        // Example: '-Dfile.encoding=UTF-8',
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum execution time for report generation (in seconds).
    | Set to 0 for no timeout.
    |
    */
    'timeout' => env('JASPER_CLI_TIMEOUT', 60),
];
