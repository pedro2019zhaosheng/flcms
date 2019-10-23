<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterJczqOpen2 extends Migrator
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
        // base表新增让球数
        $this->table('jczq_base')
            ->addColumn('rqs', 'char', ['limit' => 10, 'default' => '', 'comment' => '让球数'])
            ->update();

        // 删除让球数
        $table = $this->table('jczq_open');
        $table->removeColumn('rqs');
        $table->update();
    }
}
