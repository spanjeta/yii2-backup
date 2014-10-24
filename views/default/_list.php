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

				),
				array(
						'class' => 'yii\grid\ActionColumn',
						'template' => '{delete}',

				),
		),
]); ?>