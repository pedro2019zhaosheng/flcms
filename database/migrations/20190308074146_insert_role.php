<?php

use think\migration\Migrator;
use think\migration\db\Column;

class InsertRole extends Migrator
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
        $this->execute("INSERT INTO " . config('database.prefix') . "admin_role (pid, `name`, sort, create_time, update_time, roletype) VALUES (0, '超级管理员', 1, '". date('Y-m-d H:i:s') ."', '" . date('Y-m-d H:i:s') . "', 0)");
    }
}