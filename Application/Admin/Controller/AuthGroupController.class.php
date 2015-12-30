<?php
/**
 * 权限控制-用户组控制器
 */

namespace Admin\Controller;
use Think\Controller;
class AuthGroupController extends CommonController {
    public function __construct(){
        parent::__construct();
        $this->dbname = 'Auth_group';
    }

    public function index(){
    	$model = D($this->dbname);
    	$this->list = $model->where($map)->select();
    	$this->display();
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

	public function editRule(){ 
	  	if (IS_POST){
	   		$data['id']=I('id');
	   		$data['rules']=implode(',',I('rules'));
	   		M('Auth_group')->data($data)->save();
	   		$this->mtReturn(200,'权限设置成功',$_REQUEST['navTabId']);  
	  	}
	  	$id=I('get.id');
	  	$rs=M('Auth_group')->where('id='.I('get.id'))->getField('rules');
	  	$rs=explode(',',$rs);
	  	$info = M('auth_rule'); 
	  	$list = $info->where('level=0')->order('sort')->select();
	  	$this->assign('id',$id);
	  	$this->assign('rs',$rs);
	  	$this->assign('list',$list);
	  	$this->display(); 
	}
}