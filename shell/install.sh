#!/bin/bash
ENV=$0;

if [ -f "composer.phar" ]
then
    echo 'composer.phar already exists';
else
    php -r "readfile('https://getcomposer.org/installer');" | php
fi

if [ $ENV == 'dev' ]
then
    php composer.phar -v --profile install
else
    php composer.phar --no-dev -o install
fi

