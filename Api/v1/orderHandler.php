<?php

$app->post('/checkout', function() use ($app) {
	$db = new DbHandler();
	$response = array();
	$r = json_decode($app->request->getBody());
	// verifyRequiredParams(array('total_price', 'total_quantity', 'data'),$r);
	$sales = $r->data;

	$table_name = "order";
	$coloum_names = array('product_id', 'quantity', 'buyer', 'total_price');
    $result = $db->InsertIntoTable($r, $coloum_names, $table_name);
    if($result != null){
    	foreach($sales as $sale){
			$result = $db->InsertIntoTable($sale, array('product_id', 'quantity', 'buyer', 'total_price'), $table_name);
		}
		$response["status"] = "success";
        $response["message"] = "Checkout Complete Please Print Receipt";
        echoResponse(200, $response);
    }
    else{
    	$response["status"] = "error";
        $response["message"] = "Something Went Wrong";
        echoResponse(201, $response);
    }  
});


// transactions for  buyer side
$app->get('/transaction/:id', function($id) use ($app) {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT products.product_name, users.name, orders.* FROM orders LEFT JOIN products ON (orders.product_id = products._id) LEFT JOIN users ON (users._id = products.seller) WHERE orders.buyer = '$id'");

    $response["status"] = "success";
    $response["products"] = array();

    while ($product = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $product["_id"];
        $tmp["product_name"] = $product["product_name"];
        $tmp["name"] = $product["name"];
        $tmp["quantity"] = $product["quantity"];
        $tmp["date_added"] = $product["date_added"];
        array_push($response["products"], $tmp);
    }
    echoResponse(200, $response);
});

// transaction for seller side
$app->get('/request/:id', function($id) use ($app) {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT products.product_name, users.name, orders.* FROM orders LEFT JOIN products ON (orders.product_id = products._id) LEFT JOIN users ON (users._id = products.seller) WHERE products.seller = '$id'");

    $response["status"] = "success";
    $response["products"] = array();

    while ($product = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $product["_id"];
        $tmp["product_name"] = $product["product_name"];
        $tmp["name"] = $product["name"];
        $tmp["quantity"] = $product["quantity"];
        $tmp["date_added"] = $product["date_added"];
        array_push($response["products"], $tmp);
    }
    echoResponse(200, $response);
});




?>