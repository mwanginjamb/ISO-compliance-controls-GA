<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[SubClause]].
 *
 * @see SubClause
 */
class SubClauseQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SubClause[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SubClause|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
