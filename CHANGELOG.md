# Changelog

All notable changes to the MarzPay PHP SDK will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-10-15

### Added
- Initial release of MarzPay PHP SDK
- Complete Collections API support (collect money from customers)
- Complete Disbursements API support (send money to recipients)
- Phone Verification API support (verify phone numbers and get user info)
- Laravel integration with ServiceProvider and Facade
- Automatic UUID v4 generation for transaction references
- Automatic phone number formatting for Uganda numbers
- Comprehensive error handling with MarzPayException
- Support for webhook callbacks
- Multipart form data support for collections and disbursements
- JSON request support for phone verification
- Laravel auto-discovery support (Laravel 5.5+)
- Comprehensive documentation:
  - Complete README.md with usage examples
  - Laravel integration guide (README-LARAVEL.md)
  - Detailed API reference (API-REFERENCE.md)
  - Testing guide (README-TESTING.md)
- Full test suite with 27 unit tests
- PHPUnit configuration and test runner
- Composer package configuration
- MIT License
- Environment configuration support
- Security best practices with .gitignore

### Features
- **Collections API**:
  - `collectMoney()` - Initiate payment collection
  - `getServices()` - Get available collection services
  - `getCollectionDetails()` - Get collection details by UUID
- **Disbursements API**:
  - `sendMoney()` - Send money to recipients
  - `getServices()` - Get available disbursement services
  - `getSendMoneyDetails()` - Get disbursement details by UUID
- **Phone Verification API**:
  - `verifyPhoneNumber()` - Verify phone numbers and get user info
  - `getServiceInfo()` - Get service information
  - `getSubscriptionStatus()` - Check subscription status
- **Laravel Integration**:
  - ServiceProvider for automatic registration
  - Facade for clean API access
  - Configuration file with environment support
  - Auto-discovery support for Laravel 5.5+

### Technical Details
- PHP 8.0+ support
- Guzzle HTTP client for API requests
- Ramsey UUID library for reference generation
- PSR-4 autoloading
- Comprehensive error handling
- Type safety with proper exception handling
- Security-first approach with proper authentication

### Documentation
- Complete API documentation with examples
- Laravel-specific integration guide
- Testing setup and examples
- Security best practices
- Error handling guidelines
- Webhook integration examples

### Testing
- 27 unit tests covering all functionality
- Integration test examples
- PHPUnit configuration
- Test runner script with multiple test modes
- Code coverage support (when Xdebug is available)

### Verified Functionality
- ✅ Phone verification working: JOHN MUSINGUZI (0759983853)
- ✅ Collections working: 1,000.00 UGX via Airtel
- ✅ Disbursements working: 1,000.00 UGX to 0759983853
- ✅ All API endpoints tested with real MarzPay API
- ✅ Laravel integration verified and working
- ✅ Error handling tested and working
- ✅ UUID generation working
- ✅ Phone number formatting working

---

## [Unreleased]

### Planned
- Payment Links API support
- Webhooks API enhancements
- Additional provider support
- Enhanced error messages
- More comprehensive examples
- Performance optimizations

### Known Issues
- None at this time

---

**For more information, visit [MarzPay API Documentation](https://wallet.wearemarz.com/documentation)**
