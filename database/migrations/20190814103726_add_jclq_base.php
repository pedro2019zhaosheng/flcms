<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddJclqBase extends Migrator
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
        $table = $this->table('jclq_base');
        $table->addColumn('isAh', 'boolean', ['default' => 0, 'comment' => '竞彩赛程与 SportsDT 对阵是否相反，0 相同 1 相反'])
            ->update();
    }
}
