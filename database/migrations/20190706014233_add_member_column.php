<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddMemberColumn extends Migrator
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
        $table = $this->table('member');
        $table->removeIndex(['role', 'top_id', 'frozen', 'is_delete'])
            ->addIndex('top_id')
            ->addIndex('frozen')
            ->addIndex('is_delete')
            ->addIndex('role')
            ->changeColumn('last_login_time', 'datetime', ['null' => true, 'comment' => 'App最后登录时间'])
            ->changeColumn('last_login_ip', 'char', ['limit' => 20, 'default' => '', 'comment' => 'App最后登录IP'])
            ->addColumn('backend_last_login_time', 'datetime', ['null' => true, 'comment' => '代理商后台最后登录时间'])
            ->addColumn('backend_last_login_ip', 'char', ['limit' => 20, 'default' => '', 'comment' => '代理商后台最后登录IP'])
            ->update();
    }
}
