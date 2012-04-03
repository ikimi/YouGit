<?php
	Class SettingsAction extends CommonAction
	{
		// 用户通用信息更新
		public function profile() {
			if(empty($_POST)) {
				$visitor = UserModel::getInstance($_COOKIE['username']);
				$this->assign('Profile',$visitor->getProfile());
				$this->display('profile');
			}
			else {
				$profile['name'] = $_POST['name'];
				$profile['email'] = $_POST['email'];
				$profile['blog'] = $_POST['blog'];
				$profile['company'] = $_POST['company'];
				$profile['location'] = $_POST['location'];

				// 实例化 user 对象
				$visitor = UserModel::getInstance($_COOKIE['username']);
				$visitor->setProfile($profile);
				$this->redirect('profile');
			}
		}

		// 用户设置SSH Key 
		public function ssh() {
			if(empty($_POST)) {
				$visitor = UserModel::getInstance($_COOKIE['username']);
				$SSH = $visitor->getSshkey();
				$this->assign('SSH',$SSH);
				$this->display('ssh');
			}
			else {
				
				// 添加 SSH Key
				$visitor = UserModel::getInstance($_COOKIE['username']);
				$visitor->setSshkey($_POST['title'],$_POST['key']);
				$this->redirect('ssh');
			}
		}

		// 用户重置密码
		public function admin() {
			if(empty($_POST)) {
				$this->assign('error',$_COOKIE['error']);
				$this->display('admin');
				setcookie('error','',time()-1);
			}
			else {
				$visitor = UserModel::getInstance($_COOKIE['username']);
				$password = $visitor->getPassword();
				if($password === $_POST['old']) {
					$visitor->setPassword($_POST['new'],$_POST['confirm']);
				}
				else
					setcookie('error','Old Password is invilid~',0);
				$this->redirect('admin');
			}
		}
	}	
?>
