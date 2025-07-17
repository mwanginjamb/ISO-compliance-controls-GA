<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Tenants]].
 *
 * @see Tenants
 */
class TenantsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Tenants[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Tenants|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
