<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterFundLog extends Migrator
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
        $table->changeColumn('order_id', 'string', ['default' => '', 'comment' => '注单ID(一般情况下在订单返佣存入)'])
            ->update();
    }
}
