#!/usr/bin/env sh

# Instala o Docker
curl -fsSL https://get.docker.com/ | sudo sh

# Inicia o serviço do Docker
sudo systemctl start docker
# Habilita o serviço do Docker para iniciar junto com o boot do SO
sudo systemctl enable docker

# Adiciona o usuário logado ao grupo do Docker
sudo usermod -aG docker $USER
echo -e "\n>>> USER ${USER} ADDED TO DOCKER GROUP\n"

# Instala o Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/bin/docker-compose
# Torna o Docker Compose um executável
sudo chmod +x /usr/bin/docker-compose