<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterAdminLogEngine extends Migrator
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
        // 修改代理对应彩种返佣比例表的引擎
        $sql = 'ALTER TABLE ' . config('database.prefix') .'admin_log ENGINE=MyISAM';
        $this->execute($sql);
    }
}
