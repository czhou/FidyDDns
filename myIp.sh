#!/bin/sh
if ! command -v curl &> /dev/null
then
    yes | apk add curl &> /dev/null
fi
# Get my IP address
curl -s http://www.fidy.net:8081/api/ip | grep -oE '[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+'
