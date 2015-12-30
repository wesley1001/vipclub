<?php
/**
 * 菜单管理控制器
 */

namespace Admin\Controller;
use Think\Controller;
class MenuController extends CommonController {
    public function __construct(){
        parent::__construct();
        $this->dbname = CONTROLLER_NAME;
    }

    public function index(){
        $model = D($this->dbname);
        $map = $this->_search();
        //p($map);exit;
        // if (method_exists($this, '_filter')) {
        //     $this->_filter($map);
        // }
        // if (!empty($model)) {
        //     $this->_list($model, $map, 'sort');
        // }
        // if (method_exists($this, '_befor_index')) {
        //     $this->_befor_index();
        // }
        $map['status'] = array('neq', 0);
        $info = $model->where($map)->order('sort desc')->select();
        $this->list = unlimitedForLevel($info);

    	$this->display();
    }

    public function _after_list($list){
        $info = unlimitedForLevel($list);//对菜单进行有规则的排序
        return $info ? $info : $list;
    }

    public function _befor_insert(){
        
    }

    /**
     * 添加之前
     * @return [type] [description]
     */
    public function _befor_add(){
        $this->menuInfo = $this->getOrderList();
    }

    public function _befor_edit(){
        $this->menuInfo = $this->getOrderList();
    }

    /**
     * 获取已经排列了的数据表
     * @return [type] [description]
     */
    private function getOrderList(){
        $menu = D('Menu');
        $map['status'] = array('neq', 0);
        return unlimitedForLevel($menu->getAllMenu($map));
    }
}