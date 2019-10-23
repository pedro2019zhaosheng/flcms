<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CmsAd extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('cms_ad', array('engine' => 'MyISAM', 'comment' => '广告表'));
        $table->addColumn('name', 'string', array('default' => '', 'comment' => '广告名称'))
            ->addcolumn('ad_type', 'integer', array('comment' => '广告类型id'))
            ->addColumn('abstract', 'string', array('null'=>true,'default'=>null, 'comment' => '简介'))
            ->addColumn('url', 'string', array('null'=>true,'default'=>null, 'comment' => '广告链接'))
            ->addColumn('img', 'string', array('comment' => '广告位图片'))
            ->addColumn('sort', 'integer', array('default'=>0,'comment' => '排序'))
            ->addColumn('status', 'boolean', array('default'=>0,'comment' => '状态  0：禁用   1：启用'))
            ->addColumn('is_del', 'boolean', array('default'=>0,'comment' => '是否删除  0：否   1：是'))
            ->addColumn('create_time', 'datetime', array('null' =>true,'comment' => '创建时间'))
            ->addColumn('update_time', 'datetime', array('null'=>true,'comment' => '更新时间'))
            ->addColumn('delete_time', 'datetime', array('null'=>true,'comment' => '删除时间'))
            ->create();
    }
}
