<?php
/**
 * Contains \visitors\migrations\m240708_211600_create_user_agent
 */

namespace visitors\migrations;

use deele\devkit\db\SchemaHelper;
use yii\db\Migration;

/**
 * Migration class that creates "visitors__user_agent" table
 *
 * @package visitors\migrations
 */
class m240708_211600_create_user_agent extends Migration
{

    public string $tableName = 'visitors__user_agent';

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
                    ->comment('User Agent ID'),
                'name' => $this->string()
                    ->comment('Name')
                    ->notNull(),
                'version' => $this->string()
                    ->comment('Branding and version'),
                'full_version' => $this->string()
                    ->comment('Full version list'),
                'arch' => $this->string(10)
                    ->comment('Underlying platform architecture'),
                'bitness' => $this->string(10)
                    ->comment('Underlying CPU architecture bitness'),
                'mobile' => $this->string(10)
                    ->comment('Mobile device'),
                'model' => $this->string(50)
                    ->comment('Device model'),
                'platform' => $this->string(50)
                    ->comment('Underlying operating system/platform'),
                'platform_version' => $this->string(50)
                    ->comment('Underlying operating system version'),
                'prefers_color_scheme' => $this->string(50)
                    ->comment('Preference of dark or light color scheme'),
                'prefers_reduced_motion' => $this->string(50)
                    ->comment('Preference of reduced motion'),
            ],
            'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB'
        );

        return true;
    }

    /**
     * @return bool
     */
    public function deleteMainTable(): bool
    {
        $tableName = SchemaHelper::prefixedTable($this->tableName);

        $this->dropTable($tableName);

        return true;
    }
}
