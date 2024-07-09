<?php

namespace visitors\models;

use Exception;
use users\models\User;
use users\models\UserQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%visitors__visitation}}".
 *
 * @property string $tag Visitation tag
 * @property int $visited_url_id Visited URL ID
 * @property string $ip_address IP Address
 * @property string|null $geo_ip_json Geo IP (JSON)
 * @property int $is_ajax Is AJAX request
 * @property string $method Method
 * @property string|null $headers_json Headers (JSON)
 * @property string|null $cookies_json Cookies (JSON)
 * @property string $created_at Created at (UTC)
 * @property int|null $created_by Created by
 *
 * @property-read User $createdBy {@see Visitation::getCreatedBy()}
 * @property-read VisitedUrl $visitedUrlInstance {@see Visitation::getVisitedUrlInstance()}
 * @property-read UserAgent $userAgentInstance {@see Visitation::getUserAgentInstance()}
 * @property-read array $geoIp {@see Visitation::getGeoIp()}
 * @property-read array $headers {@see Visitation::getHeaders()}
 * @property-read array $cookies {@see Visitation::getCookies()}
 */
class Visitation extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['blameable'] = [
            'class' => BlameableBehavior::class,
            'updatedByAttribute' => false,
        ];
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
            'value' => new Expression('UTC_TIMESTAMP()'),
            'updatedAtAttribute' => false,
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%visitors__visitation}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['tag', 'visited_url_id', 'ip_address'], 'required'],
            [['visited_url_id', 'is_ajax', 'created_by'], 'integer'],
            [['geo_ip_json', 'headers_json', 'cookies_json'], 'string', 'max' => 65535],
            [['created_at'], 'safe'],
            [['tag'], 'string', 'max' => 23],
            [['ip_address'], 'string', 'max' => 39],
            ['method', 'string', 'max' => 5],
            ['tag', 'unique'],
            [
                'created_by',
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['created_by' => 'id']
            ],
            [
                'visited_url_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => VisitedUrl::class,
                'targetAttribute' => ['visited_url_id' => 'id']
            ],
            [
                'user_agent_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => UserAgent::class,
                'targetAttribute' => ['user_agent_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'tag' => Yii::t('visitors.models.Visitation', 'Visitation tag'),
            'visited_url_id' => Yii::t('visitors.models.Visitation', 'Visited URL ID'),
            'visitedUrl' => Yii::t('visitors.models.Visitation', 'Visited URL'),
            'ip_address' => Yii::t('visitors.models.Visitation', 'IP Address'),
            'geo_ip_json' => Yii::t('visitors.models.Visitation', 'Geo IP (JSON)'),
            'geoIp' => Yii::t('visitors.models.Visitation', 'Geo IP'),
            'user_agent_id' => Yii::t('visitors.models.Visitation', 'User Agent ID'),
            'userAgent' => Yii::t('visitors.models.Visitation', 'User Agent'),
            'is_ajax' => Yii::t('visitors.models.Visitation', 'Is AJAX request'),
            'method' => Yii::t('visitors.models.Visitation', 'Method'),
            'headers_json' => Yii::t('visitors.models.Visitation', 'Headers (JSON)'),
            'headers' => Yii::t('visitors.models.Visitation', 'Headers'),
            'cookies_json' => Yii::t('visitors.models.Visitation', 'Cookies (JSON)'),
            'cookies' => Yii::t('visitors.models.Visitation', 'Cookies'),
            'created_at' => Yii::t('visitors.models.Visitation', 'Created at (UTC)'),
            'createdAt' => Yii::t('visitors.models.Visitation', 'Created at'),
            'created_by' => Yii::t('visitors.models.Visitation', 'Created by'),
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return ActiveQuery|UserQuery
     */
    public function getCreatedBy(): ActiveQuery|UserQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[VisitedUrlInstance]].
     *
     * @return ActiveQuery|VisitedUrlQuery
     */
    public function getVisitedUrlInstance(): ActiveQuery|VisitedUrlQuery
    {
        return $this->hasOne(VisitedUrl::class, ['id' => 'visited_url_id']);
    }

    /**
     * Gets query for [[UserAgentInstance]].
     *
     * @return ActiveQuery|UserAgentQuery
     */
    public function getUserAgentInstance(): UserAgentQuery|ActiveQuery
    {
        return $this->hasOne(UserAgent::class, ['id' => 'user_agent_id']);
    }

    /**
     * {@inheritdoc}
     * @return VisitationQuery the active query used by this AR class.
     */
    public static function find(): VisitationQuery
    {
        return new VisitationQuery(static::class);
    }

    public function getGeoIp(): array
    {
        if (empty($this->geo_ip_json)) {
            return [];
        }
        try {
            return Json::decode($this->geo_ip_json);
        } catch (Exception) {
            return [];
        }
    }

    public function getHeaders(): array
    {
        if (empty($this->headers_json)) {
            return [];
        }
        try {
            return Json::decode($this->headers_json);
        } catch (Exception) {
            return [];
        }
    }

    public function getCookies(): array
    {
        if (empty($this->cookies_json)) {
            return [];
        }
        try {
            return Json::decode($this->cookies_json);
        } catch (Exception) {
            return [];
        }
    }
}
