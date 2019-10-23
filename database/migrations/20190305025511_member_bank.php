<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MemberBank extends Migrator
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
        $table = $this->table('member_bank', array('engine' => 'MyISAM', 'comment' => '用户银行卡表'));
        $table->addcolumn('member_id', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '用户id'))
            ->addColumn('bank', 'string', array('default' => '', 'comment' => '银行名称'))
            ->addColumn('bank_code', 'char', array('limit' => 20, 'default' => '', 'comment' => '银行code'))
            ->addColumn('bank_num', 'string', array('default' => '', 'comment' => '银行卡号'))
            ->addColumn('cardholder', 'char', array('limit' => 20, 'default' => '', 'comment' => '持卡人姓名'))
            ->addColumn('status', 'boolean', array('default' => 0, 'comment' => '1禁用'))
            ->addColumn('create_at', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_at', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addIndex(array('member_id', 'bank_code', 'bank_num', 'status'))
            ->create();
    }
}
