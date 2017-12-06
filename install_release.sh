#!/usr/local/bin/bash
#!/bin/sh

#$REPOSITORY_GROUP  $1
#$REPOSITORY_NAME   $2
#$REPOSITORY_BRANCH $3

# вывести аргументы скрипта
#echo $@

#учитывать название группы проектов
#DIR=/work/www/dev/$1
DIR=/work/www/dev

DIR2=/work/www/dev/view

#sudo /work/scripts/php7_dev_cli.sh
#HOSTNAME=$(hostname -f)
#if [ "$HOSTNAME" = dev ]; then
#    sudo /work/scripts/php7_dev_cli.sh
#else
#    #printf '%s\n' "uh-oh, not on foo"
#fi

php -v

# найти все репоозитории гит и исполнить для них

#find -H $DIR $DIR2 -name .git -type d -not -path "$DIR/#DEPRECATED" -mindepth 1 -maxdepth 4 -execdir /work/scripts/staging/try_install.sh $1 $2 $3 \;

find -H $DIR $DIR2 -name .git -type d -not -path "*/#DEPRECATED/*" -mindepth 1 -maxdepth 4 -execdir /work/hook/try_install.sh $1 $2 $3 \;

#испытание поиска
#find -H /work/www/dev/ -name .git -type d -not -path "*/#DEPRECATED/*" -mindepth 1 -maxdepth 4
#find -H /work/www/dev/ -name .git -name .git -type d -not -path "*/#DEPRECATED/*" -mindepth 1 -maxdepth 4
