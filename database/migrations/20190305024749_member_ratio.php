<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MemberRatio extends Migrator
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
        $table = $this->table('member_ratio', array('engine' => 'MyISAM', 'comment' => '代理对应彩种返佣比例'));
        $table->addcolumn('member_id', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '代理id'))
            ->addColumn('lottery_id', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '彩种id'))
            ->addColumn('ratio', 'decimal', array('precision' => 3, 'scale' => 2, 'default' => "0.00", 'comment' => '返佣比例'))
            ->addColumn('status', 'boolean', array('default' => 0, 'signed' => 'unsigned', 'comment' => '1：禁用'))
            ->addColumn('create_at', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_at', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addIndex(array('member_id', 'lottery_id', 'status'))
            ->create();
    }
}
