<?php

$app->post('/checkout', function() use ($app) {
	$db = new DbHandler();
	$response = array();
	$r = json_decode($app->request->getBody());
	// verifyRequiredParams(array('total_price', 'total_quantity', 'data'),$r);
	$sales = $r->data;

	$table_name = "total_request";
	$coloum_names = array('total_price', 'total_quantity', 'checkout_discount');
    $result = $db->InsertIntoTable($r, $coloum_names, $table_name);
    if($result != null){
    	$transaction_id = $result;
    	foreach($sales as $sale){
			$sale->purchase_id = $transaction_id;
			$result = $db->InsertIntoTable($sale, array('purchase_id', 'product_id', 'quantity', 'total_price', 'total_discount'), 'product_request');
			$res = $db->forQue("UPDATE products SET quantity_in_stock = quantity_in_stock - $sale->quantity WHERE products._id = '$sale->product_id';");
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

$app->get('/salesHistory', function(){
	$db = new DbHandler();
    $response = array();
    $response["status"] = "success";
    $response["purchases"] = array();

    $resp = $db->getAllRecords("SELECT * FROM total_request WHERE 1");
    while ($purchase = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $purchase["_id"];
        $p_id = $purchase["_id"];
        $tmp["no_of_items"] = $purchase["total_quantity"];
        $tmp["total_price"] = $purchase["total_price"];
        $tmp["checkout_discount"] = $purchase["checkout_discount"];
        $tmp['sales'] = array();

        $res = $db->getAllRecords("SELECT product_request.*, products.name_of_product, products.description FROM product_request LEFT JOIN products ON (product_request.product_id = products._id) WHERE product_request.purchase_id = '$p_id' ");
        if($res != null){
            while($sale = $res->fetch_assoc()){
                $sTmp = array();
                $sTmp['sales_id'] = $sale['_id'];
                $sTmp['product_id'] = $sale['product_id'];
                $sTmp['name_of_product'] = $sale['name_of_product'];
                $sTmp['description'] = $sale['description'];
                $sTmp['total_price'] = $sale['total_price'];
                $sTmp['total_discount'] = $sale['total_discount'];
                $sTmp['quantity'] = $sale['quantity'];

                array_push($tmp["sales"], $sTmp);
            }
        }
        $tmp["date_added"] = $purchase["date_added"];
        $tmp["date_modified"] = $purchase["date_modified"];
        array_push($response["purchases"], $tmp);
    }
    echoResponse(200, $response);
});
