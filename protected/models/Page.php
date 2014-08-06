<?php

/**
 * This is the model class for table "pages".
 *
 * The followings are the available columns in table 'pages':
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property string $content
 * @property string $description
 * @property string $keywords
 * @property integer $active
 * @property string $path
 * @property integer $position
 * @property integer $parentId
 * @property integer $level
 * @property bool $hasChildren
 * @property Page[] $children
 * @property MultilingualBehavior $ml
 */
class Page extends CActiveRecord
{
	/**
	 * @var integer
	 */
	public $parentId;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pages';
	}

	public function behaviors() {
		return [
			'MaterializedPath' => [
				'class' => 'ext.behaviors.MaterializedPathTree',
			],
			'ml' => array(
				'class' => 'ext.behaviors.MultilingualBehavior',
				'langTableName' => 'pagesLang',
				'langForeignKey' => 'page_id',
				'localizedAttributes' => array('title', 'content', 'url'), //attributes of the model to be translated
				//'localizedPrefix' => 'l_',
				'languages' => Yii::app()->params['translatedLanguages'], // array of your translated languages. Example : array('fr' => 'Français', 'en' => 'English')
				'defaultLanguage' => Yii::app()->params['defaultLanguage'], //your main language. Example : 'fr'
				//'createScenario' => 'insert',
				//'localizedRelation' => 'i18nPost',
				//'multilangRelation' => 'multilangPost',
				//'forceOverwrite' => false,
				//'forceDelete' => true,
				//'dynamicLangClass' => true, //Set to true if you don't want to create a 'PostLang.php' in your models folder
			),
		];
	}

	public function defaultScope()
	{
		return $this->ml->localizedCriteria();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			array('active, position, level', 'numerical', 'integerOnly'=>true),
			array('title, path, url', 'length', 'max'=>255),
			array('content, description, keywords, path, level, position, url, parentId', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, content, description, keywords, active, path, position, level, url', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * Get pages list for dropdown control
	 * @param integer $id
	 * @return array
	 */
	public function getTreeList($id = null) {
		$query = $this->dbConnection->createCommand()
			->select('*')
			->from('pages')
			->where('active = 1')
			->order('path, position');
		if ($id) {
			$query->andWhere('id <> :id', [':id' => $id]);
		}
		$result = $query->queryAll();
		$baseFolders = array_filter($result, function($item) {
			return $item['path'] == '.';
		});
		$list = array();
		foreach($baseFolders as $baseFolder) {
			$subTree = array_filter($result, function($item) use ($baseFolder) {
				return 0 === strpos($item['path'], ".{$baseFolder['id']}.");
			});
			$list[] = $baseFolder;
			$list = array_merge($list, $subTree);
		}
		return $list;
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'content' => 'Content',
			'description' => 'Description',
			'keywords' => 'Keywords',
			'active' => 'Active',
			'path' => 'Path',
			'position' => 'Position',
			'level' => 'Level',
			'url' => 'Url',
			'parentId' => 'Parent',
		);
	}

	/**
	 * @param string $url
	 * @throws CHttpException
	 * @return Page
	 */
	public function findByUrl($url) {
		$query = $this->dbConnection->createCommand()
			->select('*')
			->from('pagesLang')
			->where('l_url = :url', [':url' => $url]);
		$result = $query->queryRow();
		if (!$result)
			throw new CHttpException(404, 'Page not found.');
		$lang = $result['lang_id'];
		$languages = Yii::app()->params['translatedLanguages'];
		if (!$lang || !array_key_exists($lang, $languages))
			throw new CHttpException(404, 'Page not found.');
		$model = Page::find('id = :id AND active = 1', [':id' => $result['page_id']]);
		if (!$model)
			throw new CHttpException(404, 'Page not found.');
		// @todo перенести установку языка в отдельный хелпер
		Yii::app()->setLanguage($lang);
		Yii::app()->session->add('language', $lang);
		return $model;
	}

	public function beforeSave() {
		if (!$this->url) {
			$this->url = Translit::text($this->title);
		}
		return parent::beforeSave();
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('keywords',$this->keywords,true);
		$criteria->compare('active',$this->active);
		$criteria->compare('path',$this->path,true);
		$criteria->compare('position',$this->position);
		$criteria->compare('level',$this->level);
		$criteria->compare('url',$this->url);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Page the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
