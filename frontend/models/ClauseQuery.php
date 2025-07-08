<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Clause]].
 *
 * @see Clause
 */
class ClauseQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Clause[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Clause|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
