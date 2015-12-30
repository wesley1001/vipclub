<?php
/**
 * 权限规则控制器
 */

namespace Admin\Controller;
use Think\Controller;
class RuleController extends CommonController {
    public function __construct(){
        parent::__construct();
        $this->dbname = 'Auth_rule';
    }

    public function index(){
        $list = D($this->dbname)->where('level=0')->order('sort')->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function _befor_add(){
        $this->list=cateTree($pid=0,$level=0,$this->dbname);
    }

    //添加之前
    public function _befor_insert($data){
        $pid = I('pid');
        if ($pid==0){
            $data['level']=0;
        }else{
            $level=D($this->dbname)->where('id='.$pid.'')->field('level')->limit(1)->select();
            $level=$level[0]['level']+1;
            $data['level']=$level;
        }
        return $data;
    }

    public function _befor_edit(){
        $list=cateTree($pid=0,$level=0,$this->dbname);
        $this->assign('list',$list);
    }

    /**
     * 删除用户组
     * @return [type] [description]
     */
    public function tdel(){
    	$id = I('get.id', '', 'intval');
    	if($id){
    		$model = D($this->dbname);
    		$result = $model->find($id);
    		if($result){
    			if($model->delete($id)){
    				$this->mtReturn(200, '删除成功'.$id, I('get.navTabId'), false);
    			}
    		} else {
    			$this->mtReturn(300, '无此记录', '', false);
    		}
    	}
    }
}