<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterOrderDetail extends Migrator
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
        $table = $this->table('order_detail');
        $table->removeColumn('match_time')
            ->changeColumn('status', 'boolean', ['limit' => 1, 'comment' => '中奖状态, 0 待开奖 1 已中奖 2未中奖', 'default' => 0])
            ->addColumn('order_id', 'biginteger', ['comment' => '订单ID', 'default' => 0])
            ->update();
    }
}
