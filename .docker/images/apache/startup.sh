#!/usr/bin/env sh

# Instala as dependências do projeto
php composer.phar install

# Gera uma nova chave para produção
php artisan key:generate

# Cria um symlink de public/storage para storage/app/public
php artisan storage:link

# Gera a documentação do Swagger
php artisan l5-swagger:generate

# Otimiza os caches
php artisan optimize

echo 'Waiting for connection to the database...'

# A execução do script esperará até a conexão com o banco estiver disponível
output=`php artisan app:wait-for-mysql --t 10 --n 6`

if [ $? -eq 1 ]; then
    echo $output
    return 1
fi

echo $output

# Realiza as migrações no banco e cria o cria se não existir
php artisan migrate --force

# Inicializa o serviço do Apache
apache2-foreground