<?php

/**
 * 栏目模型
 */

class CategoryModel extends Model
{

	// 表名
	public $table = 'category';

	/**
	 * 添加数据
	 * @param array $data 要添加的数据
	 * @return integer 新插入数据的id
	 */
	public function add_data($data = []) {
		return $this->add($data);
	}

	/**
	 * 查询所有数据
	 * @return array
	 */
	public function get_all() {
		return $this->field(['cid', 'cname'])->all();
	}

	/**
	 * 删除数据
	 * @param integer $cid 栏目id
	 * @return integer 受影响的记录条数
	 */
	public function del_data($cid) {
		return $this->where(['cid' => $cid])->delete();
	}

	/**
	 * 删除数据
	 * @param integer $cid 栏目id
	 * @return array 
	 */
	public function get_one($cid) {
		return $this->field(['cid', 'cname'])->where(['cid' => $cid])->find();
	}

	/**
	 * 更新数据
	 * @param array $data 要更新的数据
	 * @return integer 受影响的记录条数
	 */
	public function edit_data($data) {
		return $this->where(['cid' => $data['cid']])->update(['cname' => $data['cname']]);
	}

}