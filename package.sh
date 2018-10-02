#!/usr/bin/env bash
cd $(dirname $0)
./composer.phar install --no-dev --prefer-dist >/dev/null 2>&1
./composer.phar dump >/dev/null 2>&1
rm mattermost.phar >/dev/null 2>&1
./pakket.phar build . mattermost.phar
chmod +x mattermost.phar
./composer.phar install >/dev/null 2>&1