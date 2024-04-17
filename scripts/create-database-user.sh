#!/usr/bin/env bash

# Cria o usuário já com as permissões de acesso ao banco da aplicação.
sudo mysql <<MYSQL_SCRIPT
CREATE USER 'gestao_bancaria'@'localhost' IDENTIFIED BY 'laravel';
GRANT ALL PRIVILEGES ON SISTEMA_GESTAO_BANCARIA.* TO 'gestao_bancaria'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

echo -e '\nUSUÁRIO DO BANCO CRIADO!!!\n'

