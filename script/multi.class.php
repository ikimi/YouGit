<?php

// db 类 (单例模式)
class db {
	
	// 数据库对象
	private static $db;

	// 连接句柄
	private $connection;

	// 构造函数
	private function __construct() {
		$config = require_once("../App/Conf/config.php");
		$this->connection = mysql_connect($config['DB_HOST'],$config['DB_USER'],$config['DB_PWD']);
		if(!$this->connection) {
			die('Could not connection : '.mysql_error());
		}
	}

	//实例化对象方法
	public static function getInstance() {
		if(empty(self::$db)) {
			self::$db = new db();
		}
		return self::$db;
	}

	// 销毁句柄 释放资源
	public static function destory() {
		if(!empty(self::$db)) {
			mysql_close(self::$db->connection);
		}
		return null;
	}

	// 返回数据库句柄
	public function getHandler() {
		if(empty($this->connection))
			return null;
		return $this->connection;
	} 
}

// commit 类
class commit {

	// commit 对象SHA—1值
	private $SHA_1;

	// commit 对象子节点树对象
	private $tree;

	// commit 对象的 parent 节点
	private $parent; 

	// commit 对象子节点对象
	private $kid;

	// commit 对象描述信息
	private $commit_msg;

	// 构造函数，初始化
	public function __construct($SHA_1,$message) {
		$this->SHA_1 = $SHA_1;
		$this->tree = $message[0];
		$this->parent = $message[1];
		$this->commit_msg = $message[5];
	}

	// 向数据库中存储对象
	public function insert($con) {

		// 如果数据库连接句柄为空
		if(empty($con))
			return false;
		$db_selected = mysql_select_db('YouGit',$con);
		$sql = "INSERT INTO think_commit(SHA_1,tree,parent,commit_msg) VALUES('".$this->SHA_1."','".$this->tree."','".$this->parent."','".$this->commit_msg."');";
		if(!mysql_query($sql,$con)) {
			return false;
		}
		return true;
	}

	// 设置 commit 对象字节点
	public function setKids($kid) {
		$this->kid = $kid;
	}

	// 返回 commit 对象子节点
	public function getKids() {
		return $this->kid;
	}
}

// tree 类
class tree {
	
	// tree 对象SHA-1值
	private $SHA_1;

	// tree 对象字节点信息 字节点可能是 tree or blob
	private $children;

	// tree 对象父节点
	private $father;

	// tree 对象字节点对象数组
	private $kids;

	//构造函数，初始化
	public function __construct($SHA_1,$children) {
		$this->SHA_1 = $SHA_1;
		$this->children = $children;
		$this->kids = array();
	}
	
	// 向数据库中存储对象
	public function insert($con) {

		// 如果数据库连接句柄为空
		if(empty($con)) 
			return false;
		mysql_select_db('YouGit',$con);
		$sql = "INSERT INTO think_tree(SHA_1,children) VALUES('".$this->SHA_1."','".$this->children."');";
		if(!mysql_query($sql,$con)) 
			return false;
		return true;
	}

	// 设置 tree 对象父节点
	public function setFather($father) {
		$this->father = $father;
	}

	// 获得 tree 对象父节点
	public function getFather() {
		return $this->father;
	}

	// 设置子节点
	public function setKids($kid) {
		$this->kids[] = $kid;
	}

	// 返回字节点集合
	public function getKids() {
		return $this->kids;
	}
}

// blob 类
class blob {

	// blob 对象SHA-1值
	private $SHA_1;

	// blob 对象文件名
	private $filename;

	// blob 对象的父节点
	private $father;

	// blob 对象内容
	private $content;

	//构造函数 初始化
	public function __construct($SHA_1,$filename,$content) {
		$this->SHA_1 = $SHA_1;
		$this->filename = $filename;
		$this->content = $content;
	}

	// 将对象存储到数据库
	public function insert($con) {

		// 检查数据库句柄是否为空
		if(empty($con))
			return false;
		mysql_select_db('YouGit',$con);
		$sql = "INSERT INTO think_blob(SHA_1,filename,content) VALUES('".$this->SHA_1."','".$this->filename."','".$this->content."');";
		if(!mysql_query($sql,$con))
			return false;
		return true;
	}

	// 设置 blob 对象父节点
	public function setFather($father) {
		$this->father = $father;
	}

	// 返回 blob 对象父节点
	public function getFather() {
		return $this->father;
	}
}

?>
