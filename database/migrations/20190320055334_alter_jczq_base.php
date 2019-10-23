<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterJczqBase extends Migrator
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
        $this->table('jczq_base')
            ->changeColumn('sale_status', 'boolean', ['limit' => 2, 'comment' => '出售状态, 0 未出售  1已出售  2已停售', 'default' => 0])
            ->update();
    }
}
