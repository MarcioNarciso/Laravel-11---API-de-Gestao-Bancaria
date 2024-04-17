#!/usr/bin/env bash

# Verificando se já está instalado.
which nginx &> /dev/null

if [ $? -ne 0 ]
then
    echo -e '\nO NGINX JÁ ESTÁ INSTALADO!'
    exit 1
fi

echo -e '\n INSTALANDO O NGINX...\n'

# Atualiza o sistema
sudo apt update -y && sudo apt upgrade -y

# Instala o NginX
sudo apt install nginx -y

echo -e "\n\nEXECUTE O COMANDO 'sudo ufw app list' PARA VER OS APPs DISPONÍVEIS E, DEPOIS, LIBERE O NGINX NO FIREWALL COM 'sudo ufw allow 'Nginx Full''.\n\n"

echo -e '\n NGINX INSTALADO!\n'
