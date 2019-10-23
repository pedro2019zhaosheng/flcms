<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlertOrder extends Migrator
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
        $table->addColumn('pay_hadsel', 'decimal', array('precision'=>18,'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '彩金支付的金额'))
              ->addColumn('pay_balance', 'decimal', array('precision'=>18,'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '余额支付的金额'))
              ->update();
    }
}
