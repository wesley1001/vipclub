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
        $this->display();
    }
}