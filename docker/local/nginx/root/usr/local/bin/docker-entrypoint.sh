#!/bin/sh
set -eu

RED_COLOR="\e[91m"
DEFAULT_COLOR="\033[0m"

if [ -z "${FASTCGI_PASS}" ]
then
     echo -e "${RED_COLOR}FASTCGI_PASS not set.${DEFAULT_COLOR}"
     exit 1
fi

sed -e "s#%fastcgi_pass%#${FASTCGI_PASS}#" \
    /etc/nginx/conf.d/default.conf.dist > /etc/nginx/conf.d/default.conf

exec "$@"
