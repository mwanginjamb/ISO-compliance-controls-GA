<?php
namespace common\Library;
use Yii;
use yii\base\Component;
use app\models\Standards;



class Dashboard extends Component
{
    public function countStandards()
    {
        $standards = Standards::find()->asArray()->all();
        return count($standards);
    }
}