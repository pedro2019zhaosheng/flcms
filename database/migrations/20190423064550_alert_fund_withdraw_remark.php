<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlertFundWithdrawRemark extends Migrator
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
        $table = $this->table('fund_withdraw');
        $table->changeColumn('status', 'boolean', ['default' => 1, 'comment' => '状态 ：  1：审核中   2：提现中   3：已驳回 4.提现成功 5.提现失败']);
        $table->update();
    }
}
