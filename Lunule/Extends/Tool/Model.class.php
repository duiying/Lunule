<?php

/**
 * 基类Model
 */

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
		self::$sqls[] = $sql;
		$link = self::$link;
		$res = $link->query($sql);
		if ($link->errno) halt('MySQL错误: ' . $link->error . '<br/>SQL: ' . $sql);
	
		$rows = [];
		while ($row = $res->fetch_assoc()) {
			$rows[] = $row;
		}
		$res->free();

		// 初始化sql
		$this->_opt();

		// 返回结果集
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
	 * @return array 结果集数组
	 */
	public function all() {
		$sql = 'SELECT ' . $this->opt['field'] . ' FROM ' . $this->table . $this->opt['where'] . $this->opt['group'] . $this->opt['having'] . $this->opt['order']. $this->opt['limit'];
		return $this->query($sql);
	}

	/**
	 * 查询所有数据 all的别名
	 * @return array 结果集数组
	 */
	public function findAll() {
		return $this->all();
	}

	/**
	 * 指定field
	 * @param string $field
	 * @return object 对象
	 */
	public function field($field) {
		if (is_array($field)) {
			if (count($field) == 0) return $this;
			$str = '';
			foreach ($field as $k => $v) {
				$str .= $v . ',';
			}
			$this->opt['field'] = trim($str, ',');
		} else {
			$this->opt['field'] = $field;
		}
		return $this;
	}

	/**
	 * 指定where
	 * @param string $where
	 * @return object 对象
	 */
	public function where($where) {
		if (is_array($where)) {
			if (count($where) == 0) return $this;
			$str = " WHERE 1 = 1 ";
			foreach ($where as $k => $v) {
				$str .= " AND " . "`" . $k . "` = " . "'$v'"; 	
			}
			$this->opt['where'] = $str;
		} else {
			$this->opt['where'] = " WHERE " . $where;
		}
		return $this;
	}

	/**
	 * order
	 * @param string $order order条件
	 * @return object 对象
	 */
	public function order($order) {
		$this->opt['order'] = " ORDER BY " . $order;
		return $this;
	}

	/**
	 * limit
	 * @param string $limit limit条件
	 * @return object 对象
	 */
	public function limit($limit) {
		$this->opt['limit'] = " LIMIT " . $limit;
		return $this;
	}

	/**
	 * find
	 * @return array 一维结果数组
	 */
	public function find() {
		$data = $this->limit(1)->all();
		$data = current($data);
		return $data;
	}

	/**
	 * one find方法的别名
	 * @return array 一维结果数组
	 */
	public function one() {
		return $this->find();
	}

	/**
	 * delete
	 * @return integer 受影响的记录条数
	 */
	public function delete() {
		if (empty($this->opt['where'])) halt('删除语句必须有sql语句');
		$sql = "DELETE FROM " . $this->table . $this->opt['where'];
		return $this->exe($sql);
	}

	/**
	 * 添加方法
	 * @param array $data 要添加的数据
	 * @return integer 新插入数据的id
	 */
	public function add($data = []) {
		if (empty($data)) $data = $_POST;
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
	 * 更新方法
	 * @param array $data 要更新的数据
	 * @return integer 受影响的记录条数
	 */
	public function update($data = []) {
		if(empty($this->opt['where'])) halt('更新语句必须有where');
		if(empty($data)) $data = $_POST;

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
	 * @param string $str 要处理的字符串
	 * @return string 安全处理过的字符串
	 */
	private function _safe_str($str) {
		if (get_magic_quotes_gpc()) {
			$str = stripcslashes($str);
		}

		return self::$link->real_escape_string($str);
	}

}