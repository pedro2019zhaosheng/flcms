<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Debug extends Migrator
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
//        $table = $this->table('debug', array('engine' => 'MyISAM', 'comment' => '调试表'));
//        $table->addColumn('module', 'string', array('default' => '', 'comment' => '请求模块'))
//            ->addcolumn('controller', 'string', array('comment' => '请求控制器'))
//            ->addColumn('action', 'string', array('comment' => '请求方法'))
//            ->addColumn('get', 'string', array('comment' => '请求路径'))
//            ->addColumn('post', 'string', array('comment' => '请求参数'))
//            ->addColumn('create_time', 'datetime', array('null' =>true,'comment' => '创建时间'))
//            ->addColumn('update_time', 'datetime', array('null'=>true,'comment' => '更新时间'))
//            ->create();
    }
}
