#!/usr/bin/env sh

# Instala o Docker
curl -fsSL https://get.docker.com/ | sudo sh

# Inicia o serviço do Docker
sudo systemctl start docker
# Habilita o serviço do Docker para iniciar junto com o boot do SO
sudo systemctl enable docker

