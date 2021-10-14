help: ## Show this help
	@echo Usage: make [target]
	@echo
	@echo "Test env: $(TEST_ENV)"
	@echo
	@echo "Targets:"
	@grep -E '^[a-zA-Z_-]+:.*?## [^ :]+[^:] .*$$' $(MAKEFILE_LIST) | LC_ALL=C sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-20s %s\n", $$1, $$2}'
	@echo
	@echo "Docker Targets:"
	@grep -E '^[a-zA-Z_-]+:.*?## Docker: .*$$' $(MAKEFILE_LIST) | LC_ALL=C sort | awk 'BEGIN {FS = ":.*?## Docker: "}; {printf "  %-20s %s\n", $$1, $$2}'
	@echo

.PHONY: help cache deploy migrate start stop restart database


cache: ## Clean cache in docker
	bin/dc_exec_check "/app/bin/cache"

migrate: ## Re-create database
	bin/dc_exec_check "/app/bin/migrate"

start: ## Docker: start containers
	bin/dc_start

stop: ## Docker: stop containers
	bin/dc_stop

restart: stop start ## Docker: restart containers

database: ## Docker: Re-create database
	bin/dc_database

recreate: ## Docker: recreate containers
	chmod +x bin/*
	chmod -R 0777 bin/*
	bin/dc_recreate
