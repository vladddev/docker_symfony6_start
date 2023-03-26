#!/usr/bin/env bash

source ./scripts/colors.sh

USER="root"

while [[ $# -gt 0 ]]; do
    key="$1"

    case $key in
    --container)
        CONTAINER="$2"
        shift
        shift
        ;;
    --command)
        COMMAND="$2"
        shift
        shift
        ;;
    --user)
        USER="$2"
        shift
        shift
        ;;
    --workdir)
        WORKDIR="$2"
        shift
        shift
        ;;
    --env)
        ENV="$2"
        shift
        shift
        ;;
    *)
        printf "${RED}Неизвестный параметр $key${NOCOLOR}\n"
        exit 1
        ;;
    esac
done

if [[ -z ${CONTAINER} ]]; then
    printf "${RED}Имя контейнера для запуска команды не указано!${NOCOLOR}\n"
    exit 1
fi

## Ищем указанный контейнер
containerId=$(docker ps --filter="name=${CONTAINER}" -q | xargs)

## Проверяем поднялся-ли контейнер для консольных команд
if [[ -z ${containerId} ]]; then
    printf "${RED}Контейнер ${CONTAINER} не найден!${NOCOLOR}\n"
    exit 1
fi

if [[ -z ${COMMAND} ]]; then
    printf "${RED}Команда не указана!${NOCOLOR}\n"
    exit 1
fi

# Собираем строку параметров для вызова docker exec
PARAMS="-it"

if [[ -n ${USER} ]]; then
    PARAMS="${PARAMS} -u ${USER}"
fi

if [[ -n ${WORKDIR} ]]; then
    PARAMS="${PARAMS} -w ${WORKDIR}"
fi

if [[ -n ${ENV} ]]; then
    PARAMS="${PARAMS} -e ${ENV}"
fi

docker exec ${PARAMS} ${containerId} ${COMMAND}

if [[ $?=0 ]]; then
    printf "${GREEN}Команда выполнена успешно!${NOCOLOR}\n"
else
    printf "${RED}Ошибка при выполнении команды${NOCOLOR}\n"
    exit 1
fi
