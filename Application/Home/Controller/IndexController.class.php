<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommController {
    public function index(){
        $get = I('get.');
        if(isset($get['cate'])){
            if('all' !== $get['cate']){
                $map['cate'] = $get['cate'];
            }
        }
        $model = M('Account');
        $map['status'] = 1;
        $map['num'] = array('lt', 1000);
        $list = $model->where($map)->order('sort desc')->select();
        if($list){
            foreach ($list as $key => $val) {
                $result = $this->checkUserReceive($val['id']);
                $list[$key]['is_receive'] = $result ? 1 : 0;
            }
        }
        $this->get = $get;
        $this->list = $list;
        $this->cateList = $this->getCateList();
        $this->adList = $this->getAdList();
        $this->display();
    }

    public function getPassword() {
        if(IS_AJAX){
            $id = I('post.id');
            if($id){
                $result = $this->checkUserReceive($id);
                if($result){
                    return $this->ajaxReturn(array('status' => 0, 'message' => '您已领取了该账号,不能再次领取'));
                }
                $model = M('Account');
                $info = $model->find($id);
                $model->where(array('id' => $info['id']))->setInc('num');

                $insertData['uid'] = $this->uid;
                $insertData['account_id'] = $id;
                $insertData['addtime'] = time();
                $receive = M('Receive');
                $receive->add($insertData);

                return $this->ajaxReturn(array('status' => 1, 'password' => $info['password']));
            }
        }
    }

    private function checkUserReceive($account_id) {
        $model = M('Receive');
        $map['uid'] = $this->uid;
        $map['account_id'] = $account_id;
        return $model->where($map)->find();
    }

    private function getAdList() {
        $model = M('Ad');
        $map['status'] = 1;
        $list = $model->where($map)->order('id desc')->limit(5)->select();
        return $list;
    }

    /**
     * 获取分类列表
     * @return mixed
     */
    private function getCateList() {
        $model = M('Cate');
        $map['remark'] = 'account';
        $map['status'] = 1;
        $parent = $model->where($map)->find();
        $where['pid'] = $parent['id'];
        $where['status'] = 1;
        $list = $model->where($where)->order('sort desc')->select();
        return $list;
    }
}