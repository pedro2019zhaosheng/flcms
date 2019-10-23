<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminRole extends Migrator
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
        $table = $this->table('admin_role', array('engine' => 'MyISAM', 'comment' => '角色表'));
        $table->addColumn('pid', 'integer', array('signed' => 'unsigned', 'default' => 0, 'comment' => '上级角色'))
            ->addColumn('name', 'string', array('default' => '', 'comment' => '角色名称'))
            ->addcolumn('description', 'string', array('default' => '', 'comment' => '角色描述'))
            ->addColumn('sort', 'integer', array('default' => 0, 'comment' => '排序'))
            ->addColumn('create_time', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_time', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addColumn('status', 'boolean', array('limit' => 2, 'default' => 1, 'comment' => '状态,0禁用'))
            ->addColumn('roletype', 'boolean', array('limit' => 1, 'default' => 1, 'comment' => '1,其他 0超管'))
            ->addIndex(['name'], ['unique' => true])
            ->create();
    }
}
