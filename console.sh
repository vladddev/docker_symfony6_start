#!/usr/bin/env bash


./scripts/exec.sh --container core --workdir "/var/www/html/demosite" --command "php bin/console $*"