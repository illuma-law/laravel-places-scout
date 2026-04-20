# Contributing

Contributions are welcome and will be fully credited.

## Pull Requests

We accept pull requests via GitHub. Here are the steps:

1. Fork the repository
2. Create a new branch for your feature or fix
3. Write tests for your changes
4. Ensure all tests pass: `composer test`
5. Ensure static analysis passes: `composer analyse`
6. Format your code: `composer format`
7. Submit a pull request

## Requirements

- **PHP 8.5+** - All code must be compatible with PHP 8.5 and higher
- **Laravel 11/12/13** - The package must work with all supported Laravel versions
- **Tests** - All changes must include tests and maintain 100% coverage
- **Type Safety** - All code must pass PHPStan Level 5 analysis
- **Strict Types** - All PHP files must include `declare(strict_types=1)`

## Coding Standards

We use [Laravel Pint](https://laravel.com/docs/pint) for code formatting. Run it before submitting:

```bash
composer format
```

## Testing

All tests are written with [Pest](https://pestphp.com/):

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage
```

## Static Analysis

We use PHPStan with Larastan for static analysis:

```bash
composer analyse
```

## Architecture Tests

We have architecture tests that enforce dependency rules. Run them with:

```bash
vendor/bin/pest tests/Architecture
```

## Reporting Security Vulnerabilities

If you discover any security-related issues, please email ai@illuma.law instead of using the issue tracker.

## Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code:

- Be respectful and inclusive
- Welcome newcomers
- Focus on constructive feedback
- Accept responsibility for your mistakes

Thank you for contributing!
