<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddFundChargeUniqueIndex extends Migrator
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
        // 优化充值表索引
        $table = $this->table('fund_charge');
        $table->addIndex('order_no', ['unique' => true])
            ->addIndex('member_id')
            ->update();
    }
}
