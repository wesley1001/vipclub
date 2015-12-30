<?php
/**
 * 会员管理控制器
 */

namespace Admin\Controller;
use Think\Controller;
class MemberController extends CommonController {
    public function __construct(){
        parent::__construct();
        $this->dbname = CONTROLLER_NAME;
    }

    public function index(){
    	$model = M($this->dbname);
    	$pageCurrent = (isset($_REQUEST['pageCurrent']) && $_REQUEST['pageCurrent']) ? $_REQUEST['pageCurrent'] : 1;//当前页
    	$count = $model->where($map)->count();
    	if($count){
    		$numPerPage = 20;
    		$this->list = $model->where($map)->order('id desc')->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
    	}
    	$this->assign('totalCount', $count);//数据总数
        $this->assign('currentPage', $pageCurrent);//当前的页数，默认为1
        $this->assign('numPerPage', $numPerPage); //每页显示多少条
        cookie('_currentUrl_', __SELF__);
    	$this->display();
    }
}