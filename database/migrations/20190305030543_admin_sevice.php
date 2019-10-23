<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminSevice extends Migrator
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
        $table = $this->table('admin_service', array('engine' => 'MyISAM', 'comment' => '客服表'));
        $table->addColumn('name', 'string', array('default' => '', 'comment' => '客服名称'))
            ->addcolumn('icon', 'string', array('default'=>'', 'comment' => '客服标识'))
            ->addColumn('num', 'string', array('default'=>'', 'comment' => '客户号码'))
            ->addColumn('img', 'string', array('default' =>'', 'comment' => '二维码'))
            ->addColumn('status', 'boolean', array('limit'=>1,'default' =>0,'comment' => '状态  0：禁用  1：启用'))
            ->addColumn('create_time', 'datetime', array('null' =>true,'comment' => '创建时间'))
            ->addColumn('update_time', 'datetime', array('null'=>true,'comment' => '更新时间'))
            ->create();
    }
}
