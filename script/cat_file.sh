#! /bin/bash

cd "/var/www/YouGit/repo/$1/"
#tmp=1;
if [ "$3" -eq "1" ];then
git cat-file -t $2
else
git cat-file -p $2
fi
