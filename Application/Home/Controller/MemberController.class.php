<?php
/**
 * User: yff
 * Date: 16/1/4
 * Time: 下午9:34
 */
namespace Home\Controller;
use Think\Controller;
class MemberController extends CommController {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $model = M('Member');
        $memberInfo = $model->find($this->uid);
        $this->memberInfo = $memberInfo;
        $this->display();
    }

    public function myAccount() {
        $model = M('Receive r');
        $map['r.uid'] = $this->uid;
        $field = "r.id, a.account, a.password, c.title";
        $list = $model->where($map)->join('left join think_account a on a.id = r.account_id')
                ->join('left join think_cate c on c.id = a.cate')->field($field)->order('r.id desc')->select();
        $this->list = $list;
        $this->display();
    }
}