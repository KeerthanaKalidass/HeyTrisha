# Contributing to HeyTrisha

Thank you for your interest in contributing to HeyTrisha! We welcome contributions from the community.

## How to Contribute

### Reporting Bugs

1. **Check existing issues** - Before creating a new issue, please check if the bug has already been reported.
2. **Create a detailed issue** - Include:
   - A clear description of the bug
   - Steps to reproduce
   - Expected behavior vs actual behavior
   - Your environment (WordPress version, PHP version, etc.)

### Suggesting Features

1. **Open an issue first** - Before working on a new feature, please open an issue to discuss it.
2. **Describe your use case** - Explain why this feature would be useful.

### Pull Requests

1. **Open an issue first** - Discuss your proposed changes before starting work.
2. **Fork the repository** - Create your own fork to work on.
3. **Create a feature branch** - Branch from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   ```
4. **Make your changes** - Follow the coding standards below.
5. **Write/update tests** - Ensure your changes are covered by tests.
6. **Run tests** - Make sure all tests pass:
   ```bash
   cd api
   ./vendor/bin/phpunit
   ```
7. **Submit a pull request** - Include a clear description of your changes.

## Development Setup

1. Clone the repository
2. Install dependencies:
   ```bash
   cd api
   composer install
   ```
3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```
4. Run tests:
   ```bash
   ./vendor/bin/phpunit
   ```

## Coding Standards

- **PHP**: Follow PSR-12 coding standards
- **JavaScript/React**: Use ESLint
- **Comments**: Add comments for complex logic
- **Commits**: Write clear, descriptive commit messages

## Testing

All contributions must include appropriate tests:

- **Unit tests** for isolated logic (e.g., intent detection, query parsing)
- **Feature tests** for API endpoints

Run the test suite with:
```bash
cd api
./vendor/bin/phpunit
```

## Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on the code, not the person

## Questions?

If you have questions, feel free to:
- Open an issue on GitHub
- Contact: me@manikandanc.com

Thank you for contributing! 🎉
