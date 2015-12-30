<?php

namespace Admin\Model;
use Think\Model;

class AccountModel extends Model{
	public function __construct(){
		parent::__construct();
	}

	protected $_auto = array(
		array('addtime', 'time', 1, 'function'),
		array('updatetime', 'time', 2, 'function'),
	);
}