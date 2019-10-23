<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddAdminLogComment extends Migrator
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
        $table = $this->table('admin_log');
        $table->removeIndex(['belong'])
            ->removeIndex(['executor'])
            ->removeIndex(['status'])
            ->removeIndex(['exec_time'])
            ->changeColumn('status', 'boolean', ['default' => 0, 'comment' => '执行状态, 0:失败 1:成功 2:未知'])
            ->update();
    }
}
