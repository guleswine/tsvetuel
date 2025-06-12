
### install

```
sudo chown -R 1000:1000 /var/www/tsvetuel
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan migrate
```
