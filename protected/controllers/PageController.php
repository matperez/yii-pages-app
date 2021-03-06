<?php

class PageController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @var array
	 */
	public $pages;

	public function actions()
	{
		return array(
			'reorder'=>array(
				'class'=>'ext.actions.XReorderAction',
				'modelName'=>'Page'
			),
		);
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'viewPage'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'reorder'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * @param string $url0
	 * @param string $url1
	 * @param string $url2
	 * @throws CHttpException
	 */
	public function actionViewPage($url0, $url1 = null, $url2 = null) {
		if ($url2) {
			$url = $url2;
		} elseif ($url1) {
			$url = $url1;
		} elseif ($url0) {
			$url = ($url0);
		} else {
			throw new CHttpException(400,'Invalid request.');
		}
		$model = Page::model()->findByUrl($url);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		$this->pages = Pages::getMenuItems($model);
		$this->pageTitle = $model->title;
		$this->render('view',array(
			'breadcrumbs' => Pages::getBreadcrumbs($model),
			'model'=>$model,
		));
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model = $this->loadModel($id);
		$this->pages = Pages::getMenuItems($model);
		$this->pageTitle = $model->title;
		$this->render('view',array(
			'breadcrumbs' => Pages::getBreadcrumbs($model),
			'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Page;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Page']))
		{
			$model->attributes=$_POST['Page'];
			if($model->save()) {
				$parent = Page::model()->findByPk($model->parentId);
				$model->move($parent);
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}



	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id, true);
		$model->parentId = $model->getParentId();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Page']))
		{
			$model->attributes=$_POST['Page'];
			if($model->save()) {
				$parent = Page::model()->findByPk($model->parentId);
				$model->move($parent);
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$baseCriteria = new CDbCriteria();
		$baseCriteria->compare('active', 1);
		$basePages = Page::model()->getRoots($baseCriteria);
		$this->pages = Pages::getMenuItems();
		$this->render('index',array(
			'basePages' => $basePages,
			'breadcrumbs' => Pages::getBreadcrumbs()
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Page('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Page']))
			$model->attributes=$_GET['Page'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @param bool $ml
	 * @throws CHttpException
	 * @return Page the loaded model
	 */
	public function loadModel($id, $ml=false) {
		if ($ml) {
			$model = Page::model()->multilang()->findByPk((int) $id);
		} else {
			$model = Page::model()->findByPk((int) $id);
		}
		if ($model === null)
			throw new CHttpException(404, 'The requested post does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Page $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='page-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	/**
	 * Get pages list for dropdown control
	 * @param integer $id
	 * @return array
	 */
	public function getTreeList($id = null) {
		$list = Page::model()->getTreeList($id);
		$list = array_map(function($item) {
			if ($item['level']) {
				$item['title'] = str_repeat('-', $item['level']) . ' ' . $item['title'];
			}
			return $item;
		}, $list);
		return $list;
	}
}
