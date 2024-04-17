#!/usr/bin/env bash

echo -e '\n PREPARANDO O PROJETO...\n'

DIR_ATUAL=$(dirname "$0")

# Altera o diretório de trabalho
cd "$DIR_ATUAL"/../

# Instala as dependências do projeto
php composer.phar install

# Executa as migrações
php artisan migrate --force

# Executa as otimizações
php artisan optimize
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Cria um novo arquivo dot env para produção
cp .env.example .env

# Gera uma nova chave para produção
php artisan key:generate

# Copia o arquivo de configuração do server block para o diretório padrão do Nginx
sudo cp "$DIR_ATUAL"/scripts/nginx-server-block/gestao-bancaria /etc/nginx/sites-available

# Cria o symlink para a pasta sites-enabled
sudo ln -s /etc/nginx/sites-available/gestao-bancaria /etc/nginx/sites-enabled

# Remove o default de sites-enabled
sudo rm /etc/nginx/sites-enabled/default

# Cria o symlink do projeto para a pasta de onde o Nginx irá servi-lo.
DIR_PROJETO=$(readlink -f .)
sudo ln -s "$DIR_PROJETO" /var/www/

# 
sudo chown -R $USER:www-data "$DIR_PROJETO"

# Atribui permissão de escrita no diretório storage
sudo chmod 777 -R "$DIR_PROJETO"/storage/

# Define as permissões para o Nginx poder acessar a pasta do projeto.
sudo chmod 755 /home
sudo chmod 755 /home/$USER

# Adiciona o host
#sudo bash -c "echo -e '\n127.0.0.99  gestao-bancaria' >> /etc/hosts"

# Reinicia o Nginx
sudo systemctl restart nginx

# Reinicia o PHP-FPM
sudo systemctl restart php8.3-fpm

echo -e '\nOK!\n'