<?php



$app->post('/allReport', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('from', 'to'),$r);
    $db = new DbHandler();
    $from = $r->from;
    $to = $r->to;
    
    
    $resp = $db->getAllRecords("SELECT suppliers.name_of_supplier as sup_name, category.category_name as cat_name ,products.* FROM products LEFT JOIN category ON (products.category_id = category._id) LEFT JOIN suppliers ON(products.usual_supplier_id = suppliers._id) WHERE (products.date_added >= '$from') and (products.date_added <= '$to')");

    $response["status"] = "success";
    $response["products"] = array();

    while ($product = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $product["_id"];
        $tmp["name_of_product"] = $product["name_of_product"];
        $tmp["cat_name"] = $product["cat_name"];
        $tmp["sup_name"] = $product["sup_name"];
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
$app->post('/singleReport', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('id','from', 'to'),$r);
    $db = new DbHandler();
    $id = $r->id;
    $from = $r->from;
    $to = $r->to;
    
    
    $resp = $db->getAllRecords("SELECT product_request.*, products.name_of_product, products.description FROM product_request LEFT JOIN products ON (product_request.product_id = products._id) WHERE product_request.product_id = '$id' and (product_request.date_added >= '$from') and (product_request.date_added <= '$to')");

    $response["status"] = "success";
    $response["sales"] = array();

    while ($sale = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp['sales_id'] = $sale['_id'];
        $tmp['product_id'] = $sale['product_id'];
        $tmp['name_of_product'] = $sale['name_of_product'];
        $tmp['description'] = $sale['description'];
        $tmp['total_price'] = $sale['total_price'];
        $tmp['total_discount'] = $sale['total_discount'];
        $tmp['quantity'] = $sale['quantity'];
        $tmp['date_added'] = $sale['date_added'];
        array_push($response["sales"], $tmp);
    }
    echoResponse(200, $response);
    });
$app->post('/stockReport', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('from', 'to'),$r);
    $db = new DbHandler();
    $from = $r->from;
    $to = $r->to;
    
    
    $resp = $db->getAllRecords("SELECT stock.*, products.name_of_product, suppliers.name_of_supplier FROM stock LEFT JOIN products ON (stock.product_id = products._id) LEFT JOIN suppliers ON (stock.supplier_id = suppliers._id) WHERE  (stock.date_added >= '$from') and (stock.date_added <= '$to')");

    $response["status"] = "success";
    $response["stocks"] = array();

    while ($stock = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["product_id"] = $stock["product_id"];
        $tmp["name_of_product"] = $stock["name_of_product"];
        $tmp["name_of_supplier"] = $stock["name_of_supplier"];
        $tmp["supplier_id"] = $stock["supplier_id"];
        $tmp["note"] = $stock["note"];
        $tmp["quantity"] = $stock["quantity"];
        $tmp["stock_price"] = $stock["stock_price"];
        $tmp["selling_price"] = $stock["selling_price"];
        $tmp["product_change"] = $stock["product_change"];
        $tmp["date_stocked"] = $stock["date_stocked"];
        $tmp["stock_expiry_date"] = $stock["stock_expiry_date"];
        $tmp["date_added"] = $stock["date_added"];
        $tmp["date_modified"] = $stock["date_modified"];
        array_push($response["stocks"], $tmp);
    }
    echoResponse(200, $response);
    });
$app->post('/orderReport', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('from', 'to'),$r);
    $db = new DbHandler();
    $from = $r->from;
    $to = $r->to;
    
    
    $resp = $db->getAllRecords("SELECT orders.*, products.name_of_product, suppliers.name_of_supplier FROM orders LEFT JOIN products ON (orders.product_id = products._id) LEFT JOIN suppliers ON (orders.supplier_id = suppliers._id) WHERE  (orders.date_added >= '$from') and (orders.date_added <= '$to')");

    $response["status"] = "success";
    $response["orders"] = array();

    while ($order = $resp->fetch_assoc()) {
        $tmp = array();
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
$app->post('/rp', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('from', 'to'),$r);
    $db = new DbHandler();
    $from = $r->from;
    $to = $r->to;
    
    
    $resp = $db->getAllRecords("SELECT product_request.*, products.name_of_product, products.description FROM product_request LEFT JOIN products ON (product_request.product_id = products._id) WHERE (product_request.date_added >= '$from') and (product_request.date_added <= '$to')");

    $response["status"] = "success";
    $response["sales"] = array();

    while ($sale = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp['sales_id'] = $sale['_id'];
        $tmp['product_id'] = $sale['product_id'];
        $tmp['name_of_product'] = $sale['name_of_product'];
        $tmp['description'] = $sale['description'];
        $tmp['total_price'] = $sale['total_price'];
        $tmp['total_discount'] = $sale['total_discount'];
        $tmp['quantity'] = $sale['quantity'];
        $tmp['date_added'] = $sale['date_added'];
        array_push($response["sales"], $tmp);
    }
    echoResponse(200, $response);
    });