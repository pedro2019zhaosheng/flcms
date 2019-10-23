<?php

use think\migration\Migrator;
use think\migration\db\Column;

class OrderContent extends Migrator
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
        $table = $this->table('order_content', array('engine' => 'InnoDB', 'comment' => '订单内容表'));
        $table->addcolumn('order_id', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '订单id'))
            ->addColumn('lottery_id', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '彩种id'))
            ->addColumn('chuan', 'string', array('default' => '', 'comment' => '串关'))
            ->addcolumn('content', 'text', array('comment' => '投注内容'))
            ->addColumn('status', 'boolean', array('default' => 0, 'signed' => 'unsigned', 'comment' => '状态'))
            ->addColumn('bet', 'string', array('default' => '', 'comment' => '投注内容'))
            ->addColumn('bonus', 'integer', array('signed' => 'unsigned', 'default' => 0, 'comment' => '奖金'))
            ->addColumn('beishu', 'integer', array('signed' => 'unsigned', 'default' => 1, 'comment' => '优化倍数'))
            ->addColumn('create_at', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addIndex(array('lottery_id', 'status', 'order_id'))
            ->create();
    }
}
