run_server:
	docker-compose run rpc_server php /var/app/server.php

deps: api_deps db_service_deps

api_deps:
	docker-compose run composer composer install -d /var/app/api

db_service_deps:
	docker-compose run composer composer install -d /var/app/db_service
