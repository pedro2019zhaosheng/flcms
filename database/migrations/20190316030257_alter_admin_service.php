<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterAdminService extends Migrator
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
        $this->table('admin_service')
            ->changeColumn('img', 'integer', ['comment' => '二维码', 'default' => 0])
            ->changeColumn('icon', 'integer', ['comment' => '客服图标', 'default' => 0])
            ->update();
    }
}
