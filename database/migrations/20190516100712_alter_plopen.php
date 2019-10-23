<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterPlopen extends Migrator
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
        $table = $this->table('pl_open');
        $table->changeColumn('ctype', 'boolean', ['default' => 1, 'comment' => '彩种类型, 1: 排三  2: 排五  3: 澳彩  4: 葡彩'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '开奖状态, 0: 待开奖  1: 已开奖'])
            ->addIndex('status')
            ->update();
    }
}
