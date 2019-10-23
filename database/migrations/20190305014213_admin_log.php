<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminLog extends Migrator
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
        /*$table = $this->table('admin_log', array('engine' => 'MyISAM', 'comment' => '行为日志表'));
        $table->addcolumn('action_id', 'integer', array('limit' => 11,'signed'=>'unsigned','null' => false, 'default' => 0,'comment' => '行为id'))
            ->addColumn('user_id', 'integer', array('limit' => 11,'signed'=>'unsigned', 'null' => false, 'comment' => '执行用户id'))
            ->addColumn('action_ip', 'biginteger', array('limit' => 20, 'null' => false, 'comment' => '执行行为者ip'))
            ->addcolumn('model', 'string', array('limit' => 50,'default'=>'', 'comment' => '触发行为的表'))
            ->addColumn('record_id', 'integer', array('limit'=>11,'signed'=>'unsigned','null' => false,'default'=>0, 'comment' => '触发行为的数据id'))
            ->addColumn('remark', 'text', array('comment' => '日志备注'))
            ->addColumn('status', 'boolean', array('limit' => 4, 'null' => false, 'default' => 1,'comment' => '状态'))
            ->addColumn('create_time', 'datetime', array('null' =>true,'comment' => '执行行为的时间'))
            ->addIndex(array('action_ip', 'action_id'))
            ->create();*/
    }
}
