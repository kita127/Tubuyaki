
#== help
help:
	@grep -A1 -E '^#==' Makefile


#== initialize
init:
	docker compose up -d --build
	docker compose exec apache composer install
	cp ./webapp/.env.example ./webapp/.env
	docker compose exec apache php artisan key:generate
	docker compose exec apache php artisan key:generate --env=testing
	docker compose exec apache npm install
	docker compose exec apache php artisan migrate --seed
	docker compose exec apache php artisan migrate --seed --env=testing

#== terminate
down:
	docker compose down --rmi all --volumes --remove-orphans

#== test
test:
	docker compose exec apache php artisan test --env=testing
