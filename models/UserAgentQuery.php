<?php

namespace visitors\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[UserAgent]].
 *
 * @see UserAgent
 */
class UserAgentQuery extends ActiveQuery
{

    /**
     * {@inheritdoc}
     * @return UserAgent[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserAgent|array|null
     */
    public function one($db = null): array|UserAgent|null
    {
        return parent::one($db);
    }
}
