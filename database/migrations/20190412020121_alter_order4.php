<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterOrder4 extends Migrator
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
        $table->changeColumn('follow_order_commission', 'decimal', ['precision' => 18, 'scale' => 2, 'default' => "0.00", 'comment' => '推单获取的总佣金'])
            ->changeColumn('follow_order_id', 'biginteger', ['default' => 0, 'comment' => '推单id'])
            ->addColumn('pay_out_commission', 'decimal', ['precision' => 18, 'scale' => 2, 'default' => "0.00", 'comment' => '跟单付出的佣金'])
            ->update();
    }
}
