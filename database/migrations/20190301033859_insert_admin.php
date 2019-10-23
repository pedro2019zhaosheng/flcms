<?php

use think\migration\Migrator;
use think\migration\db\Column;

class InsertAdmin extends Migrator
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
    public function up()
    {
        $this->execute("INSERT INTO " . config('database.prefix') . "admin (username, password, last_login_ip, last_login_time, nick_name, create_at, update_at, photo, role) VALUES ('admin', '" . md5('admin888') . "', '127.0.0.1', '" . date('Y-m-d H:i:s') . "', 'SuperMan', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', 0, 1)");
    }
}
