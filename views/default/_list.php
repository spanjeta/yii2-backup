<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

echo GridView::widget ( [ 
		'id' => 'install-grid',
		'dataProvider' => $dataProvider,
		'columns' => array (
				'name',
				'size:shortSize',
				'create_time:datetime',
				array (
						'header' => 'Delete DB',
						'class' => 'yii\grid\ActionColumn',
						'template' => '{restore}{delete}',
						'buttons' => [ 
								'delete' => function ($url, $model) {
									return Html::a ( '<span class="glyphicon glyphicon-remove"></span>', $url, [ 
											'title' => Yii::t ( 'app', 'Delete this backup' ) ,'data-method'=>'post'
									] );
								},
								
								'restore' => function ($url, $model) {
									return Html::a ( '<span class="glyphicon glyphicon-save"></span>', $url, [ 
											'title' => Yii::t ( 'app', 'Restore this backup' ) ,'data-method'=>'post'
									] );
								} 
						],
						'urlCreator' => function ($action, $model, $key, $index) {
							
								$url = Url::toRoute ( [ 
										'default/' .$action,
										'file' => $model ['name'] 
								] );
								return $url;
							
						} 
				)
				 
		) 
] );
?>