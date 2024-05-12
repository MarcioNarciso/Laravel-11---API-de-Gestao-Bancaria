# API de Gestão Bancária

A API implementada possui suas rotas documentadas utilizando Swagger.
A documentação está disponível na URL raiz.

A API fornece um endpoint para cadastro e consulta de contas e outro para realizar 
e listar transações financeiras entre as contas.

## Funcionalidades

### Comando do Artisan
Criei um comando no Artisan para automatizar a criação do banco de dados da 
aplicação. Execute o comando a seguir e o banco será criado no MySQL:

```
php artisan db:create-db
```

### Endpoints

#### Contas
No endpoint de contas, pode-se criar, desativar, pesquisar e listar as contas ativas.

#### Transações
No endpoint de transações, pode-se realizar uma transação para transferência de 
valores entre contas e listar as transações de determinada conta.


## Padrões de Projeto Utilizados

### Template Method & Factory Method
Neste projeto, utilizei o padrão de projeto Template Method junto com o 
Factory Method para flexibilizar a criação de novas formas de pagamento e 
melhorar a coesão e acoplamento. Dessa forma, requer pouca alteração 
no código para adicionar uma nova forma de pagamento e também evita uma 
"dependência implícita".

### Active Record
Padrão já utilizado pelo Laravel. Aproveitei os benefícios do Active Record para 
manter as classes mais coesas.

### Builder & Director
Utilizei o padrão Builder com Director para simplificar a criação de objetos para
os testes automatizados (Test Data Builder).

### Repository
Definí uma interface e implementei um repositório para cada classe model. 
Dessa forma, a lógica da camada de acesso aos dados fica mais desacoplada da
aplicação e facilita a troca do repositório por outras implementações.

### Service Layer
Definí um interface e implementei uma camada de serviço para encapsular a lógica
de negócio e mantê-la mais desacoplada da aplicação.

### Singleton
Utilizei os recursos que o Laravel fornece como uma abstração do Singleton para
garantir que certas classes, como serviços, só fossem instanciadas uma vez e 
injetadas como dependências corretamente.


## Pré-requisitos
Para executar esse projeto, é necessário já ter instalado no sistema:

* PHP 8.3
* Laravel 11 (Suas dependências)
* Nginx
* MySQL

## Scripts de Setup do Ambiente

Caso queira realizar o deploy desse projeto em um distribuição Linux baseada
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