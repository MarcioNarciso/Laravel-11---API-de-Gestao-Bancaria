#!/usr/bin/env bash

echo -e '\n PREPARANDO O PROJETO...\n'

DIR_ATUAL=$(dirname $0)

# Instala as dependências do projeto
php $DIR_ATUAL/../composer.phar update

# Executa as migrações
php artisan migrate

# Executa as otimizações
php artisan optimize
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Copia o arquivo de configuração do server block para o diretório padrão do Nginx
sudo cp $DIR_ATUAL/nginx-server-block/gestao-bancaria /etc/nginx/sites-available

# Cria o symlink para a pasta sites-enabled
sudo ln -s /etc/nginx/sites-available/gestao-bancaria /etc/nginx/sites-enabled

# Cria o symlink do projeto para a pasta de onde o Nginx irá servi-lo.
DIR_PROJETO=$(readlink -f .)
sudo ln -s $DIR_PROJETO /var/www/

# Adiciona o host
sudo bash -c "echo -e '\n127.0.0.99  gestao-bancaria' >> /etc/hosts"

# Reinicia o Nginx
sudo systemctl restart nginx

# Reinicia o PHP-FPM
sudo systemctl restart php8.3-fpm

echo -e '\nOK!\n'