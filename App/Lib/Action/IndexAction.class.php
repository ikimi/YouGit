<?php
class IndexAction extends Action
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function index()
	{
		if(!isset($_COOKIE['username'])) {
			$this->redirect('login');
			return ;
		}

		/*
		 * 实例化对象
		 * 获取用户信息
		 */
		$visitor = UserModel::getInstance($_COOKIE['username']);
		$Info = $visitor->getInfo();
		$this->assign('username',$_COOKIE['username']);
		$this->assign('Info',$Info);

		/*
		 * 获取用户参加的项目的信息
		 */
		$repos = $visitor->getRepos();
		//var_dump($repos);
		$this->assign('repos',$repos);
		$this->display('index');
    }

    /**
    +----------------------------------------------------------
    * 探针模式
    +----------------------------------------------------------
    */
    public function checkEnv()
    {
        load('pointer',THINK_PATH.'/Tpl/Autoindex');//载入探针函数
        $env_table = check_env();//根据当前函数获取当前环境
        echo $env_table;
    }

	/**
	 * ----------------------------------------------------------
	 * 用户注册
	 * ----------------------------------------------------------
	 */
	public function regist() 
	{
		if(empty($_POST)) {
			setcookie('connection','',time()-1);
			$this->display('regist');
		}
		else {
			if(strlen($_POST['username']) < 4) {
				setcookie('connection','',time()-1);
				$this->redirect('login');
			}
			elseif(strlen($_POST['password']) < 6) {
				setcookie('error','Password needs at least 6 words~',0);
				setcookie('connection','',time()-1);
				$this->redirect('login');
			}
			elseif($_POST['password'] !== $_POST['confirm']) {
				setcookie('error','Password is not equal to Confirm Password~',0);
				setcookie('connection','',time()-1);
				$this->redirect('login');
			}
			else {

			// 用户注册的信息合法，创建用户
			$data['username'] = $_POST['username'];
			$data['password'] = $_POST['password'];
			$data['email'] = $_POST['email'];
			$data['membersince'] = date('F j,Y');
			$data['location'] = $_POST['location'];
			$visitor = UserModel::getInstance($_POST['username']);
			$visitor->data($data)->add();
			setcookie('error','Regist success~',0);
			setcookie('connection','',time()-1);
			$this->redirect('login');
			}
		}
	}

	/**
	 * ----------------------------------------------------------
	 * 登录验证
	 * ----------------------------------------------------------
	 */
	public function login() {
		if(isset($_COOKIE['username']))
			$this->redirect('index');
		else {
			if(isset($_COOKIE['error'])) {
				$this->assign('error',$_COOKIE['error']);
				setcookie('error','',time()-1);
			}
			if(!isset($_COOKIE['connection'])) {
				setcookie('connection','connection',0);
				$this->display('login');
			}
			else
				$this->check($_POST['username'],$_POST['password']);
		}
	}

	/**
	 * ----------------------------------------------------------
	 * 帐号检查
	 * ----------------------------------------------------------
	 */
	protected function check($username = '',$password = '') {
		if(empty($username)) {
			setcookie('error','Username can\'t be empty!',0);
			setcookie('connection','',time()-1);
			$this->redirect('login');
		}
		if(empty($password)) {
			setcookie('error','Password can\'t be empty!',0);
			setcookie('connection','',time()-1);
			$this->redirect('login');
		}
		if($username == 'admin')
			$visitor = AdminModel::getInstance($username);
		else {
			$visitor = UserModel::getInstance($username);
		}
		$pwd = $visitor->where("username='{$username}'")->getField('password');
		if($pwd == $password) {
			$visitor->keyname = $visitor->where("username='{$username}'")->getField('keyname');
		//	echo $visitor->getLastSql();
			setcookie('username',$username,0,'/');
			setcookie('connection','',time()-1);
			//序列化
		//	setcookie('visitor',serialize($visitor),0,'/');
			$this->redirect('index');
		}
		else {
			setcookie('error','Username or Password is wrong!',0);
			setcookie('connection','',time()-1);
			$this->redirect('login');
		}
	}

	/**
	 * ----------------------------------------------------------
	 * 账户注销
	 * ----------------------------------------------------------
	 */
	public function logout() {
		if(isset($_COOKIE['username']))
			setcookie('username','',time()-1,'/');
		if(isset($_COOKIE['visitor']))
			setcookie('visitor','',time()-1,'/');
		$this->redirect('login');
	}
}
?>
