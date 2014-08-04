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

<?php foreach($basePages as $basePage): ?>
	<div class="page base">
		<h2><?= $basePage->title;?></h2>
		<ul class="childs">
			<?php foreach($basePage->children as $childPage): ?>
				<?php if($childPage->hasChildren): ?>
					<ul>
						<?php foreach($childPage->children as $subPage): ?>
							<li><?= CHtml::link($subPage->title, ['page/viewPage', 'url' => $subPage->url]) ?></li>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<li><?= CHtml::link($childPage->title, ['page/viewPage', 'url' => $childPage->url]) ?></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endforeach; ?>