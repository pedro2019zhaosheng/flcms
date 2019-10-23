<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ModifyFundLog extends Migrator
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
        $table = $this->table('fund_log');
        $table->addColumn('withdraw_id', 'biginteger', ['comment' => 'fund_withdraw提现订单ID(提现时存)', 'default' => 0])
            ->addColumn('charge_id', 'biginteger', ['comment' => 'fund_charge充值订单ID(充值时存)', 'default' => 0])
            ->update();
    }
}
