<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 04.08.14
 * Time: 16:12
 */


class TreeCommand extends CConsoleCommand {
	public function actionIndex() {
		$items = Pages::getMenuItems();
		CVarDumper::dump($items);
	}
} 