<?php

namespace Admin\Model;
use Think\Model;

class MenuModel extends Model{
	public function __construct(){
		parent::__construct();
		$this->obj = M('Menu');
	}

	protected $_auto = array(
		array('status', 1),
		array('time', 'getTime', 1, 'function'),
	);

	/**
	 * 获取所有后台菜单
	 * @return [type] [description]
	 */
	public function getAllMenu($map){	
		$info = $this->obj->where($map)->order('sort desc')->select();
		return $info;
	}
}