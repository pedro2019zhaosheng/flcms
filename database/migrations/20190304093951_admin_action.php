<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminAction extends Migrator
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
       /*$table = $this->table('admin_action', array('engine' => 'MyISAM', 'comment' => '系统行为表'));
        $table->addcolumn('module', 'string', array('limit' => 16, 'default' => '','null' => false, 'comment' => '所属模块名'))
            ->addColumn('name', 'string', array('limit' => 32, 'default' => '','null' => false, 'comment' => '行为唯一标识'))
            ->addColumn('title', 'string', array('limit' => 80, 'default' => '','null' => false, 'comment' => '行为标题'))
            ->addcolumn('remark', 'string', array('limit'=>128,'default' => '', 'comment' => '行为描述'))
            ->addColumn('rule', 'text', array('comment' => '行为规则'))
            ->addColumn('log', 'text', array('comment' => '日志规则'))
            ->addColumn('status', 'boolean', array('limit'=>2,'default' => 0,'null'=>false, 'comment' => '状态'))
            ->addColumn('create_time', 'datetime', array('comment' => '创建时间', 'null' => true))
            ->addcolumn('update_time', 'datetime', array('comment' => '更新时间', 'null' => true))
            ->addIndex(array('id', 'module'))
            ->create();*/
    }
}
