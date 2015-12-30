<?php
/**
 * 广告控制器
 */

namespace Admin\Controller;
use Think\Controller;
class AdController extends CommonController {
    public function __construct(){
        parent::__construct();
        $this->dbname = CONTROLLER_NAME;
    }

    public function index(){
    	$model = M($this->dbname);
    	$pageCurrent = (isset($_REQUEST['pageCurrent']) && $_REQUEST['pageCurrent']) ? $_REQUEST['pageCurrent'] : 1;//当前页
    	$count = $model->where($map)->count();
    	if($count){
    		$numPerPage = C('PAGE_PER_NUMBER');
    		//$field = 'think_news.id, think_news.title, think_news.addtime, think_news.updatetime, think_news.status, think_cate.title cate';
    		$this->list = $model->where($map)->order('id desc')->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
    	}
    	$this->assign('totalCount', $count);//数据总数
        $this->assign('currentPage', $pageCurrent);//当前的页数，默认为1
        $this->assign('numPerPage', $numPerPage); //每页显示多少条
        cookie('_currentUrl_', __SELF__);
    	$this->display();
    }
    
    // public function _after_edit($vo){
    // 	if($vo['type']){
    // 		$vo['typeName'] = self::$arrType[$vo['type']];
    //         $vo['locaName'] = $this->getLocaName($vo['location']);
    // 	}
    // 	return $vo;
    // }

    // public function _befor_add(){
    //     $model = M('Cate');
    //     $map['remark'] = 'news';
    //     $result = $model->where($map)->find();
    //     if($result){
    //         $newsCateList = $model->where(array('pid' => $result['id'], 'status' => 1))->select();
    //         $this->newsCateList = $newsCateList;
    //     }
    // }

    // public function _befor_edit(){
    //     $this->_befor_add();
    // }
    
    /**
     * 广告类型
     * @var unknown
     */
    public static $arrType = array(
    		'0' => '单纯图片',
    		'1' => '新闻',
    		'2' => '通知',
    		'3' => '内链',
    		'4' => '外链'
    );

    public function getLocaName($id){
        $model = M('Cate');
        $info = $model->field('title')->find($id);
        return $info['title'];
    }
}