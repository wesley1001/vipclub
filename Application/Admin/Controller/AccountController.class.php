<?php
/**
 * 会员管理控制器
 */

namespace Admin\Controller;
use Think\Controller;
class AccountController extends CommonController {
    public function __construct(){
        parent::__construct();
        $this->dbname = CONTROLLER_NAME;
    }

    public function index(){
        $model = M('Account a');
        $pageCurrent = (isset($_REQUEST['pageCurrent']) && $_REQUEST['pageCurrent']) ? $_REQUEST['pageCurrent'] : 1;//当前页
        $count = $model->where($map)->count();
        if($count){
            $numPerPage = 20;
            $field = "a.id, c.title catename, a.account, a.password, a.num, a.addtime";
            $this->list = $model->where($map)->join('left join __CATE__ c on c.id = a.cate')->field($field)->order('a.id desc')->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
        }
        $this->assign('totalCount', $count);//数据总数
        $this->assign('currentPage', $pageCurrent);//当前的页数，默认为1
        $this->assign('numPerPage', $numPerPage); //每页显示多少条
        cookie('_currentUrl_', __SELF__);
        $this->display();
    }

    public function _befor_add() {
        $this->cateList = $this->getCateList();
    }

    public function _befor_edit() {
        $this->cateList = $this->getCateList();
    }

    private function getCateList() {
        $model = M('Cate');
        $map['remark'] = 'account';
        $map['status'] = 1;
        $parent = $model->where($map)->find();
        $where['pid'] = $parent['id'];
        $where['status'] = 1;
        $list = $model->where($where)->order('sort desc')->select();
        return $list;
    }
}