<?php
return array(
	//'配置项'=>'配置值'
	
	// 加载扩展配置文件 多个用,隔开
	'LOAD_EXT_CONFIG' => 'db',
	'URL_MODEL' => '2',		
	'URL_CASE_INSENSITIVE'  =>  false,//不区分大小写

	//权限验证设置
	'AUTH_CONFIG'=>array(
			'AUTH_ON' => true,
			'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
			'AUTH_GROUP' => 'think_auth_group',
			'AUTH_GROUP_ACCESS' => 'think_auth_group_access',
			'AUTH_RULE' => 'think_auth_rule',
			'AUTH_USER' => 'think_admin'
	),
	'NOT_AUTH_MODULE' => 'Login,Index,Public', // 默认无需认证模块
	//超级管理员id,拥有全部权限,只要用户uid在这个角色组里的,就跳出认证.可以设置多个值,如array('1','2','3')
	'ADMINISTRATOR' => array('1'),
		'UPLOAD_SITEIMG_QINIU' => array (
				'maxSize' => 5 * 1024 * 1024,//文件大小
				'rootPath' => './',
				'saveName' => array ('uniqid', ''),
				'driver' => 'Qiniu',
				'driverConfig' => array (
						'secrectKey' => 't4uG7FgB3ptYFOixymCvwHrBOQVAYflX2PGvOGqE',
						'accessKey' => 'x23VPxztl7izB3Xd7GaUVscBMBfOHa_m4CInLzGg',
						'domain' => '7xonlh.com1.z0.glb.clouddn.com',
						'bucket' => 'yangfan',
				)
		)
);