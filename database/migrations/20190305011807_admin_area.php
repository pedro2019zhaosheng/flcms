<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminArea extends Migrator
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
        $table = $this->table('admin_area', array('engine' => 'MyISAM', 'comment' => '地区表'));
        $table->addcolumn('pid', 'integer', array('limit' => 5,'null' => false, 'default' => 0,'comment' => '父ID'))
            ->addColumn('citycode', 'string', array('limit' => 255, 'null' => false, 'comment' => '城市编码'))
            ->addColumn('adcode', 'string', array('limit' => 255, 'null' => false, 'comment' => '地区编码'))
            ->addcolumn('name', 'string', array('limit'=>30,'null' => false, 'comment' => '地区名称'))
            ->addColumn('center', 'string', array('limit'=>255,'null' => false, 'comment' => '城市中心点'))
            ->addColumn('level', 'string', array('limit'=>255,'null' => false, 'comment' => '行政区划级别'))
            ->addColumn('ratio', 'decimal', array('precision' => 12, 'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '报价比例'))
            ->addColumn('sort', 'integer', array('limit' => 5,'null' => false, 'default' => "0",'comment' => '排序'))
            ->addColumn('status', 'boolean', array('limit' => 4,'null' => false, 'default' => "1",'comment' => '状态 1正常 0禁用'))
            ->addcolumn('is_delete', 'boolean', array('limit'=>1,'default'=>'0','null' => false, 'comment' => '0:未删除   1：删除'))
            ->addIndex(array('pid', 'sort'))
            ->create();
    }
}
