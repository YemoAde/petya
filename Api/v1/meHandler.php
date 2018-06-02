<?php
$app->get('/viewMe', function() {
    $db = new DbHandler();
    $response = array();
    $resp = $db->getAllRecords("SELECT * FROM book WHERE 1");

    $response["status"] = "success";
    $response["books"] = array();

    while ($book = $resp->fetch_assoc()) {
        $tmp = array();
        $tmp["title"] = $book["title"];
        $tmp["author"] = $book["author"];
        $tmp["quantity"] = $book["quantity"];
        $tmp["publisher"] = $book["publisher"];
        $tmp["price"] = $book["price"];
        $tmp["inventory_no"] = $book["inventory_no"];
        $tmp["date_added"] =$book["date_added"];
        array_push($response["books"], $tmp);
    }
    echoResponse(200, $response);
});

$app->post('/addMe', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('title', 'author', 'quantity', 'publisher', 'price', 'inventory_no', 'date_added'),$r);
    $db = new DbHandler();
    $title = $r->title;
    $author = $r->author;
    $quantity = $r->quantity;
    $publisher = $r->publisher;
    $price = $r->price;
    $inventory_no = $r->inventory_no;
    $date_added =$r->date_added;
    
    $isBookExist = $db->getOneRecord("SELECT 1 from book WHERE title='$title' or inventory_no='$inventory_no'");
    if(!$isBookExist){
        $table_name = "book";
        $coloum_names = array('title', 'author', 'quantity', 'publisher', 'price', 'inventory_no', 'date_added');
        $result = $db->InsertIntoTable($r, $coloum_names, $table_name);
        if($result != null){
            $response["status"] = "success";
            $response["message"] = "Book Added";
            echoResponse(200, $response);
        }
        else {
            $response["status"] = "error";
            $respnse["message"] = "Error Trying to Add Book TryAgain";
            echoResponse(201, $response);
        }}
        else{
            $response["status"] = "error";
            $response["message"] = "Book Exist Already";
            echoResponse(201, $response);        
        }
    });


?>