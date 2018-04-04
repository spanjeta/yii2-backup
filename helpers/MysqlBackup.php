<?php

namespace spanjeta\modules\backup\helpers;

use yii\base\Object;
use Yii;
use yii\db\Exception;

class MysqlBackup extends Object {
	public $tables = [ ];
	public $fp;
	public $file_name;
	public $back_temp_file = 'db_backup_';
	public $_path;
	public $enableZip = true;
	public function execSqlFile($sqlFile) {
		$message = "ok";
		
		if (file_exists ( $sqlFile )) {
			
			//$sqlArray = file_get_contents ( $sqlFile );
			$sqlArray = fopen($sqlFile, "r");
			$str = fread($sqlArray,filesize($sqlFile));			
			$sql = str_replace ( ";;;", ";", $str );
			$cmd = Yii::$app->db->createCommand ( $sql);
				
				try {
					$cmd->execute ();
				} catch ( Exception $e ) {
					$message = $e->getMessage ();
				}
		}
		return $message;
	}
	public function clean($ignore = ['tbl-user','tbl_user_role',]) {
		if (! $sql->StartBackup ()) {
			// render error
			return "error";
		}
		
		$message = '';
		
		foreach ( $tables as $tableName ) {
			if (in_array ( $tableName, $ignore ))
				continue;
			fwrite ( $this->fp, '-- -------------------------------------------' . PHP_EOL );
			fwrite ( $this->fp, 'DROP TABLE IF EXISTS ' . addslashes ( $tableName ) . ';' . PHP_EOL );
			fwrite ( $this->fp, '-- -------------------------------------------' . PHP_EOL );
			
			$message .= $tableName . ',';
		}
		$sql->EndBackup ();
		
		// logout so there is no problme later .
		Yii::$app->user->logout ();
		
		$sql->execSqlFile ( $this->file_name );
		
		unlink ( $this->file_name );
	}
	public function getTables($dbName = null) {
		$sql = 'SHOW TABLES';
		$cmd = Yii::$app->db->createCommand ( $sql );
		$tables = $cmd->queryColumn ();
		return $tables;
	}
	public function startBackup($addcheck = true) {
		$this->file_name = $this->path . $this->back_temp_file . date ( 'Y.m.d_H.i.s' ) . '.sql';
		$this->fp = fopen ( $this->file_name, 'w+' );
		
		if ($this->fp == null)
			return false;
		fwrite ( $this->fp, '-- -------------------------------------------' . PHP_EOL );
		if ($addcheck) {
			fwrite ( $this->fp, 'SET AUTOCOMMIT=0;' . PHP_EOL );
			fwrite ( $this->fp, 'START TRANSACTION;' . PHP_EOL );
			fwrite ( $this->fp, 'SET SQL_QUOTE_SHOW_CREATE = 1;' . PHP_EOL );
		}
		fwrite ( $this->fp, 'SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;' . PHP_EOL );
		fwrite ( $this->fp, 'SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;' . PHP_EOL );
		fwrite ( $this->fp, '-- -------------------------------------------' . PHP_EOL );
		$this->writeComment ( 'START BACKUP' );
		return true;
	}
	public function endBackup($addcheck = true) {
		fwrite ( $this->fp, '-- -------------------------------------------' . PHP_EOL );
		fwrite ( $this->fp, 'SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;' . PHP_EOL );
		fwrite ( $this->fp, 'SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;' . PHP_EOL );
		
		if ($addcheck) {
			fwrite ( $this->fp, 'COMMIT;' . PHP_EOL );
		}
		fwrite ( $this->fp, '-- -------------------------------------------' . PHP_EOL );
		$this->writeComment ( 'END BACKUP' );
		fclose ( $this->fp );
		$this->fp = null;
		if ($this->enableZip) {
			
			$this->createZipBackup ();
		}
		return $this->file_name;
	}
	public function getColumns($tableName) {
		$sql = 'SHOW CREATE TABLE ' . $tableName;
		$cmd = Yii::$app->db->createCommand ( $sql );
		$table = $cmd->queryOne ();
		
		$create_query = $table ['Create Table'] . ';';
		
		$create_query = preg_replace ( '/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $create_query );
		$create_query = preg_replace ( '/AUTO_INCREMENT\s*=\s*([0-9])+/', '', $create_query );
		if ($this->fp) {
			$this->writeComment ( 'TABLE `' . addslashes ( $tableName ) . '`' );
			$final = 'DROP TABLE IF EXISTS `' . addslashes ( $tableName ) . '`;' . PHP_EOL . $create_query . PHP_EOL . PHP_EOL;
			fwrite ( $this->fp, $final );
		} else {
			$this->tables [$tableName] ['create'] = $create_query;
			return $create_query;
		}
	}
	public function getData($tableName) {
		$sql = 'SELECT * FROM ' . $tableName;
		$cmd = Yii::$app->db->createCommand ( $sql );
		$dataReader = $cmd->query ();
		
		if ($this->fp)
			$this->writeComment ( 'TABLE DATA ' . $tableName );
		
		foreach ( $dataReader as $data ) {
			$itemNames = array_keys ( $data );
			$itemNames = array_map ( "addslashes", $itemNames );
			$items = join ( '`,`', $itemNames );
			$itemValues = array_values ( $data );
			$itemValues = array_map ( "addslashes", $itemValues );
			$valueString = join ( "','", $itemValues );
			$valueString = "('" . $valueString . "'),";
			$values = "\n" . $valueString;
			
			if ($values != "") {
				$data_string = "INSERT INTO `$tableName` (`$items`) VALUES" . rtrim ( $values, "," ) . ";" . PHP_EOL;
				if ($this->fp)
					fwrite ( $this->fp, $data_string );
			}
		}
		
		if ($this->fp)
			fflush ( $this->fp );
		return true;
	}
	protected function getPath() {
		$this->_path = Yii::$app->basePath . '/db/';
		if (! file_exists ( $this->_path )) {
			@mkdir ( $this->_path, 0775, true );
		}
		return $this->_path;
	}
	private function writeComment($string) {
		fwrite ( $this->fp, '-- -------------------------------------------' . PHP_EOL );
		fwrite ( $this->fp, '-- ' . $string . PHP_EOL );
		fwrite ( $this->fp, '-- -------------------------------------------' . PHP_EOL );
	}
	/**
	 * Charge method to backup and create a zip with this
	 */
	private function createZipBackup() {
		if (class_exists ( \ZipArchive::class )) {
			$zip = new \ZipArchive ();
			$file_name = $this->file_name . '.zip';
			if ($zip->open ( $file_name, \ZipArchive::CREATE ) === TRUE) {
				$zip->addFile ( $this->file_name, basename ( $this->file_name ) );
				$zip->close ();
				
				@unlink ( $this->file_name );
				$this->file_name = $file_name;
			}
		} else {
			echo "ZipArchive missing class ";
		}
	}
	
	/**
	 * Method responsible for reading a directory and add them to the zip
	 *
	 * @param ZipArchive $zip        	
	 * @param string $alias        	
	 * @param string $directory        	
	 */
	private function zipDirectory($zip, $alias, $directory) {
		if ($handle = opendir ( $directory )) {
			while ( ($file = readdir ( $handle )) !== false ) {
				if (is_dir ( $directory . $file ) && $file != "." && $file != ".." && ! in_array ( $directory . $file . '/', $this->module->excludeDirectoryBackup ))
					$this->zipDirectory ( $zip, $alias . $file . '/', $directory . $file . '/' );
				
				if (is_file ( $directory . $file ) && ! in_array ( $directory . $file, $this->module->excludeFileBackup ))
					$zip->addFile ( $directory . $file, $alias . $file );
			}
			closedir ( $handle );
		}
	}
	/**
	 * Zip file execution
	 *
	 * @param string $zipFile
	 *        	Name of file zip
	 */
	public function unzip($sqlZipFile) {
		if (file_exists ( $sqlZipFile )) {
			$zip = new \ZipArchive ();
			$result = $zip->open ( $sqlZipFile );
			if ($result === true) {
				$zip->extractTo ( dirname ( $sqlZipFile ) );
				$zip->close ();
				$sqlZipFile = str_replace ( ".zip", "", $sqlZipFile );
			}
		}
		return $sqlZipFile;
	}
}
