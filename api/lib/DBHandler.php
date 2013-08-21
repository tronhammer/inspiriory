<?php
/**
 * Database Handler
 *
 * Currently only exists as a PDO wrapper, but will eventually be expanded into handling multiple disparate connecitons
 * to multiple database platforms. 
 * Inlcuding:
 *
 *  - MySQL
 *  - Postgres
 *  - Hadoop
 *  - Memcached
 */
	
	class DBHandler {
		private static $DBHOST = 'data001';
		private static $DBENTRY = 'inspiriory';
		private static $DBUSER = 'dbu1';
		private static $DBPASS = 'testopresto';
		
		private static $tables = array();
		
		private $connection;
		private $table;
		
		const CREATE = 1;
		const DELETE = 2;
		
		public function __construct($lib){
			/** @todo find better way of doing this. */
			$this->lib = $lib;
			
			try {
				$this->connection = new PDO(
					'mysql:host=' . self::$DBHOST . ';dbname=' . self::$DBENTRY, 
					self::$DBUSER, 
					self::$DBPASS
				);
			} catch( PDOExecption $e ) { 
				echo $this->lib['wrap']( array(), 1, $e->getMessage() );
				exit(1);
			} 
		}
		
		public function setTable($table){
			$this->table = self::$tables[ $table ];
			return $this->table;
		}
		
		public function get($tableName, $what = "*", $with = array(), $where = ""){
			$table = $this->setTable( $tableName );
			
			$sql = 'SELECT ' . $what . ' FROM `' . $tableName .'`';
			
			if (count($with)){
				$sql .= ' WHERE ' . $where;
			}
			
			$sth = $this->connection->prepare($sql);
			
			$sth->execute( $with );
			
			return $sth->fetchAll( PDO::FETCH_ASSOC );
		}
		
		public function create($tableName, $params = array()){
			$with = $this->_build_with($tableName, $params, DBHandler::CREATE );
			$sql = $this->_build_insert_sql($with);
			
			try { 
				$this->connection->beginTransaction();
				
				$sth = $this->connection->prepare($sql);
			
				$sth->execute( $with );
				
				$id = $this->connection->lastInsertId();
				
				if ($sth->errorCode() == 0){
					if ($this->connection->commit()){
						return array(
							"id" => $id
						);
					}
				} else {
					$issues = $sth->errorInfo();
					if ($this->connection->rollBack()){
						return $issues;
					} else {
						return $sth->errorInfo();
					}
				}
			} catch(PDOExecption $e) { 
				$this->connection->rollBack();
				echo $this->lib['wrap']( array(), 1, $e->getMessage() );
				exit(1);
			}
			
			return false;
		}
		
		public function delete($tableName, $params = array()){
			$with = $this->_build_with($tableName, $params,  DBHandler::DELETE );
			$sql = $this->_build_delete_sql($with);
			
			try { 
				$this->connection->beginTransaction();
				
				$sth = $this->connection->prepare($sql);
			
				$sth->execute( $with );
				
				$id = $this->connection->lastInsertId();
				
				if ($sth->errorCode() == 0){
					if ($this->connection->commit()){
						return array(
							"id" => $id
						);
					}
				} else {
					$issues = $sth->errorInfo();
					if ($this->connection->rollBack()){
						return $issues;
					} else {
						return $sth->errorInfo();
					}
				}
			} catch(PDOExecption $e) { 
				$this->connection->rollBack();
				echo $this->lib['wrap']( array(), 1, $e->getMessage() );
				exit(1);
			}
			
			return false;
		}
		
		private function _build_with($table, $fields, $action){
			$table = $this->setTable( $table );
			$with = array('remove' => array());
			
			foreach ($table['fields'] as $name=>$info){
				if (isset($fields[ $name ]) 
					&& (
						!isset($info['hidden_to']) // if not set don't worry about it
						|| is_array($info['hidden_to']) && !in_array($action, $info['hidden_to']) // can be array
						|| is_string($info['hidden_to']) && $info['hidden_to'] != $action // or string
					)
				){
					$field = $fields[ $name ];
					$validate_fn = array($table['name'], $info['validate']);
					/** @todo auto create the validate funciton name derived from field name */
					if (method_exists($table['name'], $info['validate']) 
						&& is_callable($table['name'], $info['validate']) 
						&& call_user_func($validate_fn, $field)
					){
						$with[':' . $name] = $field;
					} else {
						echo $this->lib['wrap']( array(), 1, '"'.$name.'" did not pass validation!' );
						exit(1);
					}
				} else if (isset($info['required_to']) 
					&& (
						is_array($info['required_to']) && in_array($action, $info['required_to']) // can be array
						|| is_string($info['required_to']) && $info['required_to'] == $action // or string
					)
				){
					if (array_key_exists('default', $info)){
						$with[':' . $name] = $info['default'];
					} else {
						echo $this->lib['wrap']( array(), 1, '"'.$name.'" is a required field!' );
						exit(1);
					}
				} else {
					$with['remove'][] = $name;
				}
			}
			
			return $with;
		}
		
		private function _build_fields(&$with){
			$fields = array_merge(array(), $this->table['fields']);
			
			if (isset($with['remove'])){
				$remove = $with['remove'];
				if (is_array($remove)){
					foreach ($remove as $name) {
						unset( $fields[ $name ] );
					}
				}
				unset( $with['remove'] );
			}
			
			return $fields;
		}
		
		private function _build_insert_sql(&$with){
			$fields = $this->_build_fields($with);
			
			$fieldNames = implode("`, `", array_keys($fields));
			$fieldTransients = implode(", :", array_keys($fields));
			
			return 'INSERT INTO `' . $this->table['id'] . '` '
				. '(`'. $fieldNames .'`)'
				. ' VALUES '
				. '(:'. $fieldTransients .')';
		}
		
		private function _build_delete_sql(&$with){
			$fields = $this->_build_fields($with);
			$where = "";
			
			foreach ($fields as $field=>$info){
				$where .= "`" . $field . "`=:". $field;
			}
			
			return 'DELETE FROM  `' . $this->table['id'] . '` '
				. 'WHERE ' . $where;
		}
		
		static public function build_tables(){
			/** @todo Loop through tables and add them each by id; */
			self::$tables = array(
				'questions' => Questions::$data
			);
		}
		
	}
	
	
	
	/** @todo Loop through tables and add them each by id; */
	DBHandler::build_tables();