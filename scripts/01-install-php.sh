#!/usr/bin/env bash

# Verificando se já está instalado.
which php &> /dev/null

if [ $? -eq 0 ]
then
    echo -e '\nO PHP JÁ ESTÁ INSTALADO!'
    return 1
fi

echo -e '\n INSTALANDO O PHP...\n'

# Atualiza o sistema
sudo apt update -y && sudo apt upgrade -y

# Adiciona o repositório PPA Ondrej
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y

# Instala a versão mais recente do PHP
sudo apt install php -y

# Instala extensões comuns do PHP e o PHP-FPM
sudo apt install php-{cli,common,curl,zip,gd,mysql,xml,mbstring,json,intl,bcmath,fpm,sqlite} -y

# Inicia o serviço do PHP-FPM
sudo systemctl start php8.3-fpm.service

# Habilita o serviço do PHP-FPM para inicializar no boot do SO
sudo systemctl enable php8.3-fpm.service

echo -e '\n PHP INSTALADO!\n'