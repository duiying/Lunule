<?php

class IndexController extends Controller
{
	public function __empty() {
		echo 'empty method';
	}
	public function index() {
		if (!$this->is_cached()) {
			$this->assign('var', time());
		} 


		
		$this->display();
	}
}	