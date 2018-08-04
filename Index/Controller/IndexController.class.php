<?php

class IndexController extends Controller
{
	public function __empty() {
		echo 'empty method';
	}
	public function index() {
		$data = model('link')->where('link_id = 6')->delete();
		p($data);
	}
}	