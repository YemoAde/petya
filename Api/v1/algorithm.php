<?php

function is_exists($haystash, $sub){
	$val = true;
	foreach ($sub as $key => $value){
		if(!in_array($value, $haystash))
			$val = false;
	}
	return $val;
}
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

function getMultipleProductsTIDSet($itm_arr, $trans_arr){

	$output = array();
	foreach($trans_arr as $obj) {
		if(is_exists($obj['items'], $itm_arr)){
		    array_push($output, $obj['t_id']);
		}
	}
	return $output;
}

// gets ID of all products that appear in sales list 
function getAllProductID(){
 	$db = new DbHandler();
	$res = $db->getAllRecords("SELECT DISTINCT product_id FROM product_request");
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


function prediction($input, $transaction_array){
	$prediction_result = array();
	$temp_array = getAllProductID();
	// if (($key = array_search($input, $temp_array)) !== false) {
	//     unset($temp_array[$key]);
	// }
	$_temp_array = array_diff($temp_array, $input);
	
	foreach ($_temp_array as $key => $value){
		// $target = array($input, $value);
		$target = $input;
		array_push($target, $value);
		if(count(getMultipleProductsTIDSet($target, $transaction_array)) > 0 )
			array_push($prediction_result, $value);

	}
	return $prediction_result;
	
}

// executable  of the algorithm
$app->post('/algorithm', function() use ($app) {

	// verifyRequiredParams(array('total_price', 'total_quantity', 'data'),$r);
	$r = json_decode($app->request->getBody());
	$ids = $r->data;
	// so help me God
	$db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT _id AS t_id FROM total_request WHERE 1");

    $response["status"] = "success";
    $response['result'] = array();
    $response_transaction = array();

    while ($transaction = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["t_id"] = $transaction["t_id"];
        $t_id = $transaction["t_id"];

        $tmp['items'] = array();
        $res = $db->getAllRecords("SELECT product_id AS p_id FROM product_request WHERE product_request.purchase_id = '$t_id'");
        while ($sale = $res->fetch_assoc()) {
	        array_push($tmp['items'], $sale['p_id']);
	    }
        array_push($response_transaction, $tmp);
    }
    $result_ids = prediction($ids,$response_transaction);
    $result_ids = rtrim(implode(',', $result_ids),',');
    $resp = $db->getAllRecords("SELECT * FROM products WHERE _id IN (". $result_ids .")");
	while ($product = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["name_of_product"] = $product["name_of_product"];
        
        array_push($response['result'], $tmp);
    }
    // print_r(getItemSetTable(getAllProductID(),$response['transactions']));
    // $response['result'] = prediction($ids,$response_transaction);
    // getProductsID();
    echoResponse(200, $response);
});