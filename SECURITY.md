# Security Policy

## Supported Versions

We actively maintain and provide security updates for the following versions of the MarzPay PHP SDK:

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |
| < 1.0   | :x:                |

## 🚨 Reporting a Vulnerability

We take security seriously. If you discover a security vulnerability in the MarzPay PHP SDK, please follow these steps:

### 1. **DO NOT** Create a Public Issue

For security vulnerabilities, please **do not** create a public GitHub issue. This could put users at risk.

### 2. **Email Us Directly**

Send an email to: **security@wearemarz.com**

Include the following information:
- **Description**: Detailed description of the vulnerability
- **Impact**: How this vulnerability could be exploited
- **Steps to reproduce**: Clear steps to reproduce the issue
- **Environment**: PHP version, SDK version, operating system
- **Proof of concept**: If applicable, include a minimal proof of concept
- **Your contact information**: So we can follow up with you

### 3. **Response Timeline**

We will respond to security reports within:
- **Initial response**: 24-48 hours
- **Status update**: Within 1 week
- **Resolution**: As quickly as possible (typically 1-4 weeks depending on severity)

### 4. **Recognition**

If you are the first to report a security vulnerability and we fix it, we will:
- Credit you in our security advisory
- Add you to our security hall of fame (if desired)
- Consider additional recognition for significant contributions

## Security Best Practices

### For SDK Users

1. **Keep Updated**: Always use the latest version of the SDK
2. **Secure Credentials**: Never commit API keys or secrets to version control
3. **Use HTTPS**: Always use HTTPS endpoints in production
4. **Validate Input**: Validate and sanitize all user input
5. **Error Handling**: Implement proper error handling without exposing sensitive information
6. **Environment Variables**: Store credentials in environment variables, not in code

### Example Secure Configuration

```php
// ✅ Good: Use environment variables
$marzpay = new MarzPay([
    'api_key' => $_ENV['MARZPAY_API_KEY'],
    'api_secret' => $_ENV['MARZPAY_API_SECRET'],
    'base_url' => 'https://wallet.wearemarz.com/api/v1'
]);

// ❌ Bad: Hardcoded credentials
$marzpay = new MarzPay([
    'api_key' => 'your_api_key_here',
    'api_secret' => 'your_secret_here'
]);
```

### Laravel Security

```php
// ✅ Good: Laravel configuration
// config/marzpay.php
return [
    'api_key' => env('MARZPAY_API_KEY'),
    'api_secret' => env('MARZPAY_API_SECRET'),
];

// .env file (not committed to version control)
MARZPAY_API_KEY=your_api_key_here
MARZPAY_API_SECRET=your_secret_here
```

## 🔐 Authentication Security

### API Key Security

- **Rotate Regularly**: Change your API keys periodically
- **Limit Scope**: Use keys with minimal required permissions
- **Monitor Usage**: Regularly review API key usage
- **Revoke Compromised Keys**: Immediately revoke any compromised keys

### Webhook Security

When implementing webhooks, verify signatures:

```php
function verifyWebhookSignature(Request $request): bool
{
    $signature = $request->header('X-MarzPay-Signature');
    $payload = $request->getContent();
    $secret = config('marzpay.webhook_secret');
    
    $expectedSignature = hash_hmac('sha256', $payload, $secret);
    
    return hash_equals($expectedSignature, $signature);
}
```

## 🚫 Known Security Considerations

### 1. **API Credentials**

- API credentials are sensitive and should be treated like passwords
- Never log API credentials or include them in error messages
- Use environment variables or secure configuration management

### 2. **Phone Number Privacy**

- Phone numbers are considered personal data
- Implement proper data protection measures
- Follow GDPR and local privacy regulations

### 3. **Transaction Data**

- Transaction data may contain sensitive financial information
- Implement proper access controls
- Use encryption for data at rest

### 4. **Error Messages**

- Don't expose internal system details in error messages
- Log detailed errors server-side, return generic messages to clients

## Security Audit

### For Developers

We regularly audit our code for security issues:

1. **Static Analysis**: Using PHPStan and similar tools
2. **Dependency Scanning**: Regular updates of dependencies
3. **Code Review**: All code changes are reviewed
4. **Penetration Testing**: Regular security testing

### For Users

You can help by:

1. **Reporting Issues**: Report any security concerns
2. **Keeping Updated**: Use the latest SDK version
3. **Following Best Practices**: Implement recommended security measures

## Contact

For security-related questions or concerns:

- **Security Email**: security@wearemarz.com
- **General Support**: support@wearemarz.com
- **Documentation**: [MarzPay API Documentation](https://wallet.wearemarz.com/documentation)

## Security Checklist

Before using the SDK in production:

- [ ] Using latest SDK version
- [ ] API credentials stored securely
- [ ] HTTPS endpoints configured
- [ ] Input validation implemented
- [ ] Error handling in place
- [ ] Webhook signatures verified (if using webhooks)
- [ ] Access controls implemented
- [ ] Logging configured (without sensitive data)
- [ ] Regular security updates planned

## 🏆 Security Hall of Fame

We recognize security researchers who help improve the security of our SDK:

- *No vulnerabilities reported yet - be the first!*

---

**Thank you for helping keep MarzPay PHP SDK secure!**

*Last updated: October 15, 2025*
