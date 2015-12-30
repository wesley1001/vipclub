<?php
/**
 * 全部分类管理控制器
 */

namespace Admin\Controller;
use Think\Controller;
class CateController extends CommonController {
    public function __construct(){
        parent::__construct();
        $this->dbname = 'Cate';
    }

    public function index(){
        $this->_befor_add();
    	$this->display();
    }

    /**
     * 获取所有分类
     * @return [type] [description]
     */
    public function _befor_add(){
    	$model = D($this->dbname);
        $info = $model->getAllCate();
        $this->allCate = unlimitedForLevel($info);
    }

    public function _befor_edit(){
        $this->_befor_add();
    }

    /**
     * 删除
     * @return [type] [description]
     */
    public function tdel(){
        $id = I('get.id');
        if($id){
            $model = M($this->dbname);
            $result = $model->find($id);
            if($result){
                $re = $model->delete($result['id']);
                if($re){
                    $this->mtReturn(200, '删除成功' . $uid, I('get.navTabId'), false);
                }
            }
        }
    }
}