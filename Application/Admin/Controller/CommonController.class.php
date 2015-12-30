<?php
namespace Admin\Controller;
use Think\Controller;
use Lib\PdoConnnect;
use Lib\XingePushAnd;
use Lib\XingePushIos;

class CommonController extends Controller {
    public function __construct(){
        parent::__construct();
        if(!session('uid')){
            redirect(U('Admin/Login/index'));
        }

        $name = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        //p($name);exit;
        if(!authcheck($name,session('uid'))){
           $this->mtReturn(300,''.session('username').'很抱歉,此项操作您没有权限！',$_REQUEST['navTabId']);
        }
        $this->sid = session('uid');
    }

    //ajax返回
    public function mtReturn($status,$info,$navTabId="",$closeCurrent=true) {
              
        $result = array();
        $result['statusCode'] = $status; 
        $result['message'] = $info;
        $result['tabid'] = $navTabId .'/index';
        $result['forward'] = '';
        $result['forwardConfirm']='';
        $result['closeCurrent'] =$closeCurrent;
       
        if (empty($type))
            $type = C('DEFAULT_AJAX_RETURN');
        if (strtoupper($type) == 'JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header("Content-Type:text/html; charset=utf-8");
            exit(json_encode($result));
        } elseif (strtoupper($type) == 'XML') {
            // 返回xml格式数据
            header("Content-Type:text/xml; charset=utf-8");
            exit(xml_encode($result));
        } elseif (strtoupper($type) == 'EVAL') {
            // 返回可执行的js脚本
            header("Content-Type:text/html; charset=utf-8");
            exit($data);
        } else {
            // TODO 增加其它格式
        }
    }
    
    public function pushToUser(){
    	$post = I('post.');
    	$sendTime = NULL;
    	$userArr = array();
    	if(!empty($post['time'])){
    		$sendTime = $post['time'];
    	}
    	if($post['userType'] != '0'){
    		$userType = $post['userType'];
    		$pdo = new PdoConnnect();
    		//获取所有符合的用户
    		$sql = "select guid from think_member where find_in_set({$userType}, replace(gametag, '|',',')) or gametag = '' or gametag = NULL";
    		$result = $pdo->getAll($sql);
    		foreach ($result as $guid) {
    			$userArr[] = $guid['guid'];
    		}
    	}
    	$model = M('News');
    	$newsInfo = $model->find($post['id']);
 
    	if($newsInfo){
    		if($post['pushType'] == '0'){//全部推送
    			$android = new XingePushAnd();
    			$androidRes = $android->createAdminAccountPush($newsInfo['title'], $newsInfo['abstract'], $newsInfo['id'], $newsInfo['title'], $newsInfo['addtime'], $newsInfo['linkurl'], $newsInfo['thub'], $newsInfo['tp'], $newsInfo['updatetime'], $newsInfo['abstract'], $sendTime);//创建多账户信息
    			if($androidRes['result']['push_id']){
    				$re = $android->sendMultiple($androidRes['result']['push_id'], $userArr);//发送多账户信息
    			}
    			
    			$ios = new XingePushIos();
    			$iosRes = $ios->createMsgNewsByIos($newsInfo['title'], $newsInfo['abstract'], $newsInfo['id'], $newsInfo['addtime'], $newsInfo['linkurl'], $newsInfo['thub'], $newsInfo['tp'], $newsInfo['updatetime'], $sendTime);
    			if($iosRes['result']['push_id']){
    				$re = $ios->sendMultiple($iosRes['result']['push_id'], $userArr);//发送多账户信息
    			}   			
    		}elseif ($post['pushType'] == '1'){//Android
    			$android = new XingePushAnd();
    			$androidRes = $android->createAdminAccountPush($newsInfo['title'], $newsInfo['abstract'], $newsInfo['id'], $newsInfo['title'], $newsInfo['addtime'], $newsInfo['linkurl'], $newsInfo['thub'], $newsInfo['tp'], $newsInfo['updatetime'], $newsInfo['abstract'], $sendTime);//创建多账户信息
    			if($androidRes['result']['push_id']){
    				$re = $android->sendMultiple($androidRes['result']['push_id'], $userArr);//发送多账户信息
    			}
    		}elseif ($post['pushType'] == '2'){//IOS
    			$ios = new XingePushIos();
    			$iosRes = $ios->createMsgNewsByIos($newsInfo['title'], $newsInfo['id'], $newsInfo['addtime'], $newsInfo['linkurl'], $newsInfo['tp'], $newsInfo['updatetime'], $sendTime);
    			if($iosRes['result']['push_id']){
    				$re = $ios->sendMultiple($iosRes['result']['push_id'], $userArr);//发送多账户信息
    			}
    		}
    		
			if($re['ret_code'] == 0){
				$this->mtReturn(200, "推送成功".$post['id'], $_REQUEST['navTabId'], true);
			}
    	}
    }

