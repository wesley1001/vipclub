<?php
/**
 * 免验证控制器
 */
namespace Admin\Controller;
use Think\Controller;
use Lib\ImgHandle;//生成略缩图类
use Lib\Letv;
class PublicController extends Controller {
    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $this->display();
    }
    
    //加载Letv上传视频
    public function uploadLetv(){
    	if(IS_POST){
    		$videoName = I('videoName');
    		$letv = new Letv();
    		$result = $letv->uploadFlash($videoName, 'video_upload_success', 450, 270);
    		echo $result;
    	}
    }

    //修改密码
    public function changepwd() {
        if(IS_POST){
            $password=I('post.password');
            $map = array();
            if(I('post.password')!=I('post.repassword')){
                $data['statusCode']=300;
                $data['message']='两次输入密码不一致！';
            }
            $map['password'] = md5(md5((I('post.oldpassword'))));
            $map['id'] = session('uid');
            $User = M("Admin");
            if(!$User->where($map)->field('id')->find()) {
                $data['statusCode']=300;
                $data['message']='旧密码不符或者用户名错误！';     
            } else {
                if (empty($password) || strlen($password) < 5) {
                    $data['statusCode']=300;
                    $data['message']='密码长度必须大于6个字符！';
                }else{
                    $User->password =md5(md5(($password)));
                    $User->save();
                    $data['statusCode']=200;
                    $data['message']='密码修改成功！';
                    $data['tabid']=$_REQUEST['navTabId'];
                    $data['closeCurrent']='true';
                    $data['forward']='';
                    $data['forwardConfirm']='';
                }    
            }
            $this->dwzajaxReturn($data);
        }else{
          $this->display(); 
        }
    }

    //修改个人资料
    public function changeinfo() {
        $id=session('uid');
        $rs=M('Admin')->find($id);
        if(IS_POST){
            M('Admin')->save(I('post.'));
            $data['statusCode']=200;
            $data['message']='资料修改成功！';
            $data['tabid']=$_REQUEST['navTabId'];
            $data['closeCurrent']='true';
            $data['forward']='';
            $this->dwzajaxReturn($data);
        }else{
           $this->assign('id', $id);
           $this->assign('Rs', $rs);
           $this->display(); 
        }
    }

    //图片上传
    public function uploadImg(){
        $type = I('get.type');
        $thumb = I('get.thumb');//是否生成缩略图
        switch ($type) {
            case 'flow':
                $path = 'flow/';
                break;
            case 'server':
                $path = 'server/';
                break;
            case 'ad':
                $path = 'ad/';
                break;
            case 'head':
                $path = 'head/';
                break;
            case 'act':
                $path = 'act/';
                break;
            default:
                $path = '';
                break;
        }
        $rootBase = C('UPLOADS_DIR');//根目录
        $save = '/admin/';
        $config = array(   
            'maxSize'    =>    3145728000,
            'savePath'   =>    $save . $path,  
            'saveName'   =>    array('uniqid',''), 
            'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),  
            'autoSub'    =>    true,   
            'subName'    =>    array('date','Ymd'),
        );
        $upload = new \Think\Upload($config);// 实例化上传类
        $images = $upload->upload();
        //判断是否有图
        if($images){
        	if($thumb){
        		$thumb = $this->getThumb($rootBase . $images['file']['savepath'].$images['file']['savename']);
        		$info =  $images['file']['savepath'] . $thumb;
        		$result = array(
        				'statusCode' => 200,
        				'filename' => $info
        		);
        	} else {
        		$info = $images['file']['savepath'].$images['file']['savename'];
        		$result = array(
        				'statusCode' => 200,
        				'filename' => $images['file']['savepath'].$images['file']['savename']
        		);
        	}
        }else{
            //$this->error($upload->getError());//获取失败信息
            $result = array(
                'statusCode' => 300,
                'message' => $upload->getError()
            );
        }
        echo json_encode($result);
    }
    
    public function uploadVideo(){
    	$path = 'video/';
    	$rootBase = C('UPLOADS_DIR');//根目录
    	$save = '/admin/';
    	$config = array(
    			'maxSize'    =>    3145728000000,
    			'savePath'   =>    $save . $path,
    			'saveName'   =>    array('uniqid',''),
    			'exts'       =>    array('mp4', 'wma', 'rmvb', 'flv'),
    			'autoSub'    =>    true,
    			'subName'    =>    array('date','Ymd'),
    	);
    	$upload = new \Think\Upload($config);// 实例化上传类
    	$video = $upload->upload();
    	if($video){
    		$info = $video['file']['savepath'].$video['file']['savename'];
    		$result = array(
    				'statusCode' => 200,
    				'filename' => $video['file']['savepath'].$video['file']['savename']
    		);
    	}else{
    		//$this->error($upload->getError());//获取失败信息
    		$result = array(
    				'statusCode' => 300,
    				'message' => $upload->getError()
    		);
    	}
    	echo json_encode($result);
    }
    
    /**
     * 生成缩略图
     * @param unknown $img
     * @return unknown
     */
    private function getThumb($imgSrc){
    	$height = getPicHeight($imgSrc);
    	$img = new ImgHandle();
    	$thumb = $img->generateThumb($imgSrc, C('THUB_WIDTH'), $height);
    	return $thumb;
    }

    public function uploadFile(){
        $path = 'file/';
        $save = '/admin/';
        $config = array(   
            'maxSize'    =>    3145728,
            'savePath'   =>    $save . $path,  
            'saveName'   =>    array('uniqid',''), 
            'exts'       =>    array('jpg', 'gif', 'png', 'jpeg', 'xls'),  
            'autoSub'    =>    true,   
            'subName'    =>    array('date','Ymd'),
        );
        $upload = new \Think\Upload($config);// 实例化上传类
        $file = $upload->upload();
        if($file){
            $info = $file['file']['savepath'].$file['file']['savename'];
            $result = array(
                'statusCode' => 200,
                'filename' => $file['file']['savepath'].$file['file']['savename']
            );
        }else{
            //$this->error($upload->getError());//获取失败信息
            $result = array(
                'statusCode' => 300,
                'message' => $upload->getError()
            );
        }
        echo json_encode($result);
    }

    protected function dwzajaxReturn($data, $type='') {   

        $dat['username'] = session('username');
        $dat['content'] = $data['message'];
        $dat['os'] = $_SERVER['HTTP_USER_AGENT'];
        $dat['url'] = U();
        $dat['addtime'] = date("Y-m-d H:i:s",time());
        $dat['ip'] = get_client_ip();
        M("log")->add($dat);
        
        $result = array();
        $result['statusCode'] = $data['statusCode'];
        $result['tabid'] = $data['tabid'];
        $result['closeCurrent'] = $data['closeCurrent'];
        $result['message'] = $data['message'];
        $result['forward'] = $data['forward'];
        $result['forwardConfirm']=$data['forwardConfirm'];


       
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
}