<?php
/**
 * Contains \visitors\migrations\m240708_212200_create_visitation
 */

namespace visitors\migrations;

use deele\devkit\db\SchemaHelper;
use yii\db\Migration;

/**
 * Migration class that creates "visitors__visitation" table
 *
 * @package visitors\migrations
 */
class m240708_212200_create_visitation extends Migration
{

    public string $tableName = 'visitors__visitation';

    /**
     * @inheritDoc
     */
    public function up(): bool
    {
        if (!SchemaHelper::tablesExist($this->tableName)) {
            return $this->createMainTable();
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function down(): bool
    {
        if (SchemaHelper::tablesExist($this->tableName)) {
            return $this->deleteMainTable();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function createMainTable(): bool
    {
        $tableName = SchemaHelper::prefixedTable($this->tableName);
        $this->createTable(
            $tableName,
            [
                'tag' => $this->string(23)
                    ->comment('Visitation tag'),
                'visited_url_id' => $this->integer()
                    ->comment('Visited URL ID')
                    ->notNull(),
                'ip_address' => $this->string(39)
                    ->comment('IP Address'),
                'geo_ip_json' => $this->text()
                    ->comment('Geo IP (JSON)'),
                'user_agent_id' => $this->integer()
                    ->comment('User Agent'),
                'is_ajax' => $this->boolean()
                    ->comment('Is AJAX request')
                    ->defaultValue(0)
                    ->notNull(),
                'method' => $this->string(5)
                    ->comment('Method')
                    ->defaultValue('GET')
                    ->notNull(),
                'headers_json' => $this->text()
                    ->comment('Headers (JSON)'),
                'cookies_json' => $this->text()
                    ->comment('Cookies (JSON)'),
                'created_at' => $this->dateTime()
                    ->notNull()
                    ->comment('Created at (UTC)'),
                'created_by' => $this->integer()
                    ->comment('Created by'),
            ],
            'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB'
        );

        // visited_url_id
        $this->createIndex(
            SchemaHelper::createIndexName('visited_url_id'),
            $tableName,
            'visited_url_id'
        );
        $this->addForeignKey(
            SchemaHelper::createForeignKeyName($this->tableName, 'visited_url_id'),
            $tableName,
            'visited_url_id',
            SchemaHelper::prefixedTable('visitors__visited_url'),
            'id',
            SchemaHelper::createForeignKeyType(SchemaHelper::FK_CASCADE),
            SchemaHelper::createForeignKeyType(SchemaHelper::FK_CASCADE)
        );

        // user_agent_id
        $this->createIndex(
            SchemaHelper::createIndexName('user_agent_id'),
            $tableName,
            'user_agent_id'
        );
        $this->addForeignKey(
            SchemaHelper::createForeignKeyName($this->tableName, 'user_agent_id'),
            $tableName,
            'user_agent_id',
            SchemaHelper::prefixedTable('visitors__user_agent'),
            'id',
            SchemaHelper::createForeignKeyType(SchemaHelper::FK_CASCADE),
            SchemaHelper::createForeignKeyType(SchemaHelper::FK_CASCADE)
        );

        // created_by
        $this->createIndex(
            SchemaHelper::createIndexName('created_by'),
            $tableName,
            'created_by'
        );
        $this->addForeignKey(
            SchemaHelper::createForeignKeyName($this->tableName, 'created_by'),
            $tableName,
            'created_by',
            SchemaHelper::prefixedTable('user'),
            'id',
            SchemaHelper::createForeignKeyType(SchemaHelper::FK_SET_NULL),
            SchemaHelper::createForeignKeyType(SchemaHelper::FK_CASCADE)
        );

        $this->addPrimaryKey(
            'tag',
            $tableName,
            [
                'tag',
            ]
        );

        return true;
    }

    /**
     * @return bool
     */
    public function deleteMainTable(): bool
    {
        $tableName = SchemaHelper::prefixedTable($this->tableName);

        // visited_url_id
        $this->dropForeignKey(
            SchemaHelper::createForeignKeyName($this->tableName, 'visited_url_id'),
            $tableName
        );
        $this->dropIndex(
            SchemaHelper::createIndexName('visited_url_id'),
            $tableName
        );

        // user_agent_id
        $this->dropForeignKey(
            SchemaHelper::createForeignKeyName($this->tableName, 'user_agent_id'),
            $tableName
        );
        $this->dropIndex(
            SchemaHelper::createIndexName('user_agent_id'),
            $tableName
        );

        // created_by
        $this->dropForeignKey(
            SchemaHelper::createForeignKeyName($this->tableName, 'created_by'),
            $tableName
        );
        $this->dropIndex(
            SchemaHelper::createIndexName('created_by'),
            $tableName
        );

        $this->dropTable($tableName);

        return true;
    }
}