    /**
     * 列表页面
     */
    protected function _list($model, $map, $order = '', $asc = false) {
        
        //排序字段 默认为主键名
        // if (isset($_REQUEST ['orderField'])) {
        //     $order = $_REQUEST ['orderField'];
        // }

        if($order=='') {
            $order = $model->getPk();
        }
            
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST ['orderDirection'])) {
            $sort = $_REQUEST ['orderDirection'];
        }
        if($sort=='') {
            $sort = $asc ? 'asc' : 'desc';
        }

        $pageCurrent = (isset($_REQUEST['pageCurrent']) && $_REQUEST['pageCurrent']) ? $_REQUEST['pageCurrent'] : 1;//当前页
        
        //取得满足条件的记录数
        $count = $model->where($map)->count();
       
        if ($count > 0) {

            $numPerPage = 10;

            $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //p($voList);
           if(method_exists($this, '_after_list')){             
                $voList=$this->_after_list($voList);      
            }
            //p($model->getlastsql());
            $this->assign('list', $voList);

        }
        $this->assign('totalCount', $count);//数据总数
        $this->assign('currentPage', $pageCurrent);//当前的页数，默认为1
        $this->assign('numPerPage', $numPerPage); //每页显示多少条
        cookie('_currentUrl_', __SELF__);
        return;
    }

    public function _search($dbname='') {
        
        $dbname = $dbname ? $dbname : $this->dbname;
        $model = D($dbname);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (isset($_REQUEST['keys']) && $_REQUEST['keys'] != '') {
                if(in_array($val, C('SEARCHKEY'))){
                    $map [$val] = array('like','%'.trim($_REQUEST['keys']).'%');
                }else{
                    //$map [$val] = $_REQUEST['keys'];
                }      
            }
        }
        $map['_logic'] = 'or'; 
        if ((IS_POST)&&isset($_REQUEST['keys']) && $_REQUEST['keys'] != '') {
            $where['_complex'] = $map;
            return $where;
        }else{
            return $map;
        }    
    }

    public function add() {
        if(IS_POST){
            $model = D($this->dbname);
            $data=I('post.');

            if(method_exists($this, '_befor_insert')) {
                $data = $this->_befor_insert($data);
            }
            if(false === $data = $model->create($data)) {
                $this->mtReturn(300,'失败，请检查值是否已经存在', $_REQUEST['navTabId'], true);  
            }

            if($model->add($data)){
                if (method_exists($this, '_after_add')) {
                    $id = $model->getLastInsID();
                    $this->_after_add($id);
                }
            $id = $model->getLastInsID();
            $this->mtReturn(200, "新增成功".$id, $_REQUEST['navTabId'], true);  
          }  
        }
        if (method_exists($this, '_befor_add')) {
            $this->_befor_add();
        }
        $this->display();
    }

    public function edit() {
        $model = D($this->dbname);
        if(IS_POST){
            $data=I('post.');

            if (method_exists($this, '_befor_update')) {
                $data = $this->_befor_update($data);
            }
            if(false === $data = $model->create($data)) {
                $this->mtReturn(300,'失败，请检查值是否已经存在', $_REQUEST['navTabId'], true);  
            }   
            if($model->save($data)){
                if (method_exists($this, '_after_edit')) {
                   $id = $data['id'];
                   $this->_after_edit($id);
                } 
            }

            $id = $data['id'];
            $this->mtReturn(200,"编辑成功".$id,$_REQUEST['navTabId'],true);         
        }
        if (method_exists($this, '_befor_edit')) {
            $this->_befor_edit();
        }
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->find($id);//根据主键查找该条信息
        if (method_exists($this, '_after_edit')) {
            $vo = $this->_after_edit($vo);
        }
        $this->assign('id',$id);
        $this->assign('info', $vo);
        $this->display();
    }

    //查看
    public function view(){
        $model = D($this->dbname);
        if (method_exists($this, '_befor_edit')) {
            $this->_befor_edit();
        }
        $id = $_REQUEST [$model->getPk()];
        $vo = $model->find($id);//根据主键查找该条信息
        if (method_exists($this, '_after_edit')) {
            $vo = $this->_after_edit($vo);
        }
        $this->assign('id',$id);
        $this->assign('info', $vo);
        $this->display();
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

    /**
     * 改变状态 1 启用 2 锁定
     * @return [type] [description]
     */
    public function changeStatus(){
        $id = I('get.id', '', 'intval');
        if($id){
            $model = D($this->dbname);
            $data = $model -> find($id);
            if($data){
                switch ($data['status']) {
                    case '1':
                        $re['id'] = $id;
                        $re['status'] = 0;
                        $msg = '锁定';
                        break;
                    case '0':
                        $re['id'] = $id;
                        $re['status'] = 1;
                        $msg = '启用';
                        break;
                    default:
                        break;
                }
                if($re){
                    $model->save($re);
                    $this->mtReturn(200, $msg . $id, $_REQUEST['navTabId'], false);
                }
            } else {
                $this->mtReturn(300, '数据不存在', '', true);
            }
        }
    }

    public function del(){
        $model = D($this->dbname);
        $id = I('get.id');
        if($id){
            $data=$model->find($id);
            if($data){
                $data['id'] = $id;
                $data['status'] = 0;
                if(method_exists($this, '_befor_del')) {
                    $this->_befor_del($id);
                }
                if($model->save($data)){
                    $this->mtReturn(200, '删除成功'.$id,'',false);
                }
            }
        }
    }

    /**
     * 根据ID获取船东名称
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getShipOwnerNameById($id){
        $model = M('Shipowner');
        $info = $model->field('name')->find($id);
        return $info['name'];
    }

    /**
     * 根据ID获取港口名称
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getPortNameById($id){
        $model = M('Port');
        $info = $model->field('name')->find($id);
        return $info['name'];
    }

    //获取所有一级城市
    public function getAllArea(){
        $areaInfo = S('area');//读取缓存
        if(!$areaInfo){
            $model = M('Regions');
            $map['parent_id'] = 0;
            $areaInfo = $model->where($map)->select();
            S('area', $areaInfo);
        }
        return $areaInfo;
    }

    //获取联动二级城市
    public function getCity(){   
        $pid = I('get.id', '', 'intval');
        $model = M('Regions');
        $map['parent_id'] = $pid;
        $cityInfo = $model->where($map)->field('region_id value, region_name label, parent_id')->select();
        echo json_encode($cityInfo);    
    }

    //根据地区ID获取地区名称
    public function getRegionNameById($id){
        if($id){
            $model = M('Regions');
            $info = $model->field('region_name')->find($id);
            return (isset($info['region_name']) && $info['region_name']) ? $info['region_name'] : '';
        }
    }
}