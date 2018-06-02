<?php
// get ID pf the all transactions a product shows in 
function getProductTIDSet($itm, $arr){
	// $result = json_decode($arr, true);
 	$output = array();
	foreach($arr as $obj) {
		if(in_array($itm, $obj['items'])){
			array_push($output, $obj['t_id']);
		}
	}
	return $output;
}

// gets ID of all products that appear in sales list 
function getAllProductID(){
 	$db = new DbHandler();
	$res = $db->getAllRecords("SELECT DISTINCT product_id FROM sales");
	$products = array();
	while ($product = $res->fetch_assoc()) {
        
        array_push($products, $product['product_id']);
    }
    return $products;
}

//create the 
function getItemSetTable($p_array, $transaction_array){
	$large_array = array();

	foreach ($p_array as $key => $value) {
		$tmp = array();
		$tmp['item_set'] = $value;

		$tmp['tid_set'] = getProductTIDSet($value, $transaction_array);


		array_push($large_array, $tmp);
	}
	return json_encode($large_array);
}

// executable  of the algorithm
$app->get('/algorithm', function(){
	// so help me God
	$db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT _id AS t_id FROM purchases WHERE 1");

    $response["status"] = "success";
    $response["transactions"] = array();

    while ($transaction = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["t_id"] = $transaction["t_id"];
        $t_id = $transaction["t_id"];

        $tmp['items'] = array();
        $res = $db->getAllRecords("SELECT product_id AS p_id FROM sales WHERE sales.purchase_id = '$t_id'");
        while ($sale = $res->fetch_assoc()) {
	        array_push($tmp['items'], $sale['p_id']);
	    }
        array_push($response["transactions"], $tmp);
    }
    print_r(getItemSetTable(getAllProductID(),$response['transactions']));
    // getProductsID();
    // echoResponse(200, $response);
});