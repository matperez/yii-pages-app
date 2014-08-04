<?php
/* @var $this PageController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Pages',
);

$this->menu=array(
	array('label'=>'Create Page', 'url'=>array('create')),
	array('label'=>'Manage Page', 'url'=>array('admin')),
);
?>

<h1>Справочная</h1>

<?php
$this->widget('zii.widgets.CMenu', array(
	'items'=>Pages::getMenuItems(null, true),
	'htmlOptions'=>array('class'=>'pages'),
));
?>