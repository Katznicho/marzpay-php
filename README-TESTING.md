# MarzPay PHP SDK - Testing Guide

This document provides comprehensive information about testing the MarzPay PHP SDK.

## Test Structure

The SDK includes multiple types of tests:

### 1. Unit Tests (`tests/Unit/`)
- Test individual components without external dependencies
- Mock API responses and test business logic
- Fast execution, no network calls required

### 2. Integration Tests (`tests/Integration/`)
- Test actual API interactions with MarzPay servers
- Require real API credentials
- Test authentication, basic API calls

### 3. Feature Tests (`tests/Feature/`)
- Test complete workflows and user scenarios
- Require real API credentials
- Test end-to-end functionality

## Quick Start

### 1. Install Dependencies
```bash
composer install
```

### 2. Run Quick Validation
```bash
php test-quick.php
```

This will test basic SDK functionality without requiring API credentials.

### 3. Run Unit Tests
```bash
php run-tests.php unit
# or directly:
vendor/bin/phpunit tests/Unit
```

### 4. Set Up for Integration/Feature Tests
```bash
# Copy the environment template
cp env.testing .env

# Edit .env with your real API credentials
# MARZPAY_API_KEY=your_real_api_key
# MARZPAY_API_SECRET=your_real_api_secret
```

### 5. Run Integration Tests
```bash
php run-tests.php integration
```

### 6. Run Feature Tests
```bash
php run-tests.php feature
```

### 7. Run All Tests
```bash
php run-tests.php all
```

## Test Commands

| Command | Description |
|---------|-------------|
| `php run-tests.php unit` | Run unit tests only |
| `php run-tests.php integration` | Run integration tests (needs real credentials) |
| `php run-tests.php feature` | Run feature tests (needs real credentials) |
| `php run-tests.php all` | Run all tests |
| `php run-tests.php coverage` | Run tests with code coverage report |
| `php run-tests.php quick` | Run quick validation test |
| `php run-tests.php help` | Show help message |

## Environment Variables

Create a `.env` file with the following variables:

```env
# API Configuration
MARZPAY_API_KEY=your_api_key_here
MARZPAY_API_SECRET=your_api_secret_here
MARZPAY_BASE_URL=https://wallet.wearemarz.com/api/v1
MARZPAY_TIMEOUT=30

# Testing Configuration
MARZPAY_TEST_MODE=true
MARZPAY_TEST_PHONE=0759983853
MARZPAY_TEST_AMOUNT=1000
```

## Test Coverage

Generate code coverage reports:

```bash
php run-tests.php coverage
```

This will create an HTML coverage report in the `coverage/` directory.

## Continuous Integration

For CI/CD pipelines, you can run tests with:

```bash
# Run unit tests only (no external dependencies)
vendor/bin/phpunit tests/Unit

# Run with coverage
vendor/bin/phpunit --coverage-clover coverage.xml
```

## Testing Without Real API Credentials

If you don't have real API credentials, you can still:

1. **Run unit tests** - These test the SDK logic without API calls
2. **Run quick test** - Basic validation without external dependencies
3. **Review test code** - Understand expected behavior and API structure

## Troubleshooting

### Common Issues

1. **Authentication Errors**
   - Ensure your API credentials are correct
   - Check that the credentials are properly set in `.env`
   - Verify your account has the necessary permissions

2. **Network Errors**
   - Check your internet connection
   - Verify the API base URL is correct
   - Check if there are firewall restrictions

3. **Test Failures**
   - Review the error messages carefully
   - Check if it's a validation error or API error
   - Ensure test data (phone numbers, amounts) are valid

### Debug Mode

Enable debug mode by setting:
```env
APP_DEBUG=true
```

This will provide more detailed error information.

## Test Data

The tests use the following test data:
- **Test Phone Number**: `0759983853` (can be configured via `MARZPAY_TEST_PHONE`)
- **Test Amount**: `1000` UGX (can be configured via `MARZPAY_TEST_AMOUNT`)

**Note**: Integration and feature tests may make actual API calls, so ensure you're using appropriate test data.

## Best Practices

1. **Always run unit tests first** - They're fast and don't require external dependencies
2. **Use real credentials only for integration/feature tests** - Never commit real credentials
3. **Review test output carefully** - Tests provide valuable information about SDK behavior
4. **Run coverage reports regularly** - Ensure your code changes are well-tested
5. **Keep test data minimal** - Use small amounts for financial transactions in tests

## Support

If you encounter issues with testing:

1. Check this documentation
2. Review the test output and error messages
3. Ensure your environment is properly configured
4. Contact support with specific error details

For more information, see the main [README.md](README.md) file.
