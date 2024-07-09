<?php

namespace visitors\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%visitors__user_agent}}".
 *
 * @property int $id User Agent ID
 * @property string $name Name
 * @property string|null $version Branding and version
 * @property string|null $full_version Full version list
 * @property string|null $arch Underlying platform architecture
 * @property string|null $bitness Underlying CPU architecture bitness
 * @property string|null $mobile Mobile device
 * @property string|null $model Device model
 * @property string|null $platform Underlying operating system/platform
 * @property string|null $platform_version Underlying operating system version
 * @property string|null $prefers_color_scheme Preference of dark or light color scheme
 * @property string|null $prefers_reduced_motion Preference of reduced motion
 */
class UserAgent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%visitors__user_agent}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'version', 'full_version', 'mobile'], 'string', 'max' => 255],
            [['arch', 'bitness'], 'string', 'max' => 10],
            [['model', 'platform', 'platform_version', 'prefers_color_scheme', 'prefers_reduced_motion'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('visitors.models.UserAgent', 'User Agent ID'),
            'name' => Yii::t('visitors.models.UserAgent', 'Name'),
            'version' => Yii::t('visitors.models.UserAgent', 'Branding and version'),
            'full_version' => Yii::t('visitors.models.UserAgent', 'Full version list'),
            'arch' => Yii::t('visitors.models.UserAgent', 'Underlying platform architecture'),
            'bitness' => Yii::t('visitors.models.UserAgent', 'Underlying CPU architecture bitness'),
            'mobile' => Yii::t('visitors.models.UserAgent', 'Mobile device'),
            'model' => Yii::t('visitors.models.UserAgent', 'Device model'),
            'platform' => Yii::t('visitors.models.UserAgent', 'Underlying operating system/platform'),
            'platform_version' => Yii::t('visitors.models.UserAgent', 'Underlying operating system version'),
            'prefers_color_scheme' => Yii::t('visitors.models.UserAgent', 'Preference of dark or light color scheme'),
            'prefers_reduced_motion' => Yii::t('visitors.models.UserAgent', 'Preference of reduced motion'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return UserAgentQuery the active query used by this AR class.
     */
    public static function find(): UserAgentQuery
    {
        return new UserAgentQuery(static::class);
    }
}
