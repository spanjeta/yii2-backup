<div class="backup-default-index">

<?php
$this->params['breadcrumbs'][]= [
		'label'	=> 'Manage',
		'url'	=> array('index'),
];?>

<?php if(Yii::$app->session->hasFlash('success')): ?>
<div class="alert alert-success">
	<?php echo Yii::$app->session->getFlash('success'); ?>
</div>
<?php endif; ?>

<h1>Manage database backup files</h1>

<?php echo $this->render('_list', array(
		'dataProvider'=>$dataProvider,
));
?>
</div>