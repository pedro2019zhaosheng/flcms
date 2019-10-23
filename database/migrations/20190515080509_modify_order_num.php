<?php

use think\migration\Migrator;
use think\migration\db\Column;

class ModifyOrderNum extends Migrator
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
        $table = $this->table('order_num');
        $table->addColumn('is_push', 'boolean', ['default' => 0, 'comment' => '该期是否是推单, 0: 不是  1: 是'])
            ->update();
    }
}
