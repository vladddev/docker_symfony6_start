#!/usr/bin/env bash

export $(cat .env* | grep ^[A-Z] | xargs)

./acme.sh --issue --force -d "demosite.ru" \
  -w /opt/acme --server letsencrypt

./acme.sh --install-cert -d "demosite.ru" \
  --fullchain-file /opt/certificates/fullchain.pem \
  --key-file /opt/certificates/privkey.pem
