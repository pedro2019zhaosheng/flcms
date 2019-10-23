<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddLotteryColumn extends Migrator
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
        $table = $this->table('lottery');
        $table->addColumn('is_run', 'boolean', ['limit' => 2, 'default' => 0, 'comment' => '是否已启用，默认是未启用，1：已启用'])
            ->update();
    }
}
