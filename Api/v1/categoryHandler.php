<?php
$app->post('/addCat', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('category_name', 'category_description'),$r);
    $db = new DbHandler();
    $category_name = $r->category_name;
    $category_description = $r->category_description;
    
    $isproductExist = $db->getOneRecord("SELECT 1 from category WHERE category_name='$category_name'");
    if(!$isproductExist){
        $table_name = "category";
        $coloum_names = array('category_name', 'category_description');
        $result = $db->InsertIntoTable($r, $coloum_names, $table_name);
        if($result != null){
            $response["status"] = "success";
            $response["message"] = "Category Added";
            echoResponse(200, $response);
        }
        else {
            $response["status"] = "error";
            $respnse["message"] = "Error Trying to Add Category Try Again";
            echoResponse(201, $response);
        }}
        else{
            $response["status"] = "error";
            $response["message"] = "Category Already Exist";
            echoResponse(201, $response);        
        }
    });
$app->get('/viewCat', function() {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT category.*, count(products.name_of_product) as number_of_products ,products.name_of_product FROM category LEFT JOIN products ON (category._id = products.category_id) GROUP BY category._id");

    $response["status"] = "success";
    $response["categories"] = array();

    while ($category = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $category["_id"];
        $tmp["category_name"] = $category["category_name"];
        $tmp["category_description"] = $category["category_description"];
        $tmp["number_of_products"] = $category["number_of_products"];
        $tmp["date_added"] = $category["date_added"];
        $tmp["date_modified"] = $category["date_modified"];
        array_push($response["categories"], $tmp);
    }
    echoResponse(200, $response);
});
$app->post('/editCat/:id', function($id) use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    verifyRequiredParams(array('category_name', 'category_description'),$r);
    $db = new DbHandler();
    $category_name = $r->category_name;
    $category_description = $r->category_description;
    
    $table_name ="category";
    $coloum_names = array('category_name', 'category_description');
    $result = $db->UpdateTable($r, $table_name, $condition);
    if($result != null) {
        $response["status"] = "success";
        $response["message"] = "Category Updated";
        echoResponse(200, $response);
    }
    else{
        $response["status"] = "error";
        $response["message"] = "Category Update Failed";
        echoResponse(201, $response);
    }
});
$app->get('/deleteCat/:id', function($id) use($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    $db = new DbHandler();
    $table_name = "category";
    $isproductExist = $db->getOneRecord("SELECT 1 from products WHERE products.category_id='$id'");
    if(!$isproductExist){
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
    }
    else {
            $response["status"] = "error";
            $response["message"] = "Cannot Delete. There are Products in this Category. Please move them to continue";
            echoResponse(201, $response);
        }
    
});