<?php
$app->post('/addProduct', function() use ($app) {


        require_once 'passwordHash.php';
    $db = new DbHandler();
    $response = array();
    $product_name= $_POST['product_name'];
    $category_id= $_POST['category_id'];
    $seller= $_POST['seller'];
    $product_details= $_POST['product_details'];
    $quantity= $_POST['quantity'];
    $unit_of_measure= $_POST['unit_of_measure'];
    $product_price= $_POST['product_price'];
    $status= $_POST['status'];

    $target_dir = "images";

    // for form 
    $image_target_file = $target_dir ."/" . $product_name . '_' . $seller . "_" . date("d-m-y") ."_".  date("s"). "_" . basename($_FILES["image"]["name"]);


    $file_ext=strtolower(end(explode('.',$_FILES['image']['name'])));
    $expensions= array("jpeg","jpg","png");
    
    $isRecExist = $db->getOneRecord("SELECT 1 from products WHERE product_name='$product_name' and seller='$seller'");
    if(!$isRecExist){
        if(!in_array($file_ext, $expensions)=== false){

            if(move_uploaded_file($_FILES["image"]['tmp_name'], $image_target_file)){
                
            }else{
                $response["status"] = "error";
                $response["message"] = "Error";
                echoResponse(201, $response);
                exit();
            }
            $query = "INSERT INTO products (product_name, category_id, seller, product_details, quantity, product_price, unit_of_measure, status, product_image) VALUES('$product_name', '$category_id', '$seller', '$product_details', '$quantity', '$product_price', '$unit_of_measure', '$status','$image_target_file')";
            $well = $db->forQue($query);
            if($well == 1){
                $response["status"] = "success";
                $response['message'] = "Product Added ";
                echoResponse(200, $response);
            }else{
                $response["status"] = "error";
                $response["message"] = "Product was not Added";
                echoResponse(201, $response);
            }

        }
        else{
            $response["status"] = "error";
            $response["message"] = "Only jpg, jpeg and png files are supported";
            echoResponse(201, $response);
        }
    }else{
            $response["status"] = "error";
            $response["message"] = "You have Registered this Product";
            echoResponse(201, $response);
    }
    });
$app->get('/search/:keyword', function($keyword) use ($app) {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT category.category_name as cat_name, products.*, users.name FROM products LEFT JOIN category ON (category._id = products.category_id) LEFT JOIN users ON (users._id = products.seller) WHERE products.product_name LIKE '%$keyword%' or products.product_details LIKE '%$keyword%'");

    $response["status"] = "success";
    $response["products"] = array();

    while ($product = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $product["_id"];
        $tmp["product_name"] = $product["product_name"];
        $tmp["product_details"] = $product["product_details"];
        $tmp["product_price"] = $product["product_price"];
        $tmp["product_image"] = $product["product_image"];
        $tmp["cat_name"] = $product["cat_name"];
        $tmp["name"] = $product["name"];
        $tmp["date_added"] = $product["date_added"];
        $tmp["date_modified"] = $product["date_modified"];
        array_push($response["products"], $tmp);
    }
    echoResponse(200, $response);
});
$app->get('/viewProducts', function() use ($app) {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT category.category_name as cat_name, products.*, users.name FROM products LEFT JOIN category ON (category._id = products.category_id) LEFT JOIN users ON (users._id = products.seller) WHERE 1");

    $response["status"] = "success";
    $response["products"] = array();

    while ($product = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $product["_id"];
        $tmp["product_name"] = $product["product_name"];
        $tmp["product_details"] = $product["product_details"];
        $tmp["product_price"] = $product["product_price"];
        $tmp["product_image"] = $product["product_image"];
        $tmp["cat_name"] = $product["cat_name"];
        $tmp["name"] = $product["name"];

        $tmp["date_added"] = $product["date_added"];
        $tmp["date_modified"] = $product["date_modified"];
        array_push($response["products"], $tmp);
    }
    echoResponse(200, $response);
});
$app->get('/myProduct/:id', function($id) use ($app) {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT category.category_name as cat_name, products.*, users.name FROM category LEFT JOIN products ON (category._id = products.category_id) LEFT JOIN users ON (users._id = products.seller) WHERE products.seller = '$id'");

    $response["status"] = "success";
    $response["products"] = array();

    while ($product = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["_id"] = $product["_id"];
        $tmp["product_name"] = $product["product_name"];
        $tmp["product_details"] = $product["product_details"];
        $tmp["product_details"] = $product["product_details"];
        $tmp["cat_name"] = $product["cat_name"];
        $tmp["name"] = $product["name"];

        $tmp["date_added"] = $product["date_added"];
        $tmp["date_modified"] = $product["date_modified"];
        array_push($response["products"], $tmp);
    }
    echoResponse(200, $response);
});


$app->get('/deleteProduct/:id', function($id) use($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    $condition = array('_id'=>$id);
    
    $db = new DbHandler();
    $table_name = "products";
    $result = $db->deleteTable($table_name, $condition);
    if ($result != null) {
        $response["status"] = "success";
        $response["message"] = "Delete Successful";
        echoResponse(200, $response);
    }
    else {
        $response["status"] = "error";
        $response["message"] = "Error Occured";
        echoResponse(201, $response);
    }
});