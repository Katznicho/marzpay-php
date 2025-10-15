# Contributing to MarzPay PHP SDK

Thank you for your interest in contributing to the MarzPay PHP SDK! This document provides guidelines and information for contributors.

## 🚀 Getting Started

### Prerequisites

- PHP 8.0 or higher
- Composer
- Git
- A MarzPay API account for testing

### Development Setup

1. **Fork and clone the repository**
   ```bash
   git clone https://github.com/your-username/marzpay-php.git
   cd marzpay-php
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up environment**
   ```bash
   cp env.testing .env
   # Add your real API credentials to .env
   ```

4. **Run tests**
   ```bash
   php run-tests.php unit
   ```

## 📋 Development Guidelines

### Code Style

We follow PSR-12 coding standards. Please ensure your code adheres to these standards:

```bash
# Check code style
composer run cs

# Fix code style issues
composer run cs-fix
```

### Testing

All contributions must include appropriate tests:

```bash
# Run unit tests
php run-tests.php unit

# Run integration tests (requires real API credentials)
php run-tests.php integration

# Run all tests
php run-tests.php all

# Run tests with coverage
php run-tests.php coverage
```

### Static Analysis

We use PHPStan for static analysis:

```bash
composer run stan
```

## 🔄 Pull Request Process

### Before Submitting

1. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**
   - Write code following PSR-12 standards
   - Add tests for new functionality
   - Update documentation as needed
   - Update CHANGELOG.md

3. **Test thoroughly**
   ```bash
   # Run all checks
   composer run cs
   composer run stan
   php run-tests.php all
   ```

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "feat: add new feature description"
   ```

### Pull Request Guidelines

1. **Title**: Use clear, descriptive titles
   - `feat:` for new features
   - `fix:` for bug fixes
   - `docs:` for documentation updates
   - `test:` for test additions/changes
   - `refactor:` for code refactoring

2. **Description**: Include:
   - What changes you made
   - Why you made them
   - How to test the changes
   - Any breaking changes

3. **Testing**: Ensure all tests pass and coverage is maintained

## 🐛 Reporting Issues

### Bug Reports

When reporting bugs, please include:

1. **Environment details**:
   - PHP version
   - SDK version
   - Operating system

2. **Steps to reproduce**:
   - Clear, numbered steps
   - Expected vs actual behavior

3. **Error details**:
   - Full error messages
   - Stack traces (if applicable)

4. **Code example**:
   - Minimal code that reproduces the issue

### Feature Requests

For feature requests, please include:

1. **Use case**: Why do you need this feature?
2. **Proposed solution**: How should it work?
3. **Alternatives**: What other solutions have you considered?

## 🏗️ Architecture Guidelines

### Code Organization

- **Classes**: Place in appropriate namespaces under `src/`
- **Tests**: Mirror the `src/` structure under `tests/`
- **Laravel**: Laravel-specific code goes in `src/Laravel/`

### API Design

- **Consistency**: Follow existing patterns
- **Error Handling**: Use MarzPayException for API errors
- **Validation**: Validate input parameters
- **Documentation**: Add PHPDoc comments

### Example Structure

```php
<?php

namespace MarzPay\Classes;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class NewAPI
{
    private MarzPay $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    /**
     * Method description
     * 
     * @param array $params Parameters
     * @return array Response
     * @throws MarzPayException When request fails
     */
    public function newMethod(array $params): array
    {
        // Implementation
    }
}
```

## 📚 Documentation

### Updating Documentation

When adding new features:

1. **Update README.md** with basic usage examples
2. **Update API-REFERENCE.md** with detailed method documentation
3. **Update README-LARAVEL.md** if Laravel-specific changes
4. **Update CHANGELOG.md** with your changes
5. **Add examples** in the `examples/` directory if applicable

### Documentation Standards

- Use clear, concise language
- Include code examples
- Explain error cases
- Keep examples up-to-date

## 🧪 Testing Guidelines

### Test Types

1. **Unit Tests**: Test individual methods and classes
2. **Integration Tests**: Test API interactions (requires real credentials)
3. **Feature Tests**: Test complete workflows

### Test Structure

```php
<?php

namespace MarzPay\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MarzPay\MarzPay;

class NewAPITest extends TestCase
{
    public function test_new_method_success()
    {
        // Test implementation
    }
    
    public function test_new_method_validation_error()
    {
        // Test error cases
    }
}
```

### Test Requirements

- All new code must have tests
- Maintain or improve test coverage
- Use descriptive test method names
- Test both success and failure cases

## 🔒 Security

### Security Guidelines

1. **Never commit credentials**: Use environment variables
2. **Validate input**: Always validate and sanitize input
3. **Handle errors safely**: Don't expose sensitive information
4. **Use HTTPS**: All API calls use HTTPS
5. **Follow OWASP guidelines**: For web-related security

### Reporting Security Issues

For security vulnerabilities, please:

1. **DO NOT** create public issues
2. **DO** email security@wearemarz.com
3. **Include** detailed information about the vulnerability
4. **Wait** for confirmation before disclosing

## 📝 Commit Message Guidelines

### Format

```
type(scope): description

[optional body]

[optional footer]
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

### Examples

```
feat(collections): add support for custom references

fix(auth): handle expired API keys properly

docs(readme): update installation instructions

test(integration): add phone verification tests
```

## 🏷️ Release Process

### Version Numbering

We follow [Semantic Versioning](https://semver.org/):

- **MAJOR**: Breaking changes
- **MINOR**: New features (backward compatible)
- **PATCH**: Bug fixes (backward compatible)

### Release Checklist

- [ ] All tests pass
- [ ] Documentation updated
- [ ] CHANGELOG.md updated
- [ ] Version bumped in composer.json
- [ ] Tagged in Git
- [ ] Released to Packagist

## 🤝 Community

### Getting Help

- **Documentation**: Check the docs first
- **Issues**: Search existing issues
- **Discussions**: Use GitHub Discussions for questions
- **Email**: dev@wearemarz.com for direct support

### Code of Conduct

- Be respectful and inclusive
- Focus on constructive feedback
- Help others learn and grow
- Follow the golden rule

## 📄 License

By contributing to this project, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to MarzPay PHP SDK! 🚀**

For more information, visit:
- [MarzPay API Documentation](https://wallet.wearemarz.com/documentation)
- [GitHub Repository](https://github.com/marzpay/php-sdk)
- [Support](mailto:dev@wearemarz.com)
