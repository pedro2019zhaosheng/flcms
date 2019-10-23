<?php

use think\migration\Migrator;
use think\migration\db\Column;

class HistoryRecord extends Migrator
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
        $table = $this->table('history_record', array('engine' => 'InnoDB', 'comment' => '搜索历史记录表'));
        $table->addColumn('uid', 'integer', array('limit' => 11, 'default' => 0,'null' => false, 'comment' => '会员ID'))
            ->addcolumn('content', 'string', array('limit' => 50, 'default' => '','null' => false, 'comment' => '搜索内容'))
            ->addColumn('create_at', 'datetime', array('comment' => '创建时间', 'null' => true))
            ->create();
    }
}
