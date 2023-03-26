.PHONY: *

clear:
	@php bin/console cache:clear --no-optional-warmers -vv

all:
	@php bin/console cache:clear

update:
	@composer update
	@$(MAKE) assets
	@$(MAKE) clear

assets:
	@yarn install
	@[ -L node_modules ] || (rm -rf var/modules)
	@[ -L node_modules ] || (mv node_modules var/modules)
	@[ -L node_modules ] || (ln -s var/modules node_modules)

dev: development
development: assets
ifdef FORCE
	@$(MAKE) dist-clean
	@echo "APP_ENV=dev" >> .env
	@sed -i "/APP_DEBUG=/d" .env
ifdef DEBUG
	@echo "APP_DEBUG=$(DEBUG)" >> .env
else
	@echo "APP_DEBUG=1" >> .env
endif
	@$(MAKE) clear
endif
	@yarn run watch

prod: production
production: assets
ifdef FORCE
	@$(MAKE) dist-clean
	@echo "APP_ENV=prod" >> .env
	@sed -i "/APP_DEBUG=/d" .env
ifdef DEBUG
	@echo "APP_DEBUG=$(DEBUG)" >> .env
else
	@echo "APP_DEBUG=0" >> .env
endif
	@$(MAKE) clear
endif
	@yarn run prod

linter:
	@echo "Not implemented yet."
tests:
	@echo "Not implemented yet."

database: db
db:
	@php bin/console doctrine:schema:update --force --complete

database-charset: db-charset
db-charset:
	@php bin/console doctrine:schema:charset --update --update-tables

doom:
	@$(RM) -rf var/cache

clean: doom
	@$(RM) -rf var/log

dist-clean: clean
	@$(RM) -rf var/modules node_modules
	@$(RM) -rf package-lock.json yarn.lock
	@sed -i "/APP_ENV=/d" .env
	@sed -i "/APP_DEBUG=/d" .env
