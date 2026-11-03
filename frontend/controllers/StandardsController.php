<?php

namespace frontend\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use app\models\Clause;
use app\models\SubClause;
use app\models\Standards;
use app\models\Requirements;
use yii\httpclient\Client;
use yii\filters\VerbFilter;
use app\models\StandardsSearch;
use yii\filters\AccessControl;
use yii\httpclient\CurlTransport;
use yii\filters\ContentNegotiator;
use frontend\models\CloneStandardForm;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;

/**
 * StandardsController implements the CRUD actions for Standards model.
 */
class StandardsController extends Controller
{
    /**
     * Resources that can be edited inline through actionCommit():
     * REST resource name => [model class, RBAC permission entity, editable fields]
     */
    const INLINE_EDITABLE = [
        'clauses' => [Clause::class, 'clause', ['title', 'analyzable']],
        'sub-clauses' => [SubClause::class, 'sub-clause', ['sub_clause']],
        'requirements' => [
            Requirements::class,
            'requirement',
            ['description', 'status', 'evidence_path', 'gaps', 'actions_required', 'assignee', 'timeline'],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['index', 'view', 'visualization', 'analysis', 'status', 'assignees', 'commit', 'test'],
                            'allow' => true,
                            'roles' => ['@'], // commit checks per-resource permissions itself
                        ],
                        [
                            'actions' => ['create'],
                            'allow' => true,
                            'permissions' => ['create-standard'],
                        ],
                        [
                            'actions' => ['update'],
                            'allow' => true,
                            'permissions' => ['update-standard'],
                        ],
                        [
                            'actions' => ['delete'],
                            'allow' => true,
                            'permissions' => ['delete-standard'],
                        ],
                        [
                            'actions' => ['clone'],
                            'allow' => true,
                            'permissions' => ['clone-standard'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'commit' => ['POST'],
                    ],
                ],
                'contentNegotiator' => [
                    'class' => ContentNegotiator::class,
                    'only' => ['commit', 'status', 'analysis', 'assignees'],
                    'formatParam' => '_format',
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON
                    ]
                ],
            ]
        );
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
     * Clones a standard's clauses, controls and requirements into a new
     * gap analysis for another assessment period.
     * @param int $id ID of the standard to clone
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionClone($id)
    {
        $source = $this->findModel($id);
        $model = new CloneStandardForm($source);

        if ($model->load($this->request->post()) && ($clone = $model->cloneStandard()) !== null) {
            Yii::$app->session->setFlash('success', 'Gap analysis cloned for ' . ($clone->period ?: $clone->standard) . '.');
            return $this->redirect(['view', 'id' => $clone->id]);
        }

        return $this->render('clone', [
            'model' => $model,
            'source' => $source,
        ]);
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

    /**
     * Saves a single inline-edited field.
     *
     * The client posts the record's REST URL as `service`; only the resource
     * name and id are taken from it, and the record is updated locally.
     * Returns the saved record, or an error message string for the UI to toast.
     */
    public function actionCommit()
    {
        $request = Yii::$app->request;
        $path = (string) parse_url((string) $request->post('service'), PHP_URL_PATH);

        if (!preg_match('#/apiv1/([a-z-]+)/(\d+)/?$#', $path, $matches) || !isset(self::INLINE_EDITABLE[$matches[1]])) {
            throw new BadRequestHttpException('Unknown resource.');
        }

        [$modelClass, $entity, $editableFields] = self::INLINE_EDITABLE[$matches[1]];
        $field = $request->post('name');

        if (!in_array($field, $editableFields, true)) {
            throw new BadRequestHttpException('This field cannot be edited.');
        }
        if (!Yii::$app->user->can('update-' . $entity)) {
            throw new ForbiddenHttpException('You are not allowed to update this ' . $entity . '.');
        }
        if (($model = $modelClass::findOne((int) $matches[2])) === null) {
            throw new NotFoundHttpException('The record does not exist.');
        }

        $model->$field = $request->post('value');

        if (!$model->validate([$field]) || !$model->save(false)) {
            return implode(' ', $model->getErrorSummary(true)) ?: 'The change could not be saved.';
        }

        return $model->toArray();
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
    public function actionStatus()
    {
        $list = [];
        foreach (Requirements::STATUS_LABELS as $code => $name) {
            $list[] = (object) ['code' => $code, 'name' => $name, 'description' => Requirements::STATUS_DESCRIPTIONS[$code]];
        }

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
