<?php

    function sendResetMail($email,$username){

    //Variable
    $sender = "Dove <support@replug.net>";
    $to_email = $email;
    $subject = "Password Reset";
    //build message body
    $msg .= "Hello ".$username.",\n";
    $msg .="You requested for a password reset\n";
    $msg .="Please use this code: ".$code. " to reset your password.\n\n";
    $msg .="Kind Regards\n";
    $msg .="Replug!";

    //headers
    $headers = 'From: '.$sender.'' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    //sending block
    $send_mail = mail($to_email, $subject, $msg, $headers);

    if($send_mail){
      return true;
    }else {
      return false;
    }

}

function sendBorrowBook($email,$username,$Book,$Date,$id){
    
    $sender = "Dove <support@replug.net>";
    $to_email  = $email;
    $subject = "Unreturned Book";
    
    // message body
    
    $msg .= "Hello ".$username.".\n";
    $msg .= "A book was borrowed by you ".$Book.".\n";
    $msg .= "Having reference number".$_id."\n";
    $msg .= "Please you are requested to return the on".$Date."\n";
    $msg .= "Kind Regards\n";
    $msg .= "Replug!";
    
    //headers
    $headers = 'from: '.$sender.'' . "\r\n" .
        'X-Mailer: PHP/'.phpversion();
    
    $send_mail = mail($to_email, $subject, $msg, $headers);
    
    if($send_mail){
        return true;
    }else {
        return false;
    }
    
}

function OnDueDate($email,$usename,$Date,$Book,$id){
    $sender = "Dove <support@replug.net>";
    $to_email = $email;
    $subject = "Book  Due For Return";
    
    // message body
    
    $msg .= "Hello" .$username.".\n";
    $msg .= "The book you borrowed".$Book."is due for return today".$Date."\n";
    $msg .= "Thanks for your cooperation from WORD STUDY LIBRARY \n";
    $msg .= "REplug!";
}
?>