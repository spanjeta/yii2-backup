<?php


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
<h1>
	<?php echo  $this->context->action->id; ?>
</h1>

<p>
	<?php if(isset($error)) echo $error; else echo 'Done';?>
</p>

