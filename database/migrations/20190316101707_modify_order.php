<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ModifyOrder extends Migrator
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
        $table->changeColumn('status', 'boolean', array('signed' => 'unsigned', 'default' => 0, 'comment' => '状态0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖'))
            ->addColumn('pay_status', 'boolean', array('default' => 0, 'comment' => '支付状态0：未结算 1已结算'))
            ->addColumn('pay_time', 'datetime', array('null' => true, 'comment' => '支付时间'))
            ->addColumn('pay_type', 'boolean', array('default' => 1, 'comment' => '购买方式1：自购 2：跟单'))
            ->update();
    }
}
