<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Attach extends Migrator
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
        $table = $this->table('attach', array('engine' => 'MyISAM', 'comment' => '附件表'));
        $table->addColumn('uid', 'biginteger', array('default' => 0, 'comment' => '用户ID'))
            ->addcolumn('path', 'string', array('default' => "", 'comment' => '路径'))
            ->addColumn('md5', 'string', array('default' => "", 'comment' => 'MD5散列值'))
            ->addColumn('type', 'boolean', array('limit' => 2, 'default' => 1, 'comment' => '类型 1 会员  2 代理商  3 后台管理员'))
            ->addColumn('status', 'boolean', array('limit' => 2, 'default' => 0, 'comment' => '1:禁用'))
            ->addColumn('create_time', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_time', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addIndex(['uid', 'type', 'status'])
            ->create();
    }
}
