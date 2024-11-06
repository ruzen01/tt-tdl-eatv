.PHONY: help
help: ## Показать это сообщение
		@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

.PHONY: install
install: ## Установка зависимостей
		composer install

.PHONY: test
test: ## Запуск тестов
		php artisan test

.PHONY: test-coverage
test-coverage: ## Запуск тестов с покрытием
		XDEBUG_MODE=coverage php artisan test --coverage

.PHONY: lint
lint: ## Проверка кода линтером
		./vendor/bin/php-cs-fixer fix --dry-run --diff

.PHONY: lint-fix
lint-fix: ## Исправление ошибок линтера
		./vendor/bin/php-cs-fixer fix

.PHONY: serve
serve: ## Запуск локального сервера
		php artisan serve

.PHONY: migrate
migrate: ## Запуск миграций
		php artisan migrate

.PHONY: rollback
rollback: ## Откат миграций
		php artisan migrate:rollback

.PHONY: seed
seed: ## Заполнение базы данных тестовыми данными
		php artisan db:seed

.PHONY: cache-clear
cache-clear: ## Очистка кэша
		php artisan cache:clear
		php artisan config:clear
		php artisan route:clear
		php artisan view:clear

.PHONY: ide-helper
ide-helper: ## Генерация файлов для поддержки IDE
		php artisan ide-helper:generate
		php artisan ide-helper:meta
		php artisan ide-helper:models --nowrite