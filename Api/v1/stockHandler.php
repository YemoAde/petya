<?php
$app->post('/newStock', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('product_id', 'supplier_id', 'note', 'quantity', 'stock_price', 'selling_price', 'product_change', 'date_stocked', 'stock_expiry_date'),$r);
    $db = new DbHandler();
    $product_id = $r->product_id;
    $supplier_id = $r->supplier_id;
    $note = $r->note;
    $quantity = $r->quantity;
    $stock_price = $r->stock_price;
    $selling_price = $r->selling_price;
    $product_change =$r->product_change;
    $date_stocked =$r->date_stocked;
    
    $isWithinRange = $db->maximumTest($product_id, $quantity);
    
    if($isWithinRange){
        $table_name = "stock";
        $coloum_names = array('product_id', 'supplier_id', 'note', 'quantity', 'stock_price', 'selling_price', 'product_change', 'date_stocked', 'stock_expiry_date');
        $result = $db->InsertIntoTable($r, $coloum_names, $table_name);
        $res = $db->forQue("UPDATE products SET quantity_in_stock = quantity_in_stock + $quantity WHERE products._id = '$product_id';");
        
        if($result != null && $res != null){
            if($product_change){
                $condition = array('_id'=>$product_id);
                $temp = array();
                $temp['selling_price'] = $selling_price;
                $temp['purchase_price'] = $stock_price;
                $res = $db->UpdateTable($temp, 'products', $condition);
                if($res != null){
                    $response["status"] = "success";
                    $response["message"] = "Stocking Complete and Product Price Updated";
                    echoResponse(200, $response);
                }
            }
            else{
                $response["status"] = "success";
                $response["message"] = "Stocking Complete, No Product Price Update";
                echoResponse(200, $response);
            }
        }
        else {
            $response["status"] = "error";
            $respnse["message"] = "Error Trying to Add Product Try Again";
            echoResponse(201, $response);
        }
    }    
    else{
            $response["status"] = "error";
            $response["message"] = "Stocking the Quantity of $quantity Will Exceed Maximum Quantity Limit For this Product";
            echoResponse(201, $response);        
    }
    });
$app->get('/stockHistory', function() {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords(" SELECT stock.*, products.name_of_product, suppliers.name_of_supplier FROM stock LEFT JOIN products ON (stock.product_id = products._id) LEFT JOIN suppliers ON (stock.supplier_id = suppliers._id) WHERE 1");

    $response["status"] = "success";
    $response["stocks"] = array();

    while ($stock = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $stock["_id"];
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
