# Contributing to JasperReports CLI Bridge

Thank you for considering contributing to this project! We welcome contributions from the community.

## How to Contribute

### Reporting Bugs

If you find a bug, please create an issue on GitHub with:

- A clear, descriptive title
- Steps to reproduce the issue
- Expected behavior
- Actual behavior
- Your environment (PHP version, Java version, OS)
- Any relevant code samples or error messages

### Suggesting Features

Feature suggestions are welcome! Please create an issue with:

- A clear description of the feature
- Use cases and benefits
- Any implementation ideas you might have

### Pull Requests

1. **Fork the repository**
   ```bash
   git clone https://github.com/ptpkp/jasper-cli-bridge.git
   cd jasper-cli-bridge
   ```

2. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make your changes**
   - Follow PSR-12 coding standards for PHP
   - Follow Google Java style guide for Java code
   - Add tests if applicable
   - Update documentation as needed

4. **Test your changes**
   ```bash
   # Build Java CLI
   mvn clean package
   
   # Run PHP tests (when available)
   composer test
   ```

5. **Commit your changes**
   ```bash
   git add .
   git commit -m "Add: Brief description of your changes"
   ```

   Use conventional commit messages:
   - `Add:` for new features
   - `Fix:` for bug fixes
   - `Update:` for updates to existing features
   - `Remove:` for removed features
   - `Docs:` for documentation changes

6. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Create a Pull Request**
   - Provide a clear description of the changes
   - Reference any related issues
   - Ensure all tests pass

## Development Setup

### Prerequisites

- PHP 8.0+
- Java 17+
- Maven 3.6+
- Composer 2.0+

### Setup Steps

1. Clone the repository
2. Install PHP dependencies: `composer install`
3. Build the Java CLI: `mvn clean package`
4. Run tests (when available): `composer test`

## Coding Standards

### PHP

- Follow PSR-12 coding style
- Use type hints and return types
- Document all public methods with PHPDoc
- Keep methods focused and single-purpose

### Java

- Follow Google Java Style Guide
- Use meaningful variable and method names
- Add Javadoc for public classes and methods
- Keep methods short and focused

### Documentation

- Update README.md for significant changes
- Add examples for new features
- Update CHANGELOG.md
- Keep code comments clear and concise

## Testing

- Add tests for new features
- Ensure existing tests pass
- Test with different PHP versions (8.0, 8.1, 8.2, 8.3)
- Test with different Java versions (17, 21)

## Questions?

Feel free to create an issue for any questions about contributing.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.
