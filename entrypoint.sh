#!/bin/bash
set -e

echo "Aguardando o banco de dados ($DB_HOST:$DB_PORT) ficar pronto..."

# Healthcheck nativo do banco de dados utilizando PHP PDO em vez de pg_isready
php -r "
\$host = getenv('DB_HOST') ?: 'postgres';
\$port = getenv('DB_PORT') ?: '5432';
\$db = getenv('DB_DATABASE') ?: 'laravel_db';
\$user = getenv('DB_USERNAME') ?: 'laravel_user';
\$pass = getenv('DB_PASSWORD') ?: 'secret';

\$dsn = 'pgsql:host=' . \$host . ';port=' . \$port . ';dbname=' . \$db;

\$maxRetries = 30;
\$retryCount = 0;

while (\$retryCount < \$maxRetries) {
    try {
        \$pdo = new PDO(\$dsn, \$user, \$pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo 'Conexão com PostgreSQL estabelecida!' . PHP_EOL;
        exit(0);
    } catch (PDOException \$e) {
        \$retryCount++;
        echo 'PostgreSQL não está pronto ainda. Tentativa ' . \$retryCount . '/' . \$maxRetries . '...' . PHP_EOL;
        sleep(2);
    }
}
echo 'Erro: Tempo esgotado aguardando o banco de dados.' . PHP_EOL;
exit(1);
"

# Roda as migrations automaticamente após a estabilização do banco
echo "Executando migrations do banco de dados..."
php artisan migrate --force
php artisan tenants:migrate --force

# Limpa/Otimiza os caches do Laravel caso esteja em modo de produção
if [ "$APP_ENV" = "production" ]; then
    echo "Otimizando aplicação para Produção..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo "Iniciando a aplicação com RoadRunner..."
exec "$@"
