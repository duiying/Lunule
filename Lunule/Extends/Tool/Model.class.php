<?php

class Model 
{

	// 保存连接信息
	public static $link = NULL;
	// 保存表名
	protected $table = NULL;
	// 初始化表信息
	private $opt;
	// 记录发送的sql
	public static $sqls = [];

	public function __construct($table = NULL) {
		$this->table = is_null($table) ? config('DB_PREFIX') . $this->table : config('DB_PREFIX') . $table;

		// 连接数据库
		$this->_connect();

		// 初始化sql
		$this->_opt();
	}

	/**
	 * 连接数据库
	 */
	private function _connect() {
		if (is_null(self::$link)) {
			$db_database 	= config('DB_DATABASE');
			$db_host 		= config('DB_HOST');
			$db_user 		= config('DB_USER');
			$db_password 	= config('DB_PASSWORD');
			$db_database 	= config('DB_DATABASE');
			$db_port 		= config('DB_PORT');

			if (empty($db_database)) halt('请先配置数据库'); 
			$link = new Mysqli($db_host, $db_user, $db_password, $db_database, $db_port);
			if ($link->connect_error) halt('数据库连接错误, 请检查配置项');
			$link->set_charset(config('DB_CHARSET'));
			self::$link = $link;
		}
	}


	/**
	 * 初始化sql
	 */
	private function _opt() {
		$this->opt = [
			'field' 	=> '*',
			'where' 	=> '',
			'group' 	=> '',
			'having' 	=> '',
			'order' 	=> '',
			'limit' 	=> '',
		];
	}

	/**
	 * 执行sql 有结果集
	 * @param string $sql SQL语句
	 */
	public function query($sql) {
		echo $sql;
		self::$sqls[] = $sql;
		$link = self::$link;
		$res = $link->query($sql);
		if ($link->errno) halt('MySQL错误: ' . $link->error . '<br/>SQL: ' . $sql);
	
		$rows = [];
		while ($row = $res->fetch_assoc()) {
			$rows[] = $row;
		}
		$res->free();
		return $rows;
	}

	/**
	 * 执行sql 无结果集
	 * @param string $sql SQL语句
	 */
	public function exe($sql) {
		self::$sqls[] = $sql;
		$link = self::$link;
		$bool = $link->query($sql);

		if (is_object($bool)) {
			halt('请用query方法发送查询sql');
		}

		if ($bool) {
			return $link->insert_id ? $link->insert_id : $link->affected_rows;
		} else {
			halt('MySQL错误: ' . $link->error . '<br/>SQL: ' . $sql);
		}
	}

	/**
	 * 查询所有数据
	 */
	public function all() {
		$sql = 'SELECT ' . $this->opt['field'] . ' FROM ' . $this->table . $this->opt['where'] . $this->opt['group'] . $this->opt['having'] . $this->opt['order']. $this->opt['limit'];
		return $this->query($sql);
	}

	/**
	 * 查询所有数据 all的别名
	 */
	public function findAll() {
		return $this->all();
	}

	/**
	 * 指定field
	 * @param string $field
	 */
	public function field($field) {
		$this->opt['field'] = $field;
		return $this;
	}

	/**
	 * 指定where
	 * @param string $where
	 */
	public function where($where) {
		$this->opt['where'] = " WHERE " . $where;
		return $this;
	}

	/**
	 * 指定order
	 * @param string $order
	 */
	public function order($order) {
		$this->opt['order'] = " ORDER BY " . $order;
		return $this;
	}

	/**
	 * limit
	 * @param string $limit 字段
	 */
	public function limit($limit) {
		$this->opt['limit'] = " LIMIT " . $limit;
		return $this;
	}

	/**
	 * find 返回一维数组
	 * @param string $limit 字段
	 */
	public function find() {
		$data = $this->limit(1)->all();
		$data = current($data);
		return $data;
	}

	/**
	 * one 返回一维数组 find方法的别名
	 * @param string $limit 字段
	 */
	public function one() {
		return $this->find();
	}

	/**
	 * delete 
	 */
	public function delete() {
		if (empty($this->opt['where'])) halt('删除语句必须有sql语句');
		$sql = "DELETE FROM " . $this->table . $this->opt['where'];
		return $this->exe($sql);
	}

	/**
	 * 添加方法
	 */
	public function add($data = NULL) {
		if (is_null($data)) $data = $_POST;
		$fields = '';
		$values = '';

		foreach ($data as $f => $v) {
			$fields .= "`" . $this->_safe_str($f) . "`,";
			$values .= "'" . $this->_safe_str($v) . "',";
		}

		$fields = trim($fields, ',');
		$values = trim($values, ',');

		$sql = "INSERT INTO " . $this->table . "(" . $fields . ") VALUES (" . $values . ")";
		return $this->exe($sql);
	}

	/**
	 * 修改方法
	 */
	public function update($data = NULL) {
		if(empty($this->opt['where'])) halt('更新语句必须有where');
		if(is_null($data)) $data = $_POST;

		$values = '';

		foreach ($data as $f => $v) {
			$values .= "`" . $this->_safe_str($f) . "`='" . $this->_safe_str($v) . "',";
		}

		$values = trim($values, ',');
		$sql = "UPDATE " . $this->table . " SET " . $values . $this->opt['where'];
		return $this->exe($sql);
	}

	/**
	 * 安全处理
	 */
	private function _safe_str($str) {
		if (get_magic_quotes_gpc()) {
			$str = stripcslashes($str);
		}

		return self::$link->real_escape_string($str);
	}

}