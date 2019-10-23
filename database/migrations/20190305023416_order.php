<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Order extends Migrator
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
        $table = $this->table('order', array('engine' => 'InnoDB', 'comment' => '注单表'));
        $table->addcolumn('order_no', 'string', array('default' => '', 'comment' => '订单号'))
            ->addColumn('member_id', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '会员ID'))
            ->addColumn('lottery_id', 'integer', array('default' => 0, 'comment' => '彩种id'))
            ->addcolumn('beishu', 'integer', array('default' => 0, 'signed' => 'unsigned', 'comment' => '倍数'))
            ->addColumn('zhu', 'integer', array('default' => 0, 'signed' => 'unsigned', 'comment' => '注数'))
            ->addColumn('amount', 'integer', array('signed' => 'unsigned', 'default' => 0, 'comment' => '金额'))
            ->addColumn('start_amount', 'integer', array('signed' => 'unsigned', 'default' => 0, 'comment' => '起跟金额'))
            ->addColumn('bet_type', 'string', array('default' => '', 'comment' => '玩法'))
            ->addColumn('chuan', 'string', array('default' => '', 'comment' => '串关信息'))
            ->addColumn('play_type', 'string', array('default' => '', 'comment' => '过关方式'))
            ->addColumn('status', 'boolean', array('signed' => 'unsigned', 'default' => 0, 'comment' => '状态'))
            ->addColumn('beizhu', 'text', array('comment' => '备注'))
            ->addColumn('bet_content', 'text', array('comment' => '投注内容'))
            ->addColumn('order_type', 'boolean', array('default' => 1, 'signed' => 'unsigned', 'comment' => '订单类型'))
            ->addColumn('commission_rate', 'integer', array('default' => 0, 'signed' => 'unsigned', 'comment' => '佣金比例'))
            ->addColumn('order_title', 'string', array('default' => '', 'comment' => '订单标题'))
            ->addColumn('create_time', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('start_time', 'datetime', array('null' => true, 'comment' => '跟单截止时间'))
            ->addColumn('follows', 'integer', array('signed' => 'unsigned', 'default' => 0, 'comment' => '跟单人数'))
            ->addColumn('is_follow_order', 'boolean', array('signed' => 'unsigned', 'default' => 0, 'comment' => '是否为跟单'))
            ->addColumn('follow_order_id', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '跟单的订单id'))
            ->addColumn('follow_order_commission', 'integer', array('signed' => 'unsigned', 'default' => 0, 'comment' => '跟单的佣金'))
            ->addColumn('bonus', 'integer', array('signed' => 'unsigned', 'default' => 0, 'comment' => '奖金'))
            ->addColumn('is_yh', 'boolean', array('signed' => 'unsigned', 'default' => 0, 'comment' => '是否优化'))
            ->addColumn('is_moni', 'boolean', array('signed' => 'unsigned', 'default' => 0, 'comment' => '是否模拟  0：否   1：是'))
            ->addIndex(array('order_no'), ['unique' => true])
            ->addIndex(array('member_id', 'lottery_id', 'status', 'order_type', 'is_moni'))
            ->create();
    }
}
