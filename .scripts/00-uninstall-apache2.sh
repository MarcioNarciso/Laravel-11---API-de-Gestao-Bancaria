#!/usr/bin/env bash

# Verificando se já está instalado.
which php &> /dev/null

if [ $? -ne 0 ]
then
    echo -e '\nO APACHE JÁ ESTÁ DESINSTALADO!'
    return 1
fi

echo -e '\n DESINSTALANDO O APACHE...\n'

sudo apt remove --purge apache2* -y
sudo apt autoremove -y

echo -e '\n APACHE DESINSTALADO!\n'