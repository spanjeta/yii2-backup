<?php

namespace spanjeta\modules\backup\commands;

use spanjeta\modules\backup\helpers\MysqlBackup;
use yii\console\Controller;

class BackupController extends Controller {
	public $dryrun=false;
	public $file=null;
	public function options($actionID) {
		return [ 
				'dryrun',
				'file'
		];
	}
	public function optionAliases() {
		return [ 
				'd' => 'dryrun',
				'f'=>'file'
		];
	}
	public function log($string) {
		echo $string . PHP_EOL;
	}
	protected function getFileList($ext = '*.sql') {
		$sql = new MysqlBackup ();
		$path = $sql->path;
		$dataArray = array ();
		$list = array ();
		$list_files = glob ( $path . $ext );
		if ($list_files) {
			$list = array_map ( 'basename', $list_files );
			sort ( $list );
		}
		return $list;
	}
	public function actionCreate() {
		$sql = new MysqlBackup ();
		
		if (! $sql->startBackup ()) {
			$this->log ( __FUNCTION__ . ":Started" );
		}
		$tables = $sql->getTables ();
		foreach ( $tables as $tableName ) {
			$sql->getColumns ( $tableName );
		}
		foreach ( $tables as $tableName ) {
			$sql->getData ( $tableName );
		}
		
		$sqlFile = $sql->endBackup ();
		$this->log ( __FUNCTION__ . ":Finished : " . $sqlFile );
		return $sqlFile;
	}
	public function actionRestore() {
		$message = 'NOK';
		$dryrun=$this->dryrun;
		$file = $this->file;
		if ($file == null) {
			$files = $this->getFileList ();
			if (empty ( $files )) {
				$this->log ( __FUNCTION__ . " : No Files dound" );
			}
			$file = $files [0];
		}
		
		$this->log ( __FUNCTION__ . " : " . $file );
		$sql = new MysqlBackup ();
		
		$sqlZipFile = $file;
		if (! file_exists ( $file )) {
			$sqlZipFile = $sql->path . basename ( $file );
		}
		$sqlFile = $sql->unzip ( $sqlZipFile );
		if (! $dryrun)
			$message = $sql->execSqlFile ( $sqlFile );
		
		$this->log ( __FUNCTION__ . " : " . $message );
		return $message;
	}
}
