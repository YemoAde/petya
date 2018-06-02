<?php
////get Number of products, cat, suppliers
$app->get('/getCount', function(){
	$db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT (SELECT COUNT(*) FROM products WHERE 1) as product_count, (SELECT COUNT(*) FROM category WHERE 1) as category_count, (SELECT COUNT(*) FROM suppliers WHERE 1) as supplier_count, (SELECT COUNT(*) FROM users WHERE 1) as user_count");

    $response["status"] = "success";
    $response["counts"] = array();

    while ($count = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["product_count"] = $count["product_count"];
        $tmp["category_count"] = $count["category_count"];
        $tmp["supplier_count"] = $count["supplier_count"];
        $tmp["user_count"] = $count["user_count"];
        array_push($response["counts"], $tmp);
    }
    echoResponse(200, $response);
});

$app->get('/oneProduct/:id', function($id){
	$db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT category.category_name as cat_name ,products.* FROM products LEFT JOIN category ON (products.category_id = category._id) WHERE products._id = '$id'");

    $response["status"] = "success";
    $response["products"] = array();

    while ($product = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $product["_id"];
        $tmp["name_of_product"] = $product["name_of_product"];
        $tmp["cat_name"] = $product["cat_name"];
        $tmp["category_id"] = $product["category_id"];
        $tmp["usual_supplier_id"] = $product["usual_supplier_id"];
        $tmp["description"] = $product["description"];
        $tmp["company_of_manufacture"] = $product["company_of_manufacture"];
        $tmp["quantity_in_stock"] = $product["quantity_in_stock"];
        $tmp["max_quantity"] = $product["max_quantity"];
        $tmp["min_quantity"] =$product["min_quantity"];
        $tmp["purchase_price"] =$product["purchase_price"];
        $tmp["selling_price"] =$product["selling_price"];
        $tmp["discount"] =$product["discount"];
        $tmp["date_added"] = $product["date_added"];
        $tmp["date_modified"] = $product["date_modified"];
        array_push($response["products"], $tmp);
    }
    echoResponse(200, $response);
});
$app->get('/dailyExpiry', function(){
	$db = new DbHandler();
    $response = array();
    $response["status"] = "success";
    $response["products"] = array();

    $resp = $db->getAllRecords("SELECT products.*, stock.stock_expiry_date, stock.date_added as register_date FROM stock LEFT JOIN products ON(stock.product_id = products._id) WHERE stock.stock_expiry_date = CURDATE()");
    while ($product = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $product["_id"];
        $tmp["name_of_product"] = $product["name_of_product"];
        $tmp["category_id"] = $product["category_id"];
        $tmp["usual_supplier_id"] = $product["usual_supplier_id"];
        $tmp["description"] = $product["description"];
        $tmp["company_of_manufacture"] = $product["company_of_manufacture"];
        $tmp["quantity_in_stock"] = $product["quantity_in_stock"];
        $tmp["max_quantity"] = $product["max_quantity"];
        $tmp["min_quantity"] =$product["min_quantity"];
        $tmp["purchase_price"] =$product["purchase_price"];
        $tmp["selling_price"] =$product["selling_price"];
        $tmp["discount"] =$product["discount"];
        $tmp["stock_expiry_date"] =$product["stock_expiry_date"];
        $tmp["register_date"] =$product["register_date"];
        $tmp["date_added"] = $product["date_added"];
        $tmp["date_modified"] = $product["date_modified"];
        array_push($response["products"], $tmp);
    }
    echoResponse(200, $response);
});

$app->get('/viewOrder', function(){
	$db = new DbHandler();
    $response = array();
    $response["status"] = "success";
    $response["orders"] = array();

    $resp = $db->getAllRecords("SELECT orders.*, products.name_of_product, suppliers.name_of_supplier FROM orders LEFT JOIN products ON (orders.product_id = products._id) LEFT JOIN suppliers ON (orders.supplier_id = suppliers._id) WHERE 1");
    while ($order = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $order["_id"];
        $tmp["name_of_product"] = $order["name_of_product"];
        $tmp["name_of_supplier"] = $order["name_of_supplier"];
        $tmp["product_id"] = $order["product_id"];
        $tmp["supplier_id"] = $order["supplier_id"];
        $tmp["quantity"] = $order["quantity"];
        $tmp["status"] = $order["status"];
        $tmp["date_added"] = $order["date_added"];
        $tmp["date_modified"] = $order["date_modified"];
        array_push($response["orders"], $tmp);
    }
    echoResponse(200, $response);
});

$app->get('/regulator', function(){
	$db = new DbHandler();
    $response = array();
    $response["status"] = "success";
    $response["data"] = array();

    $resp = $db->getAllRecords("SELECT * from products as p WHERE NOT EXISTS (
SELECT * FROM `orders` as r WHERE r.product_id = p._id AND r.status = 1) AND (p.quantity_in_stock <= p.min_quantity OR p.quantity_in_stock > p.max_quantity)");
    while ($product = $resp->fetch_assoc()) {
    	$tmp = array();
    	///check whether above threshold or below threshold 
    	if($product["quantity_in_stock"] >= $product["max_quantity"]){
    		//above
    		$tmp['status'] = "overstock";
    		$tmp['difference'] = $product["quantity_in_stock"]  - $product["max_quantity"];
    	}
    	else{
    		//below
    		$tmp['status'] = "understock";
    		$tmp['difference'] = $product["max_quantity"] - $product["quantity_in_stock"];
    	}
        $tmp["_id"] = $product["_id"];
        $tmp["name_of_product"] = $product["name_of_product"];
        $tmp["category_id"] = $product["category_id"];
        $tmp["usual_supplier_id"] = $product["usual_supplier_id"];
        $tmp["description"] = $product["description"];
        $tmp["quantity_in_stock"] = $product["quantity_in_stock"];
        $tmp["max_quantity"] = $product["max_quantity"];
        $tmp["min_quantity"] =$product["min_quantity"];
        $tmp["date_added"] = $product["date_added"];
        $tmp["date_modified"] = $product["date_modified"];
        array_push($response["data"], $tmp);
    }
    echoResponse(200, $response);
});



$app->post('/order', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('_id', 'usual_supplier_id', 'difference', 'quantity_in_stock', 'max_quantity', 'min_quantity', 'name_of_product', 'description'),$r);
    $db = new DbHandler();
    $product_id = $r->_id;
    $supplier_id = $r->usual_supplier_id;
    $quantity = $r->difference;

    $name_of_product = $r->name_of_product;
    $description = $r->description;


    $r->product_id = $r->_id;
    $r->supplier_id = $r->usual_supplier_id;
    $r->quantity = $r->difference;
    $r->status = 1;

    $resp = $db->getAllRecords("SELECT * from suppliers WHERE suppliers._id = '$r->usual_supplier_id'");
    $result = $resp->fetch_assoc();

    $to = $result['supplier_email'];
	$subject = "Product Order";

	$message = "
	<html>
		<head>
		<title>Our Inventory Order</title>
		</head>
		<body>
			<p>Please Kindly fulfill Order For</p>
			<table>
				<thead>
					<tr>
						<th>Product</th>
						<th>Quantity</th>
						<th>Quantity</th>
					</tr>
				</thead>
					<tbody>
					<tr>
						<td>$name_of_product</td>
						<td>$description</td>
						<td>$quantity</td>
					</tr>
				</tbody>
			</table>
			<p>This message was automated by our Inventory Manager. Please contact the store for any details</p>
		</body>
	</html>
	";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: <store@gmail.com>' . "\r\n";

    
    if(mail($to,$subject,$message,$headers)){
        $table_name = "orders";
        $coloum_names = array('product_id', 'supplier_id', 'quantity', 'status');
        $result = $db->InsertIntoTable($r, $coloum_names, $table_name);
        
        if($result != null){
            
                
                $response["status"] = "success";
                $response["message"] = "Done";
                echoResponse(200, $response);
        }
        else {
            $response["status"] = "error";
            $respnse["message"] = "Not Done";
            echoResponse(201, $response);
        }
    }    
    else{
            $response["status"] = "error";
            $response["message"] = "Something Went Wrong";
            echoResponse(201, $response);        
    }
    });

$app->post('/overstock', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    // $condition = array('_id'=>$id);
    verifyRequiredParams(array('product_id', 'new_quantity'),$r);
    $db = new DbHandler();
    $product_id = $r->product_id;
    $new_quantity = $r->new_quantity;
    
    $table_name ="products";
    $coloum_names = array('product_id', 'new_quantity');
    // $result = $db->UpdateTable($r, $coloum_names, $table_name);
    $res = $db->forQue("UPDATE products SET quantity_in_stock = $new_quantity WHERE products._id = '$product_id';");

    if($res != null) {
        $response["status"] = "success";
        $response["message"] = "OverStock Corrected";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Error";
        echoResponse(201, $response);
    }
});
$app->post('/assignRole', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    // $condition = array('_id'=>$id);
    verifyRequiredParams(array('user_id', 'new_role'),$r);
    $db = new DbHandler();
    $user_id = $r->user_id;
    $new_role = $r->new_role;
    
    $table_name ="users";
    // $coloum_names = array('user', 'new_quantity');
    // $result = $db->UpdateTable($r, $coloum_names, $table_name);
    $res = $db->forQue("UPDATE users SET role = $new_role WHERE _id = '$user_id';");

    if($res != null) {
        $response["status"] = "success";
        $response["message"] = "Done";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Done";
        echoResponse(201, $response);
    }
});

