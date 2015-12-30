<?php
namespace Home\Controller;
use Think\Controller;
use Lib\Aes;
class CommController extends Controller {
    protected $uid;
    public function __construct(){
        parent::__construct();
        //$uid = cookie('uid');
        $uid = 3;
        if(!isset($uid) || empty($uid)){
            $get = I('get.');
            if(isset($get['code']) && $get['code']){
                $aes = new Aes();
                $code = $aes->decrypt($get['code']);
                $uid = $code;
                cookie('uid', $code);
            } else {
                //$this->error("请在微信端打开网页");
                //exit;
                //redirect($_SERVER['HTTP_HOST'] . '/weixin/web.php');
                cookie('url', $_SERVER['HTTP_REFERER']);
                $url = $_SERVER['HTTP_HOST'] . '/weixin/web.php';
                //var_dump($url);die;
                header("Location:http://" . $url);
            }
        }
        $this->uid = $uid;
    }

    /**
     * 获取会员信息
     * @param $map
     * @param $field
     * @return mixed
     */
    protected function getMemberInfo($field, $map = NULL){
        $model = D('Member');
        $map = empty($map) ?  array('id' => $this->uid) : $map;
        return $model->getMemberInfoByMap($map, $field);
    }

    /**
     * 设置会员信息
     * @param $map
     * @param $field
     * @return mixed
     */
    protected function setMemberInfo($field, $map = NULL){
        $model = D('Member');
        $map = empty($map) ? array('id' => $this->uid) : $map;
        return $model->setMemberInfoByMap($map, $field);
    }
}