<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ModifyPloPen1 extends Migrator
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
        $table->changeColumn('ctype', 'boolean', ['comment' => '彩种类型, 1: 排三  2: 排五  3: 澳彩  4: 葡彩 5: 幸运飞艇', 'default' => 1])
            ->update();
    }
}
