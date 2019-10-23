<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterOrder7 extends Migrator
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
        $table = $this->table('order');
        $table->changeColumn('status', 'boolean', ['default' => 0, 'comment' => '状态, 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖'])
            ->addColumn('is_clear', 'boolean', ['default' => 0, 'comment' => '是否结算, 0: 未结算 1: 已结算'])
            ->addColumn('exit_account', 'decimal', ['precision' => 8, 'scale' => 2, 'default' => "0.00", 'comment' => '退还资金(数字彩)'])
            ->addColumn('open_time', 'datetime', ['null' => true, 'comment' => '订单开奖时间'])
            ->addIndex('is_clear')
            ->update();

        $table = $this->table('order_num');
        $table->changeColumn('status', 'boolean', ['default' => 1, 'comment' => '1：待开奖 2：未中奖 3：已中奖'])
            ->update();
    }
}
