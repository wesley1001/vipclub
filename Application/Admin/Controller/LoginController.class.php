<?php
/**
 * 后台登陆控制器
 */
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->display();
    }

    public function checkLogin(){
        if(IS_POST){
            $post = I('post.');
            $result = $this->checkUsername($post);
            if($result['status']){
                $userInfo = $result['info'];//个人信息
                //session存储，放于页面底部
                session('uid',$userInfo['id']);
                session('username',$userInfo['username']);
                session("truename",$userInfo['truename']);
                session('group',$userInfo['group']);
                session('loginip',get_client_ip());
                session('logintime',date("Y-m-d H:i:s",time()));
                //添加登陆时间、IP、次数
                $data['id'] = $userInfo['id'];
                $data['logintime'] = date("Y-m-d H:i:s",time());
                $data['loginip'] = get_client_ip();
                $data['number'] = array('exp','number+1');
                M("Admin")->save($data);

                //添加登陆日志
                $dat['username'] = $userInfo['username'];
                $dat['content'] = '登录成功！';
                $dat['os'] = $_SERVER['HTTP_USER_AGENT'];
                $dat['url'] = U();
                $dat['addtime'] = date("Y-m-d H:i:s",time());
                $dat['ip'] = get_client_ip();
                M("Log")->add($dat);

                $this->success('登陆成功，正在跳转...', U('Index/index'));
            } else {
                $this->error($result['msg']);
            }
        }
    }

    /**
     * 检测用户名是否存在
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    private function checkUsername($data){
        $arr = array('status' => 1);
        if($data){
            $model = D('Admin');
            $map['username'] = $data['username'];
            $result = $model->where($map)->find();
            if($result){
                if($result['status'] == 1){
                    if($result['password'] !== $this->encrypt($data['password'])){
                        $arr = array('status' => 0, 'msg' => '密码不正确');
                    }
                } else {
                    $arr = array('status' => 0, 'msg' => '用户被锁定，不允许登陆');
                }
            } else {
                $arr = array('status' => 0 ,'msg' => '用户不存在');
            }
        } else {
            $arr = array('status' => 0, 'msg' => '用户名不能为空');
        }
        if($arr['status'] == 1){
            $arr = array('status' => 1, 'info' => $result);
        }
        return $arr;
    }

    /**
     * 加密
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    private function encrypt($str){
        return md5(md5($str));
    }

    //注销
    public function logout() {
        $dat['username'] = session('username');
        $dat['content'] = '退出成功！';
        $dat['os'] = $_SERVER['HTTP_USER_AGENT'];
        $dat['url'] = U();
        $dat['addtime'] = date("Y-m-d H:i:s",time());
        $dat['ip'] = get_client_ip();
        M("Log")->add($dat);
        session_unset();
        session_destroy();
        redirect(U('Admin/Login/index'));
    }
}