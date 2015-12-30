<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        if(IS_POST){
            M('Admin')->data(I("post."))->save();
            $this->mtReturn(200,'保存成功','',true);
        }
        $menu = D('Menu');
        $map['status'] = 1;
        $this->menu = node_merge($menu->getAllMenu($map));
        $Rs=M('Admin')->find(session('uid'));
        $this->assign('Rs', $Rs);
    	$this->display();
    }

    public function test(){
		$index = 'sdsdsds.sdssd';
		$arr   = explode('.', $index);
    	p($arr);
    }

    public function add(){
    	$this->display();
    }

    public function testForm(){
    	$this->display();
    }

    public function checkForm(){
    	$result = array();
        $result['statusCode'] = 200; 
        $result['message'] = '测试消息';
		$result['tabid'] = 'Index/testForm';
        $result['forward'] = '';
		$result['forwardConfirm']='';
        $result['closeCurrent'] = true;
    	header("Content-Type:text/html; charset=utf-8");
        exit(json_encode($result));
    }

}