#!/usr/bin/env bash

DIR_ATUAL=$(dirname "$0")

. "$DIR_ATUAL"/scripts/install-php.sh

. "$DIR_ATUAL"/scripts/install-mysql.sh

. "$DIR_ATUAL"/scripts/create-database-user.sh

. "$DIR_ATUAL"/scripts/install-nginx.sh

. "$DIR_ATUAL"/scripts/prepare-project.sh