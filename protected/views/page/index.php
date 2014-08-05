<?php
/* @var $this PageController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Справочная',
);

$this->menu=array(
	array('label'=>'Create Page', 'url'=>array('create')),
	array('label'=>'Manage Page', 'url'=>array('admin')),
);
?>

<h1>Справочная</h1>


<?php foreach($basePages as $root): ?>
	<h2><?=CHtml::encode($root->title)?></h2>
	<?php
	$this->widget('zii.widgets.CMenu', array(
		'items'=> Pages::getMenuItemsFromRoot($root),
		'htmlOptions'=>array('class'=>'root'),
	));
	?>
<?php endforeach; ?>
