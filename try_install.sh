#!/usr/local/bin/bash
#!/bin/sh



#RCol='\e[0m'    # Text Reset
# Regular           Bold                Underline           High Intensity      BoldHigh Intens     Background          High Intensity Backgrounds
#Bla='\e[0;30m';     BBla='\e[1;30m';    UBla='\e[4;30m';    IBla='\e[0;90m';    BIBla='\e[1;90m';   On_Bla='\e[40m';    On_IBla='\e[0;100m';
#Red='\e[0;31m';     BRed='\e[1;31m';    URed='\e[4;31m';    IRed='\e[0;91m';    BIRed='\e[1;91m';   On_Red='\e[41m';    On_IRed='\e[0;101m';
#Gre='\e[0;32m';     BGre='\e[1;32m';    UGre='\e[4;32m';    IGre='\e[0;92m';    BIGre='\e[1;92m';   On_Gre='\e[42m';    On_IGre='\e[0;102m';
#Yel='\e[0;33m';     BYel='\e[1;33m';    UYel='\e[4;33m';    IYel='\e[0;93m';    BIYel='\e[1;93m';   On_Yel='\e[43m';    On_IYel='\e[0;103m';
#Blu='\e[0;34m';     BBlu='\e[1;34m';    UBlu='\e[4;34m';    IBlu='\e[0;94m';    BIBlu='\e[1;94m';   On_Blu='\e[44m';    On_IBlu='\e[0;104m';
#Pur='\e[0;35m';     BPur='\e[1;35m';    UPur='\e[4;35m';    IPur='\e[0;95m';    BIPur='\e[1;95m';   On_Pur='\e[45m';    On_IPur='\e[0;105m';
#Cya='\e[0;36m';     BCya='\e[1;36m';    UCya='\e[4;36m';    ICya='\e[0;96m';    BICya='\e[1;96m';   On_Cya='\e[46m';    On_ICya='\e[0;106m';
#Whi='\e[0;37m';     BWhi='\e[1;37m';    UWhi='\e[4;37m';    IWhi='\e[0;97m';    BIWhi='\e[1;97m';   On_Whi='\e[47m';    On_IWhi='\e[0;107m';



#RCol=''    # Text Reset
# Regular           Bold                Underline           High Intensity      BoldHigh Intens     Background          High Intensity Backgrounds
#Bla='';     BBla='';    UBla='';    IBla='\e[0;90m';    BIBla='\e[1;90m';   On_Bla='\e[40m';    On_IBla='\e[0;100m';
#Red='';     BRed='';    URed='';    IRed='\e[0;91m';    BIRed='\e[1;91m';   On_Red='\e[41m';    On_IRed='\e[0;101m';
#Gre='';     BGre='';    UGre='';    IGre='\e[0;92m';    BIGre='\e[1;92m';   On_Gre='\e[42m';    On_IGre='\e[0;102m';
#Yel='';     BYel='';    UYel='';    IYel='\e[0;93m';    BIYel='\e[1;93m';   On_Yel='\e[43m';    On_IYel='\e[0;103m';
#Blu='';     BBlu='';    UBlu='';    IBlu='\e[0;94m';    BIBlu='\e[1;94m';   On_Blu='\e[44m';    On_IBlu='\e[0;104m';
#Pur='';     BPur='';    UPur='';    IPur='\e[0;95m';    BIPur='\e[1;95m';   On_Pur='\e[45m';    On_IPur='\e[0;105m';
#Cya='';     BCya='';    UCya='';    ICya='\e[0;96m';    BICya='\e[1;96m';   On_Cya='\e[46m';    On_ICya='\e[0;106m';
#Whi='';     BWhi='';    UWhi='';    IWhi='\e[0;97m';    BIWhi='\e[1;97m';   On_Whi='\e[47m';    On_IWhi='\e[0;107m';






#PATH=/etc:/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin
export PATH=/usr/local/bin:$PATH

ORIGIN_URL="$(git config --get remote.origin.url)"

#echo $ORIGIN_URL

# проверяем совпадение части строки
case "$ORIGIN_URL" in
    *"$2"*) IS_CORRECT_REPOSTORY=1 ;;
    *)  ;;
esac


if [ "$IS_CORRECT_REPOSTORY" = 1 ]; then

case "$ORIGIN_URL" in
    *"http"*) IS_HTTP=1 ;;
    *)  ;;
esac

if [ "$IS_HTTP" = 1 ]; then
    echo -e ":x: Пропущен http репозиторий: "
    pwd
    echo -e "\n";
    exit
fi

branch_name="$(git symbolic-ref HEAD 2>/dev/null)" ||
branch_name="(unnamed branch)"     # detached HEAD
branch_name=${branch_name##refs/heads/}

  if [ $# != 3 ] || [ $3 = $branch_name ]; then
       if [ -f "$(pwd)/install_release.sh" ]; then
         chmod 777 $(pwd)/install_release.sh && $(pwd)/install_release.sh && 
         echo -e "Стейджинг *$(pwd)* обновлен успешно! Ветка - $branch_name" &&
         echo -e "Обновление произведено с помощью файла $(pwd)/install_release.sh" 
       else
         git pull -q || { echo -e "При обновлении стейджинга $(pwd) произошла ошибка" ; }
         #git fetch origin || { echo "${Bla}${On_Red}При обновлении стейджинга $(pwd) произошла ошибка${RCol}" ; exit 1; }
         #git reset --hard FETCH_HEAD || { echo "${Bla}${On_Red}При обновлении стейджинга $(pwd) произошла ошибка${RCol}" ; exit 1; }
         echo -e "Стейджинг *$(pwd)* обновлен успешно! Ветка - $branch_name"
         echo -e "Была выполнена команда"
         echo -e "git pull --quiet"
         #echo -e "${Gre}Были выполнены команды:${RCol}" 
         #echo -e "${Gre}git fetch origin${RCol}" 
         #echo -e "${Gre}git reset --hard FETCH_HEAD${RCol}" 
         echo -e "\nВы можете использовать свой скрипт автообновления, поместив его в корень проекта и назвав install_release.sh\n"
       fi
  fi
fi


