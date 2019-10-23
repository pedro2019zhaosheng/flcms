<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddLotteryStatus extends Migrator
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
        $table = $this->table('jczq');
        $table->addColumn('status', 'boolean', ['comment' => '状态, 0停售 1出售中', 'default' => 1])
            ->addColumn('cutoff_time', 'datetime', ['null' => true, 'comment' => '手动截止时间'])
            ->update();
        $table = $this->table('jclq');
        $table->addColumn('status', 'boolean', ['comment' => '状态, 0停售 1出售中', 'default' => 1])
            ->addColumn('cutoff_time', 'datetime', ['null' => true, 'comment' => '手动截止时间'])
            ->update();
    }
}
