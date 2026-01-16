setup:
	docker compose exec app composer install
	docker compose exec app npm install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate --seed
	docker compose exec app npm run build
