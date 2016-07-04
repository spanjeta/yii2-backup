<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

echo GridView::widget([
		'id' => 'install-grid',
		'dataProvider' => $dataProvider,
		'columns' => array(
				'name',
				'size:size',
				'create_time',
				'modified_time',
				[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{restore_action}',
				'header' => 'Restore DB',
				'buttons' => [
						'restore_action' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-import"></span>', $url, [
								'title' => Yii::t('app', 'Restore this backup'),
						]);
						}
						],
						'urlCreator' => function ($action, $model, $key, $index) {
						if ($action === 'restore_action') {
							$url = Url::toRoute(['default/restore','filename'=>$model['name']]);
							return $url;
						}
						}
						],
				
				array(
						'header' => 'Delete DB',
						'class' => 'yii\grid\ActionColumn',
						'template' => '{delete}',
						'buttons'=>[
								'delete' => function ($url, $model) {
								return Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
										'title' => Yii::t('app', 'Delete this backup'),
								]);
								}
								],
								'urlCreator' => function ($action, $model, $key, $index) {
								if ($action === 'delete') {
							$url = Url::toRoute(['default/delete','file'=>$model['name']]);
									return $url;
								}
								}

				),
		),
]); ?>