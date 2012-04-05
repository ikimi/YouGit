<?php

// 定义缓存项目
define('CACHE_PATH','/var/www/YouGit/Repositories');

// 文件夹遍历
function fileList($dir,&$fileList) {
	if(is_dir($dir)) {
		$files = scandir($dir);
		foreach($files as $value) {
			if($value != '.' && $value != '..') {
				$fileList[] = $dir.'/'.$value;
				fileList($dir.'/'.$value,$fileList);
			}
		}
	}
}

// 建树函数
function setTree($dir,$key,$path,&$fileList) {

	$result = null;
	$children = '';
	$sha_1 = $dir->getSHA();

	$path = $path.'/'.$dir->getName();
	if(!is_dir(CACHE_PATH.$path)) {
		mkdir(CACHE_PATH.$path,0777);
	}
	$Key = array_search(CACHE_PATH.$path,$fileList);
	if($Key !== false)
		array_splice($fileList,$Key,1);

	exec("/bin/bash cat_file.sh $key $sha_1 2",$result);
	$con = db::getInstance();

	foreach($result as $value) {
		$children .= substr($value,7,45).';';
		$temp = explode(" ",$value);
		/*
		 * 如果子节点是 blob
		 * 创建 blob 对象,建立与 tree 对象的联系
		 */
		if('blob' == $temp[1]) {
			$blob = new blob(substr($temp[2],0,40),substr($temp[2],41));
			$fileName = CACHE_PATH.$path.'/'.$blob->getName();

			// 如果创建了新 blob 对象
			if(!($blob->isStored($con->getHandler()))) {
				$name = $blob->getSHA();
				unset($blobName);
				$blobName = array();

				exec("/bin/bash cat_file.sh $key $name 2",$blobName);
				$content = '';
				foreach($blobName as $line) {
					$content .= $line."\n";
				}
				$blob->insert($content,$con->getHandler());
				echo $fileName.'---------'.$blob->getSHA().'--------'.$content.'<br/>';
				/*
				 * 判断缓存文件是否存在
				 * 若存在则 重写缓存文件
				 * 若不存在 则创建缓存文件 
				 */
				if(!file_exists($fileName))
					file_put_contents($fileName,'');

				// 更新缓存文件内容
				$f = fopen($fileName,"w");
				fwrite($f,$content);
				fclose($f);

				$dir->setKids($blob);
				$blob->setFather($dir);
			}

			// 更新 fileList
			$Key = array_search($fileName,$fileList);
			if($Key !== false)
				array_splice($fileList,$Key,1);
		}

		// 如果子节点是 tree 
		else {
			$tree = new tree(substr($temp[2],0,40),substr($temp[2],41));
			$dirName = CACHE_PATH.$path.'/'.$tree->getName();
			// 如果创建了新的 tree 对象
			if(!($tree->isStored($con->getHandler()))) {
				echo $dirName.'<br/>';
				$dir->setKids($tree);
				$tree->setFather($dir);
				setTree($tree,$key,$path,&$fileList);
			}

			// 如果是已存在的 tree 对象
			else {
				$Key = array_search($dirName,$fileList);
				if($Key !== false)
					array_splice($fileList,$Key,1);
				$fileList_tmp = array();

				// 该文件夹下所有的文件/文件夹都解除跟踪
				fileList($dirName,$fileList_tmp);
				$fileList = array_diff($fileList,$fileList_tmp);
			}
		}
	}

		$dir->insert($children,$con->getHandler());
}

// 前序深度优先遍历树
function Traverse($root) {
	if($root != null) {
		echo $root->getSHA().'---'.$root->getName()."<br/>";
		if(get_class($root) == 'tree') {
			$children = $root->getKids();
			foreach($children as $child) {
				Traverse($child);
			}
		}
	}
}

// 定义日志文件
define('LOG','monitor.log');

// 引入Log类
require_once('Log.class.php');

// 引入commit tree blob 类
require_once('multi.class.php');

//生成新日志对象
$new_log = new Log(ROOT_PATH);
$new_log->repoNum();
$new_log->repoList();

$root = null;
$dir = null;

//反序列化日志对象
if(file_exists(LOG)) {
	$log = unserialize(file_get_contents(LOG));
	$changeList = array();

	// 新结果与日志结果对比
	while($key = key($new_log->repo_list)) {
		foreach($new_log->repo_list[$key] as $value) {
			if(count($new_log->repo_list[$key]) != 0 && count($new_log->repo_list[$key]) != count($log->repo_list[$key])) {

			//将项目更新的文件存储起来
				$changeList[$key] = array_diff($new_log->repo_list[$key],$log->repo_list[$key]);
			}		
		}
		next($new_log->repo_list);
	}

	//如果有更新
	if(count($changeList))

		/* 
		 * 分析changeList中的文件类型，commit? tree? blob?
		 * 遍历更新数组，针对每个项目进行分析操作
		 */
		while($key = key($changeList)) {
			foreach($changeList[$key] as $object) {

				// 获取 object 文件路径
				$type = null;

				// 执行shell脚本 分析object类型
				exec("/bin/bash cat_file.sh $key $object 1",$type);
			
				// 根据脚本返回结果 进行相应操作
				$result = array();

				// 如果是commit 对象
				if('commit' == $type[0]) {

					exec("/bin/bash cat_file.sh $key $object 2",$result);

					// 获取根节点 及 主 tree
					$con = db::getInstance();

					$root = new commit($object,$result);
					$root->insert($con->getHandler());
					$dir = new tree($root->getTree(),$key);

					// 将主 tree 添加为子节点
					$root->setKids($dir);

					// 将 root 添加为主 tree 的父节点
					$dir->setFather($root);
					$fileList = array();
					$fileList[] = CACHE_PATH.'/'.$key;
					fileList(CACHE_PATH.'/'.$key,$fileList);
					setTree($dir,$key,'',$fileList);
				}
			}
			next($changeList);
		}

	// 遍历对象树
//	Traverse($dir);
	
//	$fileList = array();
//	$fileList[] = '/var/www/haha';
//	fileList('/var/www/haha',$fileList);
//	print_r($fileList);

	//将改变记录到日志中
	file_put_contents(LOG,serialize($new_log));
}
else {
	//序列化 new_log 对象
	file_put_contents(LOG,serialize($new_log));
}
?>
