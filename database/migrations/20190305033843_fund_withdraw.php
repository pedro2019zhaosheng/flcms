<?php

use think\migration\Migrator;
use think\migration\db\Column;

class FundWithdraw extends Migrator
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
        $table = $this->table('fund_withdraw', array('engine' => 'InnoDB', 'comment' => '提现记录表'));
        $table->addcolumn('member_id', 'biginteger', array('default' => 0, 'comment' => '用户id'))
            ->addColumn('bank_id', 'integer', array('default' => 0, 'comment' => '提现银行卡id'))
            ->addColumn('account', 'decimal', array('precision' => 18, 'scale' => 2, 'default' => "0.00", 'comment' => '提现金额'))
            ->addColumn('to_account', 'decimal', array('precision' => 18, 'scale' => 2, 'default' => "0.00", 'comment' => '到账金额'))
            ->addColumn('status', 'boolean', array('default' => 0, 'comment' => '状态 ：  1：审核中   2：审核通过   3：审核不通过'))
            ->addColumn('remark', 'string', array('default' => '', 'comment' => '备注  (审核意见）'))
            ->addColumn('create_at', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_at', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addIndex(array('member_id', 'bank_id', 'status', 'create_at'))
            ->create();
    }
}
