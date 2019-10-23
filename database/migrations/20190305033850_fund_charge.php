<?php

use think\migration\Migrator;
use think\migration\db\Column;

class FundCharge extends Migrator
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
        $table = $this->table('fund_charge', array('engine' => 'InnoDB', 'comment' => '充值记录表'));
        $table->addColumn('order_no', 'string', array('default' => '', 'comment' => '充值订单号'))
            ->addcolumn('member_id', 'integer', array('comment' => '用户id'))
            ->addColumn('account', 'decimal', array('precision' => 11, 'scale' => 2, 'default' => "0.00",'comment' => '充值金额'))
            ->addColumn('to_account', 'decimal', array('precision' => 11, 'scale' => 2, 'default' => "0.00",'comment' => '到账金额'))
            ->addColumn('type', 'boolean', array('limit'=>2,'default'=>0,'comment' => '支付方式'))
            ->addColumn('status', 'boolean', array('limit'=>2,'default'=>0,'comment' => '充值状态 1：待支付  2：充值成功  3：充值失败'))
            ->addColumn('create_time', 'datetime', array('null' =>true,'comment' => '创建时间'))
            ->addColumn('update_time', 'datetime', array('null'=>true,'comment' => '更新时间'))
            ->create();
    }
}
