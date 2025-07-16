<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Standards $model */

$this->title = 'Gap Analysis Visual for ' . $model->standard;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Standards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<use class="visualization-view">

    <h1 class="lead text-center"><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <!-- Tabular Presentation -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title align-self-center">Tabular Clause by Clause Gap Analysis</h3>
                </div>
                <div class="card-body">

                    <div id="scoreTableContainer"></div>

                </div>
            </div>



        </div>
        <div class="col-md-6">
            <!-- Pie Chart: Summarizes how many clauses fall under each implementation level -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Pie Chart: Summarize Implementation Level Distribution</div>
                </div>
                <div class="card-body">
                    <canvas id="levelPieChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Graphical Representation -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title align-self-center">Gaps Analysis Visualization</h3>

                </div>
                <div class="card-body">
                    <!-- Visualization Canvas  -->
                    <canvas id="gapChart"></canvas>

                </div>
            </div>
        </div>

    </div>


    <div class="standard d-none"><?= Yii::$app->request->get('id') ?></div>
    <?php

    $script = <<<JS
    async function drawChart() {
        try{
        const id = +$('.standard').text();
        const Url = './analysis?id=' + id;

            // 1. Fetch clause scores from the backend
        const response = await fetch(Url);
        const rawArray = await response.json();

        // 2. Flatten into a map: { clause: score }
        const clauseData = {};
        rawArray.forEach(obj => {
        const [clause, value] = Object.entries(obj)[0]; // Destructure the only entry
        clauseData[clause] = parseFloat(value); // Ensure score is numeric
        });

        renderScoreTable(clauseData); // Tabular Representation
       // drawChart(clauseData); // render clause implementation level distribution chart

        console.log(clauseData);

        // 3. Prepare labels, data, and colors
        const labels = Object.keys(clauseData);
        const dataValues = Object.values(clauseData);

        const getColor = (score) => {
        if (score < 0.5) return '#e74c3c';         // Not Implemented
        if (score < 1.5) return '#f39c12';         // Partially Implemented
        if (score < 2.5) return '#f1c40f';         // Mostly Implemented
        return '#2ecc71';                          // Fully Implemented
        };

        const colors = dataValues.map(getColor);

        // 4. Optional: calculate average
        const avgScore = dataValues.reduce((sum, score) => sum + score, 0) / dataValues.length;

        // 5. Configure the chart
        const config = {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
            {
                label: 'Implementation Score',
                data: dataValues,
                backgroundColor: colors,
                borderRadius: 5,
                borderWidth: 1
            },
            {
                label: 'Average Score',
                data: Array(dataValues.length).fill(avgScore),
                type: 'line',
                borderColor: '#3498db',
                borderWidth: 2,
                pointRadius: 0,
                fill: false,
                tension: 0.4
            }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                datalabels: {
                anchor: 'end',
                align: 'right',
                formatter: (value) => {
                        if (value < 0.5) return 'Not Implemented';
                        if (value < 1.5) return 'Partially';
                        if (value < 2.5) return 'Mostly';
                        return 'Fully';
                    },
                    color: '#000',
                    font: {
                        weight: 'bold'
                    }
            },
            legend: {
                display: true,
                position: 'right',
                labels: {
                usePointStyle: true
                },
                onClick: (event, legendItem, legend) => {
                const index = legendItem.datasetIndex;
                const chart = legend.chart;
                const meta = chart.getDatasetMeta(index);
                meta.hidden = !meta.hidden;
                chart.update();
                }
            },
            title: {
                display: true,
                text: 'ISO/IEC 27001 Gap Analysis by Clause'
            },
            tooltip: {
                callbacks: {
                label: function(ctx) {
                    const value = ctx.raw;
                    let label;
                    if (value < 0.5) label = 'Not Implemented';
                    else if (value < 1.5) label = 'Partially Implemented';
                    else if (value < 2.5) label = 'Mostly Implemented';
                    else label = 'Fully Implemented';
                    return label +' '+ value;
                 }
                }
            },
            legend: {
                display: true
            }
            },
            scales: {
            x: {
                min: 0,
                max: 3,
                title: {
                display: true,
                text: 'Score (0 - 3)'
                }
            },
            y: {
                title: {
                display: true,
                text: 'ISO 27001 Clauses'
                }
            }
            }
        }
        };

        // 6. Render the chart
        new Chart(document.getElementById('gapChart'), config);

        }catch(err){
            console.error('Failed to load clause analysis data:', err);
        }
    }
    
    drawChart();    
JS;
    $this->registerJs($script);
    ?>