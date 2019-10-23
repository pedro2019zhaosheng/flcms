<?php

use think\migration\Migrator;
use think\migration\db\Column;

class OrderDetail extends Migrator
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
        $table = $this->table('order_detail', array('engine' => 'InnoDB', 'comment' => '订单详情表'));
        $table->addcolumn('order_content_id', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '订单内容id'))
            ->addColumn('lottery_id', 'integer', array('signed' => 'unsigned', 'default' => 0, 'comment' => '彩种id'))
            ->addColumn('match_time', 'biginteger', array('default' => 0, 'comment' => '赛事时间'))
            ->addcolumn('match_num', 'string', array('default' => '0', 'comment' => '场次'))
            ->addColumn('play_type', 'string', array('default' => '', 'comment' => '玩法'))
            ->addColumn('bet', 'string', array('default' => '', 'comment' => '投注内容'))
            ->addColumn('status', 'boolean', array('signed' => 'unsigned', 'limit' => 4, 'default' => 0, 'comment' => '状态'))
            ->addColumn('start_time', 'datetime', array('null' => true, 'comment' => '跟单截止时间'))
            ->addColumn('create_time', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addIndex(array('lottery_id', 'match_num', 'order_content_id', 'status', 'match_time'))
            ->create();
    }
}
