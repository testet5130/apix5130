<?php

    require_once("functions.php");

    class Connection {

        protected $conn;

        public function __construct() {
            try {
                
                $this->conn = new PDO("mysql:host=localhost;dbname=".DBNAME, DBUSER, DBPASS);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $this->conn->prepare("CREATE TABLE IF NOT EXISTS users(
                    userid int(11) UNSIGNED AUTO_INCREMENT,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    pass VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    deleted_at TIMESTAMP DEFAULT 0,
                    PRIMARY KEY(userid)
                )");
                $stmt->execute();
                
                $stmt = $this->conn->prepare("CREATE TABLE IF NOT EXISTS products(
                    productid int(11) UNSIGNED AUTO_INCREMENT,
                    product_name VARCHAR(255) NOT NULL,
                    product_price int(10) NOT NULL DEFAULT 1,
                    serial_num VARCHAR(255),
                    is_checked int(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    deleted_at TIMESTAMP DEFAULT 0,
                    PRIMARY KEY(productid)
                )");
                $stmt->execute();

            } catch( PDOException $e ) {
                if( debug() ) {
                    echo $e->getMessage();
                }
            }
        }
    
    }

    class DB extends Connection{
        
        protected $pdo;

        function __construct() {
            $connection = new Connection();
            $this->pdo = $connection->conn;
        }

        function init() {
            $x = new Connection();
            $this->pdo = $x->conn;
            
        }

        public function store($val=[]) { 
            try {

                $errors = [];
                
                foreach(array_keys($val) as $value) {
                    if( !in_array($value, $this->fillable) ) {
                        $errors["invalid"] = "error 10";
                    }
                }

                if( count($errors) == 0 ) {
                    if( count($this->fillable) > 0 ) {
                        $keys = "";
                        foreach( array_keys($val) as $key ) {
                            $keys .= $key.',';
                        }
                        $keys = substr($keys, 0, -1);
                        
                        if( count($val) <= count($this->fillable) ) {
                            $values = "";
                            foreach( $val as $value ) {
                                $values .= "'".$value."',";
                            }
                            $values = substr($values, 0, -1);
                        } else {
                            $errors["valueCount"] = "Fill all required fields";
                        }
                    } else {
                        $errors["fillable"] = "Add column to fillable";
                    }
                }
                if( count($errors) == 0 ) {
                    $q = "INSERT INTO ".$this->tableName."(". $keys .") VALUES(".$values.")";
                    $stmt = $this->pdo->prepare($q);
                    $stmt->execute();
                    return $stmt->rowCount();
                } else {
                    if( debug() ) {
                        echo json_encode($errors);
                    }
                }
            } catch (Exception $e) {
                if( debug() ) {
                    echo $e->getMessage();
                }
            }
        }

        public function update($val=[]) { 
            try {

                $errors = [];

                if( count($errors) == 0 ) {
                    $params = [];
                    $paramStr = "updated_at = CURRENT_TIMESTAMP";
                    for($i = 0;$i < count($val);$i++) {
                        $key = array_keys($val)[$i];
                        $value = array_values($val)[$i];
                        if( $key != $this->primaryKeyName ) {
                            $params[$key] = $value;
                            $paramStr .= ", ".array_keys($val)[$i]."=:".array_keys($val)[$i];
                        }
                    }

                    $paramStr .= " WHERE ".$this->primaryKeyName."=:".$this->primaryKeyName;
                    $params[$this->primaryKeyName] = $val[$this->primaryKeyName]; 

                }

                if( count($errors) == 0 ) {
                    $q = "UPDATE ".$this->tableName." SET ".$paramStr;
                    // $q .= AND deleted_at='0000-00-00 00:00:00'";
                    // echo $q;
                    // echo $paramStr;
                    $stmt = $this->pdo->prepare($q);
                    $stmt->execute($params);
                    return $stmt->rowCount();
                } else {
                    if( debug() ) {
                        echo json_encode($errors);
                    }
                }
            } catch (Exception $e) {
                if( debug() ) {
                    echo $e->getMessage();
                }
            }
        }

        public function all() { 
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM ".$this->tableName." WHERE deleted_at = '0000-00-00 00:00:00'");
                $stmt->execute();
                if( $stmt->rowCount() > 0 ) {
                    return json_encode($stmt->fetchAll());
                } else {
                    return false;
                }
            } catch (Exception $e) {
                if( debug() ) {
                    echo $e->getMessage();
                }
            }
        }

        public function find($primaryKey) {
            try {
                $stmt = $this->pdo->prepare("SELECT * FROM ".$this->tableName." WHERE deleted_at = '0000-00-00 00:00:00' AND ".$this->primaryKeyName.'=:primaryKey');
                $stmt->execute(array(
                    ":primaryKey"=>$primaryKey
                ));
                if( $stmt->rowCount() > 0 ) {
                    return json_encode($stmt->fetch());
                } else {
                    return false;
                }
            } catch (Exception $e) {
                if( debug() ) {
                    echo $e->getMessage();
                }
            }
        }

        public function where($conditions=[]) { 
            try {
                if( count($conditions) > 0 ) {
                    $cond = "";
                    $params = [];
                    for($i = 0;$i < count($conditions);$i++) {
                        if( $i < count($conditions)-1 ) {
                            $cond .= array_keys($conditions)[$i]."=:".array_keys($conditions)[$i]." AND ";
                            $params[array_keys($conditions)[$i]] = esc_char(array_values($conditions)[$i]);
                        } else {
                            $cond .= array_keys($conditions)[$i]."=:".array_keys($conditions)[$i];
                            $params[array_keys($conditions)[$i]] = esc_char(array_values($conditions)[$i]);
                        }
                    }
                    $q = "SELECT * FROM ".$this->tableName." WHERE deleted_at = '0000-00-00 00:00:00' AND ".$cond;
                    $stmt = $this->pdo->prepare($q);
                    $stmt->execute($params);
                    if( $stmt->rowCount() > 0 ) {
                        return json_encode($stmt->fetchAll());
                    } else {
                        return 0;
                    }
                } else {
                    echo "null";
                }
            } catch (Exception $e) {
                if( debug() ) {
                    echo $e->getMessage();
                }
            }
        }

        public function delete($val=[]) {
            if( count($val) > 0 ) {
                $q = "UPDATE ".$this->tableName." SET deleted_at=CURRENT_TIMESTAMP WHERE ".$this->primaryKeyName.'=:'.$this->primaryKeyName;
                $stmt = $this->pdo->prepare($q);
                $stmt->execute($val);
                return $stmt->rowCount();
            }
        }

        public function destroy($val=[]) {
            if( count($val) > 0 ) {
                $q = "DELETE FROM ".$this->tableName." WHERE ".$this->primaryKeyName.'=:'.$this->primaryKeyName;
                $stmt = $this->pdo->prepare($q);
                $stmt->execute($val);
                return $stmt->rowCount();
            }
        }

    }

?>