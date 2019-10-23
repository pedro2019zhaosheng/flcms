<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MenuNode extends Migrator
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
        $table = $this->table('admin_menu', array('engine' => 'MyISAM', 'comment' => '后台菜单表'));
        $table->addColumn('pid', 'integer', array('signed' => 'unsigned', 'default' => 0, 'comment' => '上级菜单id'))
            ->addColumn('title', 'string', array('default' => '', 'comment' => '菜单标题'))
            ->addcolumn('icon', 'string', array('default' => '', 'comment' => '菜单图标'))
            ->addColumn('module', 'string', array('default' => '', 'comment' => '模块'))
            ->addColumn('controller', 'string', array('default' => '', 'comment' => '控制器'))
            ->addColumn('action', 'string', array('default' => '', 'comment' => '方法'))
            ->addColumn('menu_type', 'string', array('default' => '', 'comment' => '菜单类型（link：连接，module：模块，single：单页，function：功能）'))
            ->addColumn('url_value', 'string', array('default' => '', 'comment' => '链接地址'))
            ->addColumn('sort', 'integer', array('default' => 100, 'comment' => '排序'))
            ->addColumn('system_menu', 'boolean', array('limit' => 4, 'signed' => 'unsigned', 'default' => 0, 'comment' => '是否为系统菜单，系统菜单不可删除, 1是 0否'))
            ->addColumn('status', 'boolean', array('limit' => 2, 'default' => 1, 'comment' => '状态,0禁用'))
            ->addColumn('create_time', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_time', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->create();
    }
}
