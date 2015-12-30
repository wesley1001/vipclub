<?php

namespace Admin\Model;
use Think\Model;

class CateModel extends Model{
	public function __construct(){
		parent::__construct();
		$this->obj = M('Cate');
	}

	protected $_auto = array(
		array('addtime', 'getTime', 1, 'function'),
	);

	/**
	 * 获取新闻分类父级ID
	 * @return [type] [description]
	 */
	public function getNewsParentId(){
		$map['remark'] = 'news';
		$map['pid'] = 0;
		$map['status'] = 1;
		$info = $this->obj->where($map)->find();
		return $info['id'];
	}

	/**
	 * 根据ID获取所有子分类
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getCateById($id){
		$map['pid'] = $id;
		$map['status'] = 1;
		return $this->obj->where($map)->select();
	}

	/**
	 * 获取所有分类
	 * @return [type] [description]
	 */
	public function getAllCate(){
		return $this->obj->order('sort')->select();
	}
}