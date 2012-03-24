#! /bin/bash
#完成项目的创建，即初始化项目

cd /var/www/YouGit/repo/ 
mkdir $1
cd $1
git init --bare
