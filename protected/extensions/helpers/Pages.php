<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 04.08.14
 * Time: 20:21
 */

class Pages {

	/**
	 * Get page url
	 * @param Page|MaterializedPathTree $model
	 * @return string
	 */
	public static function getUrl($model = null) {
		if (!$model)
			return Yii::app()->createUrl('page/index');
		$parents = $model->getParents();
		$parents[] = $model;
		$count = count($parents);
		$urls = [];
		for ($i = 0; $i < $count; $i++) {
			$parent = $parents[$i];
			$urls['url'.$i] = $parent->url;
		}
		$url = Yii::app()->createUrl('page/viewPage', $urls);
		return $url;
	}

	/**
	 * Get page breadcrumbs
	 * @param Page|MaterializedPathTree $model
	 * @return array
	 */
	public static function getBreadcrumbs($model = null) {
		$breadcrumbs = [
			'Справочная'=>array('index'),
		];
		if (!$model) {
			return $breadcrumbs;
		}
		$parents = $model->getParents();
		foreach($parents as $parent) {
			/** @var Page|MaterializedPathTree $parent */
			$breadcrumbs[$parent->title] = self::getUrl($parent);
		}
		array_push($breadcrumbs, $model->title);
		return $breadcrumbs;
	}

	/**
	 * Map page to menu item
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
	 * Get pages menu tree
	 * @param Page|MaterializedPathTree $root - коревой элемент. если указан, выводятся только его элементы
	 * @param Page|MaterializedPathTree $page - текущая страница
	 * @param bool $digDeep - если true, меню будет получено до конца безотносительно текущей страницы
	 * @return array
	 */
	public static function getMenuItems($page = null, $digDeep = false, $root = null) {
		$data = [];
		if ($root && is_object($root)) {
			$data[] = self::getData($root, $page, $digDeep);
		} else {
			$roots = Page::model()->getRoots([
				'condition' => 'active = 1',
				'order' => 'position'
			]);
			foreach($roots as $root) {
				$data[] = self::getData($root, $page, $digDeep);
			}
		}
		return $data;
	}
} 