#!/usr/bin/env bash

compose_files=(
    $(find "./compose" -maxdepth 1 -type f -name "*.yml" | sed -Ern "s/^\.\/compose\/([-a-z]+)\.yml/\1/p" | sort)
)

cd $(realpath .)

execute_docker_command() {
    if [[ "$1" == "up" ]]; then
        env $(cat .env* | grep ^[A-Z] | xargs) docker stack deploy $2 \
            --compose-file ./compose/$2.yml \
            --with-registry-auth
        if [[ $? != 0 ]]; then
            exit 1
        fi
    fi

    if [[ "$1" == "down" ]]; then
        docker stack remove $2
        if [[ $? != 0 ]]; then
            exit 1
        fi
    fi

    if [[ "$1" == "pull" ]]; then
        docker-compose -f ./compose/$2.yml pull
        if [[ $? != 0 ]]; then
            exit 1
        fi
    fi

    if [[ "$1" == "restart" ]]; then
        docker stack remove $2
        if [[ $? != 0 ]]; then
            exit 1
        fi
        env $(cat .env* | grep ^[A-Z] | xargs) docker stack deploy $2 \
            --compose-file ./compose/$2.yml \
            --with-registry-auth
        if [[ $? != 0 ]]; then
            exit 1
        fi
    fi
}

if [[ -z $2 ]]; then
    PS3='Выберите стек: '
    options=("Все" "${compose_files[@]}")
    select opt in "${options[@]}"; do
        case "$opt" in
        "Все")
            selected="all"
            break
            ;;
        *)
            selected=${opt}
            break
            ;;
        esac
    done
else
    selected=$2
fi

if [[ ${selected} == "all" ]]; then
    for compose_file in "${compose_files[@]}"; do
        execute_docker_command $1 ${compose_file}
    done
else
    execute_docker_command $1 ${selected}
fi
