#!/bin/sh
set -eu

RED_COLOR="\e[91m"
DEFAULT_COLOR="\033[0m"

chmod -R 777 var

exec "$@"
