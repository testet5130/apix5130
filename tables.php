<?php

    require_once("db.php");

    class Users extends DB {
        protected $tableName = "users";
        protected $primaryKeyName = "userid";
        protected $fillable = ["email", "pass", "name"];
    }
    
    class Products extends DB {
        protected $tableName = "products";
        protected $primaryKeyName = "productid";
        protected $fillable = ["is_checked"];
    }

?>