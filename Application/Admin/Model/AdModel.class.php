<?php

namespace Admin\Model;
use Think\Model;

class AdModel extends Model{
	public function __construct(){
		parent::__construct();
	}

	protected $_auto = array(
		array('addtime', 'getTime', 1, 'function'),
		array('updatetime', 'getTime', 2, 'function'),
	);
}