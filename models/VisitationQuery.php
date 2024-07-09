<?php

namespace visitors\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Visitation]].
 *
 * @see Visitation
 */
class VisitationQuery extends ActiveQuery
{

    /**
     * {@inheritdoc}
     * @return Visitation[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Visitation|array|null
     */
    public function one($db = null): array|Visitation|null
    {
        return parent::one($db);
    }
}
