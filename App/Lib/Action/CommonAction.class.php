<?php
	class CommonAction extends Action {
		public function _initialize() {
			if(empty($_COOKIE['username']))
				$this->redirect('Index/login');
		}
	}
?>
