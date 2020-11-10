<?php

    require_once("tables.php");

    new Connection();

    if($_SERVER["REQUEST_METHOD"] == "GET") {
        if( isset($_GET["p"]) && !empty($_GET["p"]) ) {
            if(is_numeric($_GET["p"])) {
                $p = intval($_GET['p']);
                $products = new Products();
                $params = ["serial_num"=>$p];
                if($products->where($params) != "0") {
                    $params = ["is_checked"=>"0", "serial_num"=>$p];
                    $id = $products->where($params);
                    if($id != "0") {
                        $id = json_decode($id)[0]->productid;
                        $params = ["is_checked"=>"1", "productid"=>$id];
                        $products->update($params);
                        echo "product has been verified";
                    } else {
                        echo "product had been verified";
                    }
                } else {
                    echo "this product isn't ours";
                }
            } else {
                echo "serial number is invalid";
            }
        }
    }

?>
