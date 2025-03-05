.DEFAULT_GOAL := help
.PHONY: help

help: ## show this help
	@printf "%-20s %s\n" "Target" "Description"
	@printf "%-20s %s\n" "------" "-----------"
	@sed -rn 's/^([a-zA-Z_-]+):.*?## (.*)$$/"\1" "\2"/p' < $(MAKEFILE_LIST) | sort | xargs printf "%-20s %s\n"

cc: ## clear cache
	./bin/console c:c

cc-prod: ## clear cache
	./bin/console c:c --env=prod

importmap_install: ## importmap:install
	./bin/console importmap:install

sass: ## sass:build
	./bin/console sass:build

assetmap_compile: ## asset-map:compile
	./bin/console asset-map:com

assetmap_compile-prod: ## asset-map:compile
	./bin/console asset-map:com --env=prod

var-permissions: ## set var folder to www-data user
	chown -R 33:33 var/

clearPublic:
	rm -rf public/assets/*

rebuild-local: clearPublic cc importmap_install sass var-permissions

rebuild-prod: cc-prod importmap_install sass assetmap_compile-prod var-permissions

twig-lint: ## linting and fixing twig templates
	vendor/vincentlanglet/twig-cs-fixer/bin/twig-cs-fixer --fix fix templates/

php-cs-fix: ## PHP-CS-Fixer
	vendor/bin/php-cs-fixer fix

phpunit: ## PHPUnit
	vendor/bin/phpunit

phpstan: ## Run PHPStan
	vendor/bin/phpstan analyse src

qa: php-cs-fix phpstan twig-lint phpunit## Run all QA tasks
