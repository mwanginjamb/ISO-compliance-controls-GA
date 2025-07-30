<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use app\models\Standards;
use yii\httpclient\Client;
use yii\filters\VerbFilter;
use app\models\StandardsSearch;
use yii\httpclient\CurlTransport;
use yii\filters\ContentNegotiator;
use yii\web\NotFoundHttpException;

/**
 * StandardsController implements the CRUD actions for Standards model.
 */
class StandardsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        // 'view' => ['POST'],
                    ],
                ],
                'contentNegotiator' => [
                    'class' => ContentNegotiator::className(),
                    'only' => ['commit', 'status', 'analysis', 'assignees'],
                    'formatParam' => '_format',
                    'formats' => [
                        'application/json' => \yii\web\Response::FORMAT_JSON
                    ]
                ],
            ]
        );
    }

    public function beforeAction($action)
    {

        $ExceptedActions = [
            'commit',
            'status',
            'analysis',
            'assignees'
        ];

        if (in_array($action->id, $ExceptedActions)) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all Standards models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StandardsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Standards model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionVisualization($id)
    {
        $standard = $this->findModel($id);
        // clauses from the 4th clause - use a filter
        $clauses = array_filter($standard->clauses, function ($clause) {
            return $clause->analyzable == TRUE;
        });
        return $this->render('visualization', [
            'model' => $standard,
            'clauses' => $clauses
        ]);
    }

    /**
     * Creates a new Standards model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Standards();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Standards model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Standards model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Standards model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Standards the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Standards::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionCommit()
    {
        try {
            $endpoint = Yii::$app->request->post('service');
            $field = Yii::$app->request->post('name');
            $value = Yii::$app->request->post('value');
            $id = Yii::$app->request->post('key');

            $payload = [
                $field => $value,
                'id' => $id
            ];

            $client = new Client([
                'transport' => CurlTransport::class,
            ]);

            $request = $client->createRequest()
                ->setMethod('PUT')
                ->setUrl($endpoint)
                ->addHeaders(['Content-Type' => 'application/json'])
                ->setFormat(Client::FORMAT_JSON)  // Ensures JSON encoding for request
                ->setData($payload)
                ->setOptions([
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
                ]);

            $response = $request->send();
            Yii::info('Raw response content: ' . $response->content, 'api_debug');
            if ($response->isOk) { // Check if the response status is 200-299
                return $response->data; // Return the relevant response data
            } else {
                // Log error details if needed and return a clear message
                return [
                    'status' => $response->statusCode,
                    'error' => $response->data ?? 'Unexpected error occurred'
                ];
            }
        } catch (\Exception $e) {
            return "HTTP request failed with error: " . $e->getMessage();
        }

    }

    /* Make a Get request for assignes
     * The JSON format is:
     * {
     *   "90254 - melvineobuya@gmail.com": "OBUYA",
     *  "90252 - lauraombogo@gmail.com": "LORRAINE",
     * }
     */

    public function actionAssignees()
    {
        $endpoint = env('ASSIGNEE_ENDPOINT');
        $client = new Client([
            'transport' => CurlTransport::class,
        ]);

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($endpoint)
            ->addHeaders(['Content-Type' => 'application/json'])
            ->setFormat(Client::FORMAT_JSON)  // Ensures JSON encoding for request
            ->setOptions([
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);

        $response = $request->send();
        Yii::info('Raw response content: ' . $response->content, 'api_debug');
        if ($response->isOk) { // Check if the response status is 200-299
            return $response->data; // Return the relevant response data
        } else {
            // Log error details if needed and return a clear message
            return [
                'status' => $response->statusCode,
                'error' => $response->data ?? 'Unexpected error occurred'
            ];
        }
    }

    // Status Drop Down Source
    /*
     *   ◦ Not Implemented (0): No evidence of implementation
     *   ◦ Partially Implemented (1): Some evidence, but significant gaps exist
     *   ◦ Mostly Implemented (2): Substantial evidence, minor gaps exist
     *   ◦ Fully Implemented (3): Complete implementation with evidence
     */

    public function actionStatus()
    {
        $list = [
            (object) ['code' => 0, 'name' => 'Active', 'description' => 'No evidence of implementation'],
            (object) ['code' => 1, 'name' => 'Partial', 'description' => 'Some evidence, but significant gaps exist'],
            (object) ['code' => 2, 'name' => 'Mostly Implemented', 'description' => 'Substantial evidence, minor gaps exist'],
            (object) ['code' => 3, 'name' => 'Complete', 'description' => 'Complete implementation with evidence'],
        ];

        return Yii::$app->utility->dropDown($list, 'name', 'code', ['description']);
    }


    // return a json associative array of clause - average subclauses statuses
    public function actionAnalysis($id)
    {
        $standard = $this->findModel($id);
        // $clauses = $standard->clauses;
        $clauses = array_filter($standard->clauses, function ($clause) {
            return $clause->analyzable == TRUE;
        });
        $data = [];
        foreach ($clauses as $clause) {
            // check if clause has subclauses
            if (empty($clause->subClauses)) {
                continue;
            }
            $data[] = [
                $clause->title => Yii::$app->formatter->asDecimal($clause->getSubClausesAverageStatus(), 1)
            ];
        }
        return $data;

    }

    // Demo view test
    public function actionTest()
    {
        // render without layout
        return $this->render('demodash');
    }

}
