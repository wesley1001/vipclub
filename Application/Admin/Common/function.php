<?php
/**
 * 后台公共函数库
 */

/**
 * 获取图片等比例高
 * @param unknown $img
 * @return number
 */
function getPicHeight($img){
	if(file_exists($img)){
		$info = getimagesize($img);
		$width = C('THUB_WIDTH');
		$prop = $info[1] / $info[0];
		return $width * $prop;
	}
}

/**
 * 检查权限
 * @param name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
 * @param uid  int           认证用户的id
 * @param string mode        执行check的模式
 * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
 * @return boolean           通过验证返回true;失败返回false
 */
function authcheck($name, $uid, $type=1, $mode='url', $relation='or'){
	$tmp = explode('/', $name);
	$modular = $tmp ? $tmp[1] : '';//当前模块

	$noCheckModu = explode(',', C('NOT_AUTH_MODULE'));//免验证模块
	if(!in_array($uid,C('ADMINISTRATOR')) && !in_array($modular, $noCheckModu)){
		$auth=new \Think\Auth();
		return $auth->check($name, $uid, $type, $mode, $relation)?true:false;
	}else{
		return true;
	}
}

/**
 * 获取时间
 * @return [type] [description]
 */
function getTime(){
	return date('Y-m-d H:i:s');
}

/**
 * 按照父子关系处理菜单数据
 * @param  [type] $menu [description]
 * @return [type]       [description]
 */
function node_merge($node, $access = null, $pid = 0){
	$arr = array();

	foreach($node as $v){
		if(is_array($access)){
			$v['access'] = in_array($v['id'], $access) ? 1 : 0;
		}

		if($v['pid'] == $pid){
			$v['child'] = node_merge($node, $access, $v['id']);
			$arr[] = $v;
		}
	}

	return $arr;
}

/**
 * 合并处理父子关系数据
 * @param  [type]  $cate  [description]
 * @param  string  $html  [description]
 * @param  integer $pid   [description]
 * @param  integer $level [description]
 * @return [type]         [description]
 */
function unlimitedForLevel($cate, $html = '--', $pid = 0, $level = 0){
	$arr = array();
	foreach ($cate as $key => $v) {
		if($v['pid'] == $pid){
			$v['level'] = $level;
			$v['html'] = str_repeat($html, $level);
			$arr[] = $v;
			$arr = array_merge($arr, unlimitedForLevel($cate, $html, $v['id'], $level + 1));
		}
	}
	return $arr;
}

/**
 * 生成父子关系
 * @param  integer $pid   [description]
 * @param  integer $level [description]
 * @param  integer $db    [description]
 * @return [type]         [description]
 */
function cateTree($pid=0,$level=0,$db=0){
	$cate = M(''.$db.'');
	$array = array();
	$tmp = $cate->where(array('pid'=>$pid))->order("sort")->select();

	if(is_array($tmp) && !empty($tmp)){
		foreach($tmp as $v){
			$v['level'] = $level;
			$array[] = $v;

			$sub = cateTree($v['id'], $level+1, $db);
			if(is_array($sub) && !empty($sub)){
				$array=array_merge($array,$sub);
			}
		}
	}
	return $array;
}
