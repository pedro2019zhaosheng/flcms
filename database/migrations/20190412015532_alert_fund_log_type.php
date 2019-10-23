<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlertFundLogType extends Migrator
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
        $table->changeColumn('type', 'boolean', array('limit'=>2,'default'=>0,'comment' => '变动类型 1：充值  2：提现  3：购彩  4：资金冻结 5：奖金  6：系统嘉奖  7：投注返佣  8：充值赠送 9:资金校正 10:跟单返佣'))
            ->update();
    }
}
