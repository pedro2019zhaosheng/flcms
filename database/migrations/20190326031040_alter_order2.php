<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterOrder2 extends Migrator
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
        $table->addColumn('sup_order_state', 'boolean', ['limit' => 1, 'comment' => '推单审核状态, 0 待审核 1 已通过 2 已驳回', 'default' => 0])
            ->changeColumn('pay_status', 'boolean', ['limit' => 1, 'comment' => '支付状态0：未支付  1 : 已支付', 'default' => 0])
            ->update();
    }
}
