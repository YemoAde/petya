<?php
$app->post('/addSupplier', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('name_of_supplier', 'supplier_details', 'supplier_email', 'supplier_phone', 'supplier_address'),$r);
    $db = new DbHandler();
    $name_of_supplier = $r->name_of_supplier;
    $supplier_details = $r->supplier_details;
    $supplier_email = $r->supplier_email;
    $supplier_phone = $r->supplier_phone;
    $supplier_address = $r->supplier_address;
    
    $issupplierExist = $db->getOneRecord("SELECT 1 from suppliers WHERE (name_of_supplier='$name_of_supplier' OR supplier_email='$supplier_email' OR supplier_phone='$supplier_phone')");
    if(!$issupplierExist){
        $table_name = "suppliers";
        $coloum_names = array('name_of_supplier', 'supplier_details', 'supplier_email', 'supplier_phone', 'supplier_address');
        $result = $db->InsertIntoTable($r, $coloum_names, $table_name);
        if($result != null){
            $response["status"] = "success";
            $response["message"] = "Supplier Added";
            echoResponse(200, $response);
        }
        else {
            $response["status"] = "error";
            $respnse["message"] = "Error Trying to Add Supplier Try Again";
            echoResponse(201, $response);
        }}
        else{
            $response["status"] = "error";
            $response["message"] = "Supplier Detail(s) Already Exist";
            echoResponse(201, $response);        
        }
    });
$app->get('/viewSupplier', function() {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT * FROM suppliers WHERE 1");

    $response["status"] = "success";
    $response["suppliers"] = array();

    while ($supplier = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $supplier["_id"];
        $tmp["name_of_supplier"] = $supplier["name_of_supplier"];
        $tmp["supplier_details"] = $supplier["supplier_details"];
        $tmp["supplier_email"] = $supplier["supplier_email"];
        $tmp["supplier_phone"] = $supplier["supplier_phone"];
        $tmp["supplier_address"] = $supplier["supplier_address"];
        $tmp["date_added"] = $supplier["date_added"];
        $tmp["date_modified"] = $supplier["date_modified"];
        array_push($response["suppliers"], $tmp);
    }
    echoResponse(200, $response);
});
$app->post('/editSupplier/:id', function($id) use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    verifyRequiredParams(array('name_of_supplier', 'supplier_details', 'supplier_email', 'supplier_phone', 'supplier_address'),$r);
    $db = new DbHandler();
    $name_of_supplier = $r->name_of_supplier;
    $supplier_details = $r->supplier_details;
    $supplier_email = $r->supplier_email;
    $supplier_phone = $r->supplier_phone;
    $supplier_address = $r->supplier_address;
   
    $table_name ="suppliers";
    $coloum_names = array('name_of_supplier', 'supplier_details', 'supplier_email', 'supplier_phone', 'supplier_address');
    $result = $db->UpdateTable($r, $table_name, $condition);
    if($result != null) {
        $response["status"] = "success";
        $response["message"] = "Supplier Updated";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Supplier Update Failed";
        echoResponse(201, $response);
    }
});
$app->delete('/deleteSupplier/:id', function($id) use($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    
    $db = new DbHandler();
    $table_name = "suppliers";
    $result = $db->deleteTable($table_name, $condition);
    if ($result != null) {
        $response["status"] = "Success";
        $response["message"] = "Delete Successful";
        echoResponse(200, $response);
    }
    else {
        $response["status"] = "error";
        $response["message"] = "Error Occured";
        echoResponse(201, $response);
    }
});