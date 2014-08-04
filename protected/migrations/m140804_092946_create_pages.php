<?php

class m140804_092946_create_pages extends CDbMigration
{
	public function up()
	{
		$this->createTable('pages', [
			'id' => 'pk',
			'title' => 'string NOT NULL',
			'url' => 'string NOT NULL',
			'content' => 'text',
			'description' => 'text',
			'keywords' => 'text',
			'active' => 'boolean DEFAULT 1',
			'path' => 'string DEFAULT \'.\'',
			'position' => 'integer DEFAULT 0',
			'level' => 'integer DEFAULT 0',
		]);
	}

	public function down()
	{
		$this->dropTable('pages');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}