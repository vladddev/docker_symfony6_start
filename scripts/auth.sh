#!/usr/bin/env bash

LOGGED_IN=$(awk /git\.pinkit\.io:5050/ ~/.docker/config.json)
if [[ -z $LOGGED_IN ]]; then
  docker login git.pinkit.io:5050
fi