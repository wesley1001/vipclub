<?php
	namespace PHPSTORM_META {
	/** @noinspection PhpUnusedLocalVariableInspection */
	/** @noinspection PhpIllegalArrayKeyTypeInspection */
	$STATIC_METHOD_TYPES = [

		\D('') => [
			'Menu' instanceof Admin\Model\MenuModel,
			'Adv' instanceof Think\Model\AdvModel,
			'Mongo' instanceof Think\Model\MongoModel,
			'View' instanceof Think\Model\ViewModel,
			'Cate' instanceof Admin\Model\CateModel,
			'Relation' instanceof Think\Model\RelationModel,
			'Ad' instanceof Admin\Model\AdModel,
			'Merge' instanceof Think\Model\MergeModel,
		],
	];
}