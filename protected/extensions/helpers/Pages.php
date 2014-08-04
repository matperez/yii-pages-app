<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 04.08.14
 * Time: 20:21
 */

class Pages {

	/**
	 * @param Page|MaterializedPathTree $page
	 * @return array
	 */
	public static function getMenuItems($page = null) {
		$parentIds = $page ? $page->getParentIds() : [];
		$roots = Page::model()->getRoots([
			'condition' => 'active = 1',
			'order' => 'position'
		]);
		$items = array_map(function($item) use ($page, $parentIds) {
			/** @var Page|MaterializedPathTree $item */
			$data = [
				'label' => $item->title,
				'url' => Yii::app()->createUrl('page/viewPage', ['url' => $item->url])
			];
			if ($page && ($item->isParent($page) || $item->id == $page->id)) {
				$data['active'] = true;
			}
			if (!$page && $item->hasChildren || $page && $item->id == $page->id || $page && in_array($item->id, $parentIds)) {
				$data['items'] = array_map(function($child) use ($page) {
					$data = [
						'label' => $child->title,
						'url' => Yii::app()->createUrl('page/viewPage', ['url' => $child->url])
					];
					if ($page && $child->id == $page->id) {
						$data['active'] = true;
					}
					return $data;
				}, $item->children);
				return $data;
			}
			return $data;
		}, $roots);
		return $items;
	}
} 