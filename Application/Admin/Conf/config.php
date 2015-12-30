<?php
return array(
	//'配置项'=>'配置值'
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__'      => __ROOT__ . '/Public/'.MODULE_NAME,
	),
	'SEARCHKEY' => array('name', 'title'),//后台允许搜索的项
	'THUB_WIDTH' => 200, //生成缩略图宽度
	'UPLOADS_DIR' => __ROOT__ .'/Uploads/', //保存图片根目录
	'PAGE_PER_NUMBER' => 10,
);