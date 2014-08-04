<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 04.08.14
 * Time: 20:21
 */

class Pages {

	/**
	 * @param Page|MaterializedPathTree $item
	 * @param Page|MaterializedPathTree $model
	 * @param bool $digDeep
	 * @return array
	 */
	public static function getData($item, $model = null, $digDeep = false) {
		$data = array(
			'label' => $item->title,
			'itemOptions' => ['class'=>'level'.$item->level]
		);
		$parents = $item->getParents();
		$parents[] = $item;
		$urls = [];
		for ($i = 0; $i < count($parents); $i++) {
			/** @var Page|MaterializedPathTree $parent */
			$parent = $parents[$i];
			$urls['url'.$i] = $parent->url;
		}
		$data['url'] = Yii::app()->createUrl('page/viewPage', $urls);
		$itemIsActive = $model && in_array($item->id, $model->getParentIds()) || $model && $item->id == $model->id;
		if ($itemIsActive) {
			$data['active'] = true;
		}
		if ($item->hasChildren && $itemIsActive || $digDeep) {
			foreach($item->children as $child) {
				$data['items'][] = self::getData($child, $model, $digDeep);
			}
		}
		return $data;
	}

	/**
	 * @param Page|MaterializedPathTree $page
	 * @param bool $digDeep
	 * @return array
	 */
	public static function getMenuItems($page = null, $digDeep = false) {
		$data = [];
		$roots = Page::model()->getRoots([
			'condition' => 'active = 1',
			'order' => 'position'
		]);
		foreach($roots as $root) {
			$data[] = self::getData($root, $page, $digDeep);
		}
		return $data;
	}
} 