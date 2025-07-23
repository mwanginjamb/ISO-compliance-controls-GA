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

    // Average Compliance Across all clauses
    public function getAverageCompliance($id)
    {
        $standard = Standards::findOne($id);
        // clauses from the 4th clause - use a filter
        $clauses = array_filter($standard->clauses, function ($clause) {
            // return $clause->analyzable == TRUE;
            return $clause->id >= 4;
        });
        // calculate average for all clauses
        $average = 0;
        foreach ($clauses as $clause) {
            $average += $clause->getSubClausesAverageStatus();
        }
        return Yii::$app->formatter->asDecimal($average / count($clauses), 2);
    }

    // Get Number of Clauses By Compliance Levels based on score thresholds
    public function getNonCompliantClauses($id)
    {
        $standard = Standards::findOne($id);
        $clauses = array_filter($standard->clauses, function ($clause) {
            // filter by subclauseAverageStatus
            return $clause->getSubClausesAverageStatus() <= 0.5;
        });
        return count($clauses);
    }

    // Get Number of Partially compliant Clauses
    public function getPartiallyCompliantClauses($id)
    {
        $standard = Standards::findOne($id);
        $clauses = array_filter($standard->clauses, function ($clause) {
            // filter by subclauseAverageStatus
            return $clause->getSubClausesAverageStatus() >= 0.5 && $clause->getSubClausesAverageStatus() < 1.5;
        });
        return count($clauses);
    }
}