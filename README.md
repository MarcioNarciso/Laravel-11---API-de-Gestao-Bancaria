# API de Gestão Bancária

O desafio consistia em criar dois endpoints: "/conta" e "/transacao".

O endpoint "/conta" deveria criar e fornecer informações sobre o número da conta
e o saldo. O endpoint "/transacao" seria responsável por realizar diversas 
operações financeiras.

A API implementada para esse desafio pode ser acessada pelo link: 
[API de Gestão Bancária](http://api-bancaria.marcionarciso.dev.br:8080/).

### Padrões de Projeto Utilizados

Nesse projeto, utilizei o padrão de projeto Template Method junto com o 
Factory Method (simplificado) para flexibilizar a criação de novas formas de 
pagamento e melhorar a coesão e acoplamento. Dessa forma, requer pouca alteração 
no código para adicionar uma nova forma de pagamento.

Tentei aproveitar os benefícios do Active Record (já utilizado pelo Laravel) para 
manter uma boa coesão.

Utilizei o padrão Builder com Director para simplificar a criação de objetos para
os testes automatizados.


## Pré-requisitos
Para executar esse projeto, é necessário já ter instalado no sistema:

* PHP 8.3
* Laravel 11 (Suas dependências)
* Nginx
* MySQL

## Scripts de Setup do Ambiente

Caso queira realizar o deploy desse projeto em um distribuição do Linux baseada
no Debian, na raiz do projeto há a pasta "scripts/" que contém Shell Scripts 
enumerados para auxiliar na preparação do ambiente:

0. <b>00-uninstall-apache2.sh</b> : Opcional. Remove a instalação atual do Apache 
para não dar possível conflito com o Nginx;
1. <b>01-install-php.sh</b> : Instala a versão mais atual do PHP junto com as 
extensões necessárias para esse projeto;
2. <b>02-install-mysql.sh</b> : Instala o MySQL 8;
3. <b>03-create-database-user.sh</b> : Cria no MySQL o usuário de acesso 
utilizado pela aplicação;
4. <b>04-install-nginx.sh</b> : Instala o Nginx.

## Scripts de Implantação

5. <b>05-prepare-project.sh</b> : Realiza as preparações finais para a execução 
da aplicação, como a instalação das dependências, a criação do servidor virtual 
no Nginx, etc.

A pasta "nginx-server-block/" contém o arquivo de um servidor virtual 
pré-configurado do Nginx.

## Implantação Manual

### Banco de Dados
Conecte-se ao seu servidor do MySQL e crie o usuário "gestao_bancaria" que tenha 
a senha "laravel" (ou qualquer outro usuário que se queira) e dê a ele todas 
permissões no banco "SISTEMA_GESTAO_BANCARIA".


### Preparando o .env
Faça uma cópia do arquivo ".env.example" com o nome de ".env" na raiz do projeto 
e altere as variáveis de ambiente (p. ex., conexão com o banco, URL da aplicação, 
fuso horário, etc) nesse novo arquivo conforme o seu ambiente.


### Instalando as Dependências do Projeto
Com o PHP instalado, execute o comando a seguir na raiz do projeto.

```
$ php composer.phar install
```


### Chave da Aplicação
Gere a chave da aplicação utilizando o comando a seguir.

```
$ php artisan key:generate
```

### Migrações
Execute as migrações do banco de dados para que ele fique preparado para ser
utilizado pela aplicação:

```
$ php artisan migrate --force
```

### Servidor Web

#### Implantando a Aplicação
Implante a aplicação no servidor web que for utilizar.
Se for utilizar o Nginx, a aplicação deve ser implantada no diretório 
"/var/www/" (Linux). 

Se estiver usando o Linux, pode-se criar um link simbólico da aplicação para "/var/www/". 
Nesse caso, se certifique de que as permissões em determinados diretórios estejam 
corretas:

```
$ sudo chmod 755 /home
$ sudo chmod 755 /home/$USER
```

#### Permissões 
Dê permissão para o servidor web acessar o diretório raiz da aplicação:

```
$ sudo chmod -R $USER:www-data diretório/para/a/raiz/da/aplicação/
```

Dê permissão de escrita na pasta "storage/" da aplicação:
```
$ sudo chmod 777 -R diretório/para/a/raiz/da/aplicação/storage/
```

#### Servidor Virtual
Caso esteja utilizando o Nginx ou Apache, é necessário criar o servidor virtual
para a aplicação.

<b>Nginx</b>

Copie o arquivo "nginx-server-block/gestao-bancaria" para o diretório de servidores 
virtuais do Nginx e o altere conforme suas preferências.

No Nginx, o diretório dos servidores virtuais é: "/etc/nginx/sites-available/".

Após criar o servidor virtual nesse diretório, deve-se criar um link simbólico 
desse servidor virtual para a pasta "/etc/nginx/sites-enabled/". Comando no Linux:

```
$ sudo ln -s /etc/nginx/sites-available/meu-servidor-virtual /etc/nginx/sites-enabled
```

### Reiniciando Serviços
Caso esteja utilizando Linux, reinicie os serviços do Nginx e PHP-FPM com os comandos:

```
$ sudo systemctl restart nginx
$ sudo systemctl restart php<sua versão>-fpm
```