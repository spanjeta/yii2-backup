<?php
use yii\helpers\Html;
use yii\grid\GridView;

echo GridView::widget([
		'id' => 'install-grid',
		'dataProvider' => $dataProvider,
		
		'columns' => array(
				'name',
				'size:size',
				'create_time',
				'modified_time:relativeTime',
				array(
						'class' => 'yii\grid\ActionColumn',
						'template' => '{restore}',
						'buttons' => ['restore' => function ($url, $model, $key) {
												        return Html::a('<span class="glyphicon glyphicon-circle-arrow-left"></span>', $url, ['title' => 'Restore']);
												    }
						],

				),
				
				array(
						'class' => 'yii\grid\ActionColumn',
						'template' => '{download}',
						'buttons' => ['download' => function ($url, $model, $key) {
												        return Html::a('<span class="glyphicon glyphicon-download"></span>', $url, ['title' => 'Download']);
												    }
							],
				),
				
				array(
						'class' => 'yii\grid\ActionColumn',
						'template' => '{delete}',

				),
		),
]); ?>
