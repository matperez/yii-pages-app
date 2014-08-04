<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 04.08.14
 * Time: 16:12
 */


class TreeCommand extends CConsoleCommand {
	public function actionIndex() {
		$root = Page::model()->findByPk(6);
		/** @var Page $root */
		$root->loadTree();
		$children = $root->getChildren();
		var_dump($root->id, $root->title, $root->getChildren());
	}
} 