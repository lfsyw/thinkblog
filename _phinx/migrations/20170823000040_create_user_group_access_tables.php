<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateUserGroupAccessTables extends AbstractMigration
{
    public function change()
    {
        $userTableName = getenv('AUTH_USER') ?: 'user';
        $accessTableName = getenv('AUTH_GROUP_ACCESS') ?: 'user_group';
        $groupTableName = getenv('AUTH_GROUP') ?: 'role';

        $this->table($accessTableName, [
            'id' => false,
            'primary_key' => ['uid', 'group_id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'comment' => '用户-用户组关系表',
        ])->addColumn('uid', 'integer', [
            'null' => false,
            'limit' => MysqlAdapter::INT_REGULAR,
            'precision' => 10,
            'signed' => false,
            'comment' => '用户id',
        ])->addColumn('group_id', 'integer', [
            'null' => false,
            'limit' => MysqlAdapter::INT_REGULAR,
            'precision' => 10,
            'signed' => false,
            'comment' => '用户组id',
        ])->addColumn('created_by', 'integer', [
            'null' => false,
            'limit' => MysqlAdapter::INT_REGULAR,
            'precision' => 10,
            'signed' => false,
            'comment' => '创建用户id'
        ])->addColumn('created_at', 'datetime', [
            'null' => false,
            'default' => 'CURRENT_TIMESTAMP',
            'comment' => '创建时间',
        ])->addColumn('updated_by', 'integer', [
            'null' => true,
            'limit' => MysqlAdapter::INT_REGULAR,
            'precision' => 10,
            'signed' => false,
            'comment' => '更新用户id'
        ])->addColumn('updated_at', 'datetime', [
            'null' => true,
            'comment' => '更新时间',
        ])->addIndex(['uid', 'group_id'], [
            'name' => 'idx_uid_group_id',
            'unique' => true
        ])->addIndex(['uid'], [
            'name' => 'idx_uid',
            'unique' => false
        ])->addForeignKey('uid', $userTableName, 'id', [
            'delete'=> 'RESTRICT',
            'update'=> 'CASCADE',
        ])->addIndex(['group_id'], [
            'name' => 'idx_group_id',
            'unique' => false,
        ])->addForeignKey('group_id', $groupTableName, 'id', [
            'delete'=> 'RESTRICT',
            'update'=> 'CASCADE',
        ])->create();
    }
}
