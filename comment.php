<?php
require './db.php';
header("Content-Type: application/json") ;
session_start();

extract($_SESSION["user"]);

$sql = "insert into comments(post_id, user_id, comment) values(?, ?, ?)";
$stmt = $db->prepare($sql);
$stmt->execute([$_POST["post_id"],$id,$_POST["comment"]]);

$result = "Success";

//$result = array("comment" =>"{$_POST["comment"]}");

echo json_encode($result);


//echo($_POST);
