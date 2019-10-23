<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateHandDraw extends Migrator
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
        $table = $this->table('pre_draw', ['engine' => 'MyISAM', 'comment' => '数字彩手动风控开奖号码预存表']);
        $table->addColumn('number', 'string', ['comment' => '期号', 'default' => ''])
            ->addColumn('open_code', 'string', ['comment' => '预开奖号码', 'default' => ''])
            ->addColumn('ctype', 'boolean', ['comment' => '数字彩种类型, 3: 澳彩  4: 葡彩', 'default' => 3])
            ->addColumn('create_time', 'datetime', ['comment' => '创建时间', 'null' => true])
            ->addColumn('update_time', 'datetime', ['comment' => '修改时间', 'null' => true])
            ->addColumn('status', 'boolean', ['comment' => '状态, 0: 未使用  1: 已使用', 'default' => 0])
            ->addIndex('number')
            ->addIndex('ctype')
            ->create();
    }
}
