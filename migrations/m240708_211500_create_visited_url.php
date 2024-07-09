<?php
/**
 * Contains \visitors\migrations\m240708_211500_create_visited_url
 */

namespace visitors\migrations;

use deele\devkit\db\SchemaHelper;
use yii\db\Migration;

/**
 * Migration class that creates "visitors__visited_url" table
 *
 * @package visitors\migrations
 */
class m240708_211500_create_visited_url extends Migration
{

    public string $tableName = 'visitors__visited_url';

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
                'id' => $this->primaryKey()
                    ->comment('Visited URL ID'),
                'url' => $this->string(2000)
                    ->comment('URL')
                    ->notNull(),
                'query_params_json' => $this->text()
                    ->comment('Query Params (JSON)'),
                'hits' => $this->integer()
                    ->comment('Hits')
                    ->defaultValue(1)
                    ->notNull(),
                'status' => $this->smallInteger()
                    ->comment('Status')
                    ->defaultValue(1)
                    ->notNull(),
            ],
            'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB'
        );

        $this->createIndex(
            SchemaHelper::createIndexName('url'),
            $tableName,
            'url'
        );

        return true;
    }

    /**
     * @return bool
     */
    public function deleteMainTable(): bool
    {
        $tableName = SchemaHelper::prefixedTable($this->tableName);

        $this->dropIndex(
            SchemaHelper::createIndexName('url'),
            $tableName
        );
        $this->dropTable($tableName);

        return true;
    }
}
