<?php
/**
 * 用户管理控制器
 */

namespace Admin\Controller;
use Think\Controller;
class AdminController extends CommonController {
    public function __construct(){
        parent::__construct();
        $this->dbname = CONTROLLER_NAME;
    }

    public function index(){
    	$model = D($this->dbname);
    	$this->list = $model->select();
    	$this->display();
    }

	public function _befor_insert($data){
		$password=md5(md5(I('pwd')));
		$data['password'] = $password;
		unset($data['pwd']);
		$data['logintime'] = date('Y-m-d H:i:s');
		$data['regtime'] = date('Y-m-d H:i:s');
		$data['loginip'] = get_client_ip();
		return $data;
	}

	//设置权限
	public function editrule(){
		$uid=I('get.id');
		$model = M('auth_group_access');
		$model->where('uid='.$uid.'')->delete(); 
		$gcdata['uid'] = $uid;
		$gcdata['group_id'] = M('Auth_group')->where(array("title"=>I('get.groupName')))->getField('id');
		$model->add($gcdata);
		$this->mtReturn(200,"设置成功".$id,$_REQUEST['navTabId'],false); 
	}

	//修改密码
	public function _befor_update($data){
		if (strlen(I('pwd'))!==32){
			$password = md5(md5(I('pwd')));
			$data['password'] = $password;
		}
		unset($data['pwd']);
		return $data;
	}

	//删除
	public function tdel(){
		$uid = I('get.id');
		if($uid){
			$model = M($this->dbname);
			$result = $model->find($uid);
			if($result){
				$this->delRuleById($result['id']);//同步删除auth_group_access表
				$re = $model->delete($result['id']);
				if($re){
					$this->mtReturn(200, '删除成功' . $uid, I('get.navTabId'), false);
				}
			}
		}
	}

	/**
	 * 根据用户ID删除权限规则
	 * @param [type] uid 用户ID
	 * @return [type] [description]
	 */
	private function delRuleById($uid){
		$model = M('Auth_group_access');
		$map['uid'] = $uid;
		$result = $model->where($map)->select();
		if($result){
			return $model->where($map)->delete();
		}
	}
}