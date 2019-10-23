<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminConfig extends Migrator
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
        $table = $this->table('admin_config', array('engine' => 'MyISAM', 'comment' => '网站配置表'));
        $table->addColumn('varname', 'string', array('default' => '', 'comment' => '名称'))
            ->addColumn('info', 'string', array('default' => ''))
            ->addcolumn('groupid', 'integer', array('default' => 1, 'comment' => '类别 1网站配置 2邮箱配置'))
            ->addColumn('value', 'text', array('comment' => '值'))
            ->create();
    }
}
