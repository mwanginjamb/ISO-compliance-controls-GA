<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Requirements]].
 *
 * @see Requirements
 */
class RequirementsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Requirements[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Requirements|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
