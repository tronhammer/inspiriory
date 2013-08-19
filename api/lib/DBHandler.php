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
		
		public function get($what = "*", $with = array(), $where = ""){
			$sql = 'SELECT ' . $what . ' FROM `' . $this->table['id'] .'`';
			
			if (count($with)){
				$sql .= ' WHERE ' . $where;
			}
			
			$sth = $this->connection->prepare($sql);
			
			$sth->execute( $with );
			
			return $sth->fetchAll( PDO::FETCH_OBJ );
		}
		
		public function create($table, $params = array()){
			$with = $this->_build_insert_with($table, $params);
			
			if (isset($with['remove'])){
				$sql = $this->_build_insert_sql($with['remove']);
				unset($with['remove']);
			} else {
				$sql = $this->_build_insert_sql($with);
			}
			
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
		
		private function _build_insert_with($table, $fields){
			$table = $this->setTable( $table );
			$with = array('remove' => array());
			
			foreach ($table['fields'] as $name=>$info){
				error_log("I want ".$name);
				if (isset($fields[ $name ])){
					error_log("is in fields");
					$field = $fields[ $name ];
					$validate_fn = array($table['name'], $info['validate']);
					if (method_exists($table['name'], $info['validate']) 
						&& is_callable($table['name'], $info['validate']) 
						&& call_user_func($validate_fn, $field)){
						
						$with[':' . $name] = $field;
					} else {
						echo $this->lib['wrap']( array(), 1, '"'.$name.'" did not pass validation!' );
						exit(1);
					}
				} else if (isset($info['required']) && $info['required'] === 1){
					error_log("is required");
					echo $this->lib['wrap']( array(), 1, '"'.$name.'" is a required field!' );
					exit(1);
				} else if (array_key_exists('default', $info)){
					error_log("adding this guy ".$info['name']);
					$with[':' . $name] = $info['default'];
				} else {
					error_log("removing this...");
					$with['remove'][] = $name;
				}
			}
			
			return $with;
		}
		
		private function _build_insert_sql($remove){
			$fields = array_merge(array(), $this->table['fields']);
			
			foreach ($remove as $name) {
				unset($fields[ $name ]);
			}
			
			$fieldNames = implode(", ", array_keys($fields));
			$fieldTransients = implode(", :", array_keys($fields));
			
			return 'INSERT INTO `' . $this->table['id'] . '` '
				. '('. $fieldNames .')'
				. ' VALUES '
				. '(:'. $fieldTransients .')';
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