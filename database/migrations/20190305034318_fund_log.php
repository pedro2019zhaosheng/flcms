<?php

use think\migration\Migrator;
use think\migration\db\Column;

class FundLog extends Migrator
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
        $table = $this->table('fund_log', array('engine' => 'InnoDB', 'comment' => '资金记录表'));
        $table->addcolumn('member_id', 'integer', array('comment' => '会员id'))
            ->addColumn('money', 'decimal', array('precision' => 11, 'scale' => 2, 'default' => "0.00",'comment' => '变动金额'))
            ->addColumn('front_money', 'decimal', array('precision' => 11, 'scale' => 2, 'default' => "0.00",'comment' => '变动前总金额'))
            ->addColumn('later_money', 'decimal', array('precision' => 11, 'scale' => 2, 'default' => "0.00",'comment' => '变动后总金额'))
            ->addColumn('type', 'boolean', array('limit'=>2,'default'=>0,'comment' => '变动类型 1：充值  2：提现  3：购彩  4：冻结 5：奖金  6：加奖  7：佣金奖励  8：充值赠送'))
            ->addColumn('remark', 'string', array('default'=>'','comment' => '备注（变动原因）'))
            ->addColumn('create_time', 'datetime', array('null' =>true,'comment' => '创建时间'))
            ->addColumn('update_time', 'datetime', array('null'=>true,'comment' => '更新时间'))
            ->create();
    }
}
