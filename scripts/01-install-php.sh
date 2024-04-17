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
sudo apt install php-cli -y
sudo apt install php-common -y
sudo apt install php-curl -y
sudo apt install php-zip -y
sudo apt install php-gd -y
sudo apt install php-mysql -y
sudo apt install php-xml -y
sudo apt install php-mbstring -y
sudo apt install php-json -y
sudo apt install php-intl -y
sudo apt install php-bcmath -y
sudo apt install php-fpm -y
sudo apt install php-sqlite -y

# Inicia o serviço do PHP-FPM
sudo systemctl start php8.3-fpm.service

# Habilita o serviço do PHP-FPM para inicializar no boot do SO
sudo systemctl enable php8.3-fpm.service

echo -e '\n PHP INSTALADO!\n'