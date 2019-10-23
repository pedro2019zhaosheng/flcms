<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddBaseColumn extends Migrator
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
        // 新增足球基础信息表排序字段
        $table = $this->table('jczq_base');
        $table->addColumn('sort', 'biginteger', ['comment' => '排序', 'default' => 0]);
        $table->update();

        // 新增篮球基础信息表排序字段
        $table = $this->table('jclq_base');
        $table->addColumn('sort', 'biginteger', ['comment' => '排序', 'default' => 0]);
        $table->update();
    }
}
