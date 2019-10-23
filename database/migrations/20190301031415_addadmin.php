<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Addadmin extends Migrator
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
        $table = $this->table('admin');
        $table->addColumn('nick_name', 'char', array('limit' => 15, 'default' => '', 'comment' => '用户昵称'))
            ->addColumn('phone', 'biginteger', array('limit' => 16, 'default' => 0, 'comment' => '手机号'))
            //->addColumn('phone_bind', 'boolean', array('limit' => 1, 'default' => 0,'signed'=>'unsigned', 'comment' => '是否绑定手机号'))
            ->addColumn('role', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '角色ID'))
            ->addColumn('group_id', 'integer', array('limit' => 11, 'default' => 0, 'comment' => '部门id'))
            ->addColumn('signup_ip', 'char', array('limit' => 20, 'default' => 0, 'comment' => '注册ip'))
            ->addColumn('sort', 'integer', array('limit' => 1, 'default' => 0, 'comment' => '排序'))
            ->addcolumn('photo', 'string', array('limit' => 255, 'default' => '', 'comment' => '头像'))
            ->addcolumn('frozen', 'boolean', array('limit' => 2, 'default' => 0, 'comment' => '1冻结'))
            ->addColumn('create_at', 'datetime', array('comment' => '创建时间', 'null' => true))
            ->addColumn('update_at', 'datetime', array('comment' => '修改时间', 'null' => true))
            ->addIndex(array('frozen'))
            ->update();
    }
}
