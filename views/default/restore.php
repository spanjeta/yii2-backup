<?php
use yii\helpers\Html;

$this->params ['breadcrumbs'] [] = [ 
		'label' => 'Manage',
		'url' => array (
				'index' 
		) 
];
$this->params['breadcrumbs'][]= [
'label'	=> 'Restore',
'url'	=> array('restore'),
];?>


<?php //  $this->widget('bootstrap.widgets.TbButtonGroup', array(
	//	'buttons'=>$this->actions,
	//	'type'=>'success',
	//	'size'=>'mini',
	//	'htmlOptions'=>array('class'=>'pull-right')
// ));
?>
<h1>
	<?php // echo  $this->action->id; ?>
</h1>

<p>
	<?php if(isset($error)) echo $error; else echo 'Done';?>
</p>
<p>
	<?php // echo Html::link('View Site',Yii::app()->HomeUrl)?>
</p>
