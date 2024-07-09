<?php

namespace visitors\models;

use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%visitors__visited_url}}".
 *
 * @property int $id Visited URL ID
 * @property string $url URL
 * @property string $query_params_json Query Params (JSON)
 * @property int $hits Hits
 * @property int $status Status
 *
 * @property-read Visitation[] $visitations {@see VisitedUrl::getVisitations()}
 * @property-read string|null $statusName {@see VisitedUrl::getStatusName()}
 * @property-read array $queryParams {@see VisitedUrl::getQueryParams()}
 */
class VisitedUrl extends ActiveRecord
{
    public const STATUS_OK = 1;
    public const STATUS_ERROR = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%visitors__visited_url}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['url', 'required'],
            [['hits', 'status'], 'integer'],
            ['url', 'string', 'max' => 2000],
            ['query_params_json', 'string', 'max' => 65535],
            ['status', 'in', 'range' => array_keys(static::allStatuses())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('visitors.models.VisitedUrl', 'Visited URL ID'),
            'url' => Yii::t('visitors.models.VisitedUrl', 'URL'),
            'query_params_json' => Yii::t('visitors.models.VisitedUrl', 'Query Params (JSON)'),
            'queryParams' => Yii::t('visitors.models.VisitedUrl', 'Query Params'),
            'hits' => Yii::t('visitors.models.VisitedUrl', 'Hits'),
            'status' => Yii::t('visitors.models.VisitedUrl', 'Status'),
        ];
    }

    /**
     * Gets query for [[VisitorsVisitations]].
     *
     * @return ActiveQuery|VisitationQuery
     */
    public function getVisitations(): ActiveQuery|VisitationQuery
    {
        return $this->hasMany(Visitation::class, ['visited_url_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return VisitedUrlQuery the active query used by this AR class.
     */
    public static function find(): VisitedUrlQuery
    {
        return new VisitedUrlQuery(static::class);
    }

    public static function allStatuses(): array
    {
        return [
            static::STATUS_OK => Yii::t('visitors.models.VisitedUrl', 'OK'),
            static::STATUS_ERROR => Yii::t('visitors.models.VisitedUrl', 'Error'),
        ];
    }

    public static function createStatusName(int $status): ?string
    {
        return static::allStatuses()[$status] ?? null;
    }

    public function getStatusName(): ?string
    {
        return static::createStatusName($this->status);
    }

    public function getQueryParams(): array
    {
        if (empty($this->query_params_json)) {
            return [];
        }
        try {
            return Json::decode($this->query_params_json);
        } catch (Exception) {
            return [];
        }
    }
}
