COMPOSER ?= composer

.DEFAULT_GOAL := all

.PHONY: all
all: install test ## Install dependencies and run the full suite

.PHONY: install
install: ## Install dependencies
	$(COMPOSER) install --no-interaction

.PHONY: update
update: ## Update dependencies and refresh the lock file
	$(COMPOSER) update --no-interaction

.PHONY: test
test: ## Run every quality gate
	$(COMPOSER) test

.PHONY: phpcs
phpcs: ## Check coding standard
	$(COMPOSER) phpcs

.PHONY: phpcbf
phpcbf: ## Fix what the coding standard can fix automatically
	$(COMPOSER) phpcbf

.PHONY: phpmd
phpmd: ## Check cyclomatic complexity
	$(COMPOSER) phpmd

.PHONY: phpstan
phpstan: ## Run static analysis
	$(COMPOSER) phpstan

.PHONY: phpunit
phpunit: ## Run the tests with coverage
	$(COMPOSER) phpunit

.PHONY: audit
audit: ## Check the lock file for known advisories
	$(COMPOSER) audit --locked

.PHONY: example
example: install ## Run the usage example
	php example.php

.PHONY: clean
clean: ## Remove installed dependencies and build artifacts
	rm -rf vendor .phpunit.cache .phpunit.result.cache .phpcs.cache clover.xml

.PHONY: help
help: ## List the available targets
	@grep -hE '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'
