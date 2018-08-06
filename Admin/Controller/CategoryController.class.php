<?php

/**
 * 栏目控制器
 */

class CategoryController extends CommonController
{

	private $_model;

	public function __auto() {
		$this->_model = ext_model('Category');
	}

	/**
	 * 列表
	 */
	public function index() {
		$data = $this->_model->get_all();
		$this->assign('data', $data);
		$this->display();
	}

	/**
	 * 添加-页面
	 */
	public function add() {
		$this->display();
	} 

	/**
	 * 添加-数据
	 */
	public function add_data() {
		$id = $this->_model->add_data();
		if ($id) return ajaxReturn('200', '操作成功');
		return ajaxReturn('201', '操作失败');
	}

	/**
	 * 删除
	 */
	public function del_data() {
		$cid = $_POST['data'];
		$id = $this->_model->del_data($cid);
		if ($id) return ajaxReturn('200', '操作成功');
		return ajaxReturn('201', '操作失败');
	}

	/**
	 * 编辑-页面
	 */
	public function edit() {
		$cid = intval($_GET['cid']);
		$info = $this->_model->get_one($cid);
		$this->assign('info', $info);
		$this->display();
	}

	/**
	 * 编辑-数据
	 */
	public function edit_data() {
		file_put_contents('./1.txt', print_r($_POST,true));
		$id = $this->_model->edit_data($_POST);
		if ($id) return ajaxReturn('200', '操作成功');
		return ajaxReturn('201', '操作失败');
	}

}