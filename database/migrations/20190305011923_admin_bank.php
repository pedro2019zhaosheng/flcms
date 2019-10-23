<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminBank extends Migrator
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
        $table = $this->table('admin_bank', array('engine' => 'MyISAM', 'comment' => '银行表'));
        $table->addcolumn('name', 'string', array('default' => '', 'comment' => '银行名称'))
            ->addColumn('code', 'string', array('default' => '', 'comment' => '银行编码'))
            ->addColumn('status', 'boolean', array('limit' => 1, 'default' => 1, 'comment' => '状态 ： 0：禁用  1：启用'))
            ->addIndex('code')
            ->create();
    }
}
