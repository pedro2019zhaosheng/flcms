<?php

use think\migration\Migrator;
use think\migration\db\Column;

class P3p5Open extends Migrator
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
        $table = $this->table('pl_open', ['engine' => 'InnoDB', 'comment' => '排列三，排列五开奖表']);
        $table->addColumn('expect', 'string', ['default' => '', 'comment' => '期号'])
            ->addColumn('open_code', 'string', ['default' => '', 'comment' => '开奖结果'])
            ->addColumn('open_time', 'datetime', ['null' => true, 'comment' => '开奖时间'])
            ->addColumn('ctype', 'boolean', ['default' => 1, 'comment' => '彩种类型, 1: 排三  2: 排五'])
            ->addColumn('create_time', 'datetime', ['null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '修改时间'])
            ->addIndex('ctype')
            ->addIndex('expect')
            ->create();
    }
}
