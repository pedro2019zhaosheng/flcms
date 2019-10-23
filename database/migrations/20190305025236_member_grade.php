<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MemberGrade extends Migrator
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
        $table = $this->table('member_grade', array('engine' => 'InnoDB', 'comment' => '分销关系表'));
        $table->addcolumn('member_id', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '商户号'))
            ->addColumn('pid', 'biginteger', array('signed' => 'unsigned', 'default' => 0, 'comment' => '上级ID'))
            ->addColumn('tier', 'integer', array('default' => 0, 'comment' => '层级'))
            ->addColumn('path', 'string', array('default' => '', 'comment' => '路径'))
            ->addColumn('create_at', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_at', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addIndex(array('member_id', 'pid', 'tier'))
            ->create();
    }
}
