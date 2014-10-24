<?php
$this->params['breadcrumbs'][]= [
'label'	=> 'Backup',
'url'	=> array('backup'),
];?>


<?php   $this->widget('bootstrap.widgets.TbButtonGroup', array(
		'buttons'=>$this->actions,
		'type'=>'success',
		'size'=>'mini',
		'htmlOptions'=>array('class'=>'pull-right')
));
?>
<h1>
	<?php echo  $this->action->id; ?>
</h1>

<p>
	<?php if(isset($error)) echo $error; else echo 'Done';?>
</p>
<p>
	<?php echo CHtml::link('View Site',Yii::app()->HomeUrl)?>
</p>
