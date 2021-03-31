#!/bin/bash

FILEPATH=$(realpath $0)
DIRNAME=$(dirname $FILEPATH)

if [ $DIRNAME == "/Users/wagesve/Dev/Sites/enon" ]
then
    cd $DIRNAME && wp search-replace 'www.energieausweis-online-erstellen.de' 'enon.test'
else
    echo "Can only run on local installation"
fi