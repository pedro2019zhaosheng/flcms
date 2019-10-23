<?php

use think\migration\Migrator;
use think\migration\db\Column;

class SetTable extends Migrator
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
        //会员表添加 总充值 总输赢字段.
        $table = $this->table('member');
        $table->addColumn('recharge', 'decimal',array('precision' => 18, 'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '总充值'))
              ->addColumn('profit', 'decimal',array('precision' => 18, 'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '总输赢'))
              ->update();
        //注单表添加 嘉奖奖金字段
        $table = $this->table('order');
        $table ->addColumn('bounty', 'decimal',array('precision' => 18, 'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '嘉奖奖金'))
        ->update();
        //修改代理对应彩种返佣比例表的引擎
        $table = $this->table('member_ratio',array('engine' => 'InnoDB'))->save();
    }
}
