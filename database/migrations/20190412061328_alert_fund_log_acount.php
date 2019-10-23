<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlertFundLogAcount extends Migrator
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
        $table->addColumn('bet_username','biginteger', array('limit'=>'20','default' => 0,'null' => false, 'comment' => '投注账号(订单返佣必存)'))
              ->changeColumn('order_id', 'string', array('default'=>'','null'=>false,'comment' => '注单ID(订单返佣必存)'))
            ->update();
    }
}
