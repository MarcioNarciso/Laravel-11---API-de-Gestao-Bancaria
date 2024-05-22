#!/usr/bin/env sh

# Instala o Docker Compose
$ curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/bin/docker-compose
# Torna o Docker Compose um executável
sudo chmod +x /usr/bin/docker-compose