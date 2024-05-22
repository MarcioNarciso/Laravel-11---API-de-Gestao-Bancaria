#!/usr/bin/env bash

# Verificando se já está instalado.
which mysql &> /dev/null

if [ $? -eq 0 ]
then
    echo -e '\nO MySQL JÁ ESTÁ INSTALADO!'
    return 1
fi

echo -e '\n INSTALANDO O MySQL...\n'

# Instalando o servidor do MySQL
sudo apt update -y && sudo apt upgrade -y
sudo apt install mysql-server -y

# Iniciando o serviço do MySQL
sudo systemctl start mysql

# Habilitando a inicialização do serviço junto com o boot do SO
sudo systemctl enable mysql

echo -e '\n MySQL INSTALADO!\n'

echo -e '\n POR FAVOR, REINICIE O S.O. PARA UMA CORRETA EXECUÇÃO DOS PRÓXIMOS SCRIPTS.\n'