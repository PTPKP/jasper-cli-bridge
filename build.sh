#!/bin/bash

# Build script for JasperReports CLI Bridge
# This script builds the Java CLI JAR file

set -e

echo "=================================="
echo "Building JasperReports CLI Bridge"
echo "=================================="
echo ""

# Check if Maven is installed
if ! command -v mvn &> /dev/null; then
    echo "Error: Maven is not installed or not in PATH"
    echo "Please install Maven: https://maven.apache.org/install.html"
    exit 1
fi

# Check if Java is installed
if ! command -v java &> /dev/null; then
    echo "Error: Java is not installed or not in PATH"
    echo "Please install Java 17 or higher: https://adoptium.net/"
    exit 1
fi

# Get Java version
java_version=$(java -version 2>&1 | awk -F '"' '/version/ {print $2}' | cut -d'.' -f1)
if [ "$java_version" -lt 17 ]; then
    echo "Error: Java 17 or higher is required (found Java $java_version)"
    exit 1
fi

echo "✓ Maven found: $(mvn -version | head -n1)"
echo "✓ Java found: $(java -version 2>&1 | head -n1)"
echo ""

# Navigate to script directory
cd "$(dirname "$0")"

# Clean and build
echo "Building JAR file..."
mvn clean package -q

# Check if build was successful
if [ -f "target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar" ]; then
    echo ""
    echo "=================================="
    echo "✓ Build successful!"
    echo "=================================="
    echo ""
    echo "JAR file location:"
    echo "  $(pwd)/target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar"
    echo ""
    echo "File size: $(du -h target/jasper-cli-bridge-1.0.0-jar-with-dependencies.jar | cut -f1)"
    echo ""
else
    echo ""
    echo "✗ Build failed!"
    echo "Please check the Maven output above for errors."
    exit 1
fi
