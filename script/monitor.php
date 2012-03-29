<?php

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

$Objects = array();
//$commits = array();
//$trees = array();
//$blobs = array();

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
			$file_path = ROOT_PATH.$key.'/objects/'.$object;
			$type = null;

			// 执行shell脚本 分析object类型
			exec("/bin/bash cat_file.sh $key $object 1",$type);
			
			// 根据脚本返回结果 进行相应操作
			$result = array();
			// exec("/bin/bash cat_file.sh $key $object 2",$result);

			// 如果是commit 对象
			if('commit' == $type[0]) {

				exec("/bin/bash cat_file.sh $key $object 2",$result);

				// 获取根节点 及 主 tree
				$root = new commit($object,$result);
				$dir = new tree($root->getTree());

				// 将主 tree 添加为子节点
				$root->setKids($dir);

				// 将 root 添加为 主 tree 的父节点
				$dir->setFather($root);

		/*
		 *  Coding.....
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 *
		 */
				// $commits[] =  new commit($object,$result);
				
			}

			// 如果是 tree 对象
/*			elseif('tree' == $type[0]) {

				$children = null;
				foreach($result as $value) {
					$temp = explode(" ",$value);

					// 如果该对象是blob对象
					if('blob' == $temp[1]) {

						// 获取 blob 对象内容
						$blob_result = array();
						$blob = substr($temp[2],0,40);
						$children .=$blob.';';
						exec("/bin/bash cat_file.sh $key $blob 2",$blob_result);

						// 实例化 blob 对象
						$blobs[] = new blob($blob,substr($temp[2],41),$blob_result[0]);	
					}
				}

				// 实例化 tree 对象
				$trees[] = new tree($object,$children);
}
 */
		}
		next($changeList);
	}

	// 单例模式返回 db 对象 
	/*
	$con = db::getInstance();
	foreach($commits as $commit) {
		$commit->insert($con->getHandler());
	}
	foreach($trees as $tree) {
		$tree->insert($con->getHandler());
	}
	foreach($blobs as $blob) {
		$blob->insert($con->getHandler());
	}
	db::destory();
	 */
//	print_r($commits);echo "<br/>-------------------------------------<br/>";
//	print_r($trees);echo  "<br/>-------------------------------------<br/>";
//	print_r($blobs);echo "<br/>-------------------------------------<br/>";

	//将改变记录到日志中
	file_put_contents(LOG,serialize($new_log));
}
else {
	//序列化 new_log 对象
	file_put_contents(LOG,serialize($new_log));
}
?>
