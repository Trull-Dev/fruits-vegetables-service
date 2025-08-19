.PHONY: install migrate test test-integration all

# Default PHP binary
PHP = php

# Composer binary
COMPOSER = composer

# Default target
all: install migrate test

# Install dependencies
install:
	$(COMPOSER) install

# Run database migrations
migrate:
	$(PHP) bin/console doctrine:migrations:migrate --no-interaction

# Run all tests except RequestJsonTest
test:
	$(PHP) bin/phpunit --exclude-group functionality-test

# Run only RequestJsonTest
test-integration:
	$(PHP) bin/phpunit --filter RequestJsonTest

# Help command
help:
	@echo "Available commands:"
	@echo "  make install          - Install composer dependencies"
	@echo "  make migrate         - Run database migrations"
	@echo "  make test           - Run all tests except RequestJsonTest"
	@echo "  make test-integration - Run only RequestJsonTest"
	@echo "  make all            - Run install, migrate, and test"