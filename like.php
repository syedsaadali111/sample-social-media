<?php
require './db.php';
header("Content-Type: application/json") ;
session_start();

extract($_SESSION["user"]);

if($_POST["liked"]==0){
    $sql = "insert into likes values(?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$_POST["user_id"],$_POST["post_id"]]);
}else{
    $sql = "delete from likes where user_id = ? and post_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$_POST["user_id"],$_POST["post_id"]]);
}

$result = "Success";

//$result = array("comment" =>"{$_POST["comment"]}");

echo json_encode($result);


//echo($_POST);
