#! /bin/bash
#比较两次提交的版本差异

#进入相应项目目录
cd /var/www/YouGit/repo/$1.git/

#如果选项为1 则添加 --stat 选项
if [ "$4" -eq "1" ];then
	git diff $2 $3 --stat
else
	git diff $2 $3
fi

