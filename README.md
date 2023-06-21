
## About project

This application allows users to upload doc/docx files with variables, set values of them and convert file to PDF

### Instaliation

- clone project
- cp .env.example .env
- set DOCKER_HTTP_PORT (default 8085)
- docker-compose build app
- docker-compose exec app composer install
- docker-compose exec app php artisan key:generate
- open http://localhost:DOCKER_HTTP_PORT
