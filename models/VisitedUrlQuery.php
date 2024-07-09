<?php

namespace visitors\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[VisitedUrl]].
 *
 * @see VisitedUrl
 */
class VisitedUrlQuery extends ActiveQuery
{

    /**
     * {@inheritdoc}
     * @return VisitedUrl[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return VisitedUrl|array|null
     */
    public function one($db = null): array|VisitedUrl|null
    {
        return parent::one($db);
    }
}
