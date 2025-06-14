# Contributing to Sitewise

Thank you for your interest in contributing to Sitewise! We welcome contributions from the community and are pleased to have you join us.

## 🚀 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- Docker (optional, for containerized development)

### Development Setup

1. **Fork and clone the repository**
   ```bash
   git clone https://github.com/your-username/sitewise.git
   cd sitewise
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

5. **Start development server**
   ```bash
   composer run dev
   ```

## 🛠️ Development Guidelines

### Code Style

- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting: `./vendor/bin/pint`
- Write meaningful commit messages
- Add tests for new features

### Testing

- Run tests before submitting: `php artisan test`
- Write tests for new functionality
- Ensure all tests pass

### Pull Request Process

1. **Create a feature branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes**
   - Write clean, documented code
   - Add tests if applicable
   - Update documentation if needed

3. **Test your changes**
   ```bash
   php artisan test
   ./vendor/bin/pint
   ```

4. **Commit and push**
   ```bash
   git add .
   git commit -m "Add: your feature description"
   git push origin feature/your-feature-name
   ```

5. **Create a Pull Request**
   - Provide a clear description of changes
   - Reference any related issues
   - Ensure CI checks pass

## 📋 Issue Guidelines

### Reporting Bugs

When reporting bugs, please include:

- **Environment details** (PHP version, Laravel version, OS)
- **Steps to reproduce** the issue
- **Expected behavior** vs **actual behavior**
- **Error messages** or logs if applicable
- **Screenshots** if relevant

### Feature Requests

For feature requests, please:

- **Describe the feature** and its use case
- **Explain the benefits** to users
- **Consider implementation** complexity
- **Check existing issues** to avoid duplicates

## 🏗️ Architecture Guidelines

### Multi-Tenancy

- All database queries must be scoped to the current site
- Use `app()->site` helper for tenant context
- Test multi-tenant isolation thoroughly

### FilamentPHP Integration

- Follow Filament conventions for resources
- Use proper authorization and scoping
- Maintain consistent UI/UX patterns

### Performance

- Consider caching implications
- Optimize database queries
- Test with multiple sites/domains

## 🤝 Community

- Be respectful and inclusive
- Help others in discussions
- Share knowledge and best practices
- Follow our [Code of Conduct](CODE_OF_CONDUCT.md)

## 📄 License

By contributing to Sitewise, you agree that your contributions will be licensed under the MIT License.

## 🙋‍♀️ Questions?

If you have questions about contributing, feel free to:

- Open an issue for discussion
- Join our community discussions
- Contact the maintainers

Thank you for contributing to Sitewise! 🎉
