#!/usr/bin/env bash

sites=(
  "demosite"
)

commands=(
    "update"
    "install"
    "run-script post-update-cmd"
    "run-script post-install-cmd"
    "require"
    "require --dev"
)

if [[ -z $1 ]]; then
    PS3='Выберите сайт: '
    select opt in "${sites[@]}"; do
        case "$opt" in
        *)
            site=${opt}
            break
            ;;
        esac
    done
else
    site=$1
fi

if [[ -z $2 ]]; then
    PS3='Выберите команду: '
    select opt in "${commands[@]}"; do
        case "$opt" in
        *)
            cmd=${opt}
            break
            ;;
        esac
    done
else
    cmd=$2
fi

if [[ ${cmd} == "require" || ${cmd} == "require --dev" ]]; then
    read -p "package: " package
    read -p "version: " version
    options="${package} ${version}"
else
    options=""
fi

./scripts/exec.sh --container core --workdir "/var/www/html/${site}" --env COMPOSER_MEMORY_LIMIT=-1 --command "composer ${cmd} ${options}"
