<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminAuth extends Migrator
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
        $table = $this->table('admin_auth', array('engine' => 'InnoDB', 'comment' => '角色权限表'));
        $table->addColumn('role_id', 'integer', array('default' => 0, 'comment' => '角色ID'))
            ->addColumn('menu_id', 'integer', array('default' => 0, 'comment' => '节点ID'))
            ->addIndex(['role_id'])
            ->addIndex(['menu_id'])
            ->create();
    }
}
