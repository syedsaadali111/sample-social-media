<?php
    session_start();
    require './db.php';
    //var_dump(time());
    
    if(!isset($_SESSION["user"])) {
        header("Location: signin.php");
        exit;
    }
    
    extract($_SESSION["user"]);
    //var_dump($_SESSION["user"]);
    $error = ""; 
     
     
    if(isset($_POST["postImgbtn"])){
        extract($_POST);
        //var_dump($_FILES);
        $content=$_FILES["content"]["name"];
        $extension = strtolower( pathinfo($content, PATHINFO_EXTENSION) ) ;
        $whitelist = array( "jpg", "png") ;
        if (!in_array($extension, $whitelist)){
            $error = "Wrong file format!" ;
        }else{
            $filename = uniqid() . "_" . "$content";        
            move_uploaded_file($_FILES["content"]["tmp_name"], "posts/$filename");
            
            $sql = "INSERT into posts(user_id,content,post_type) values(?,?,?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id,$filename,"image"]) ;
        }
        
    }
    
    if(isset($_POST["postTextbtn"])){
        extract($_POST);
        var_dump($_FILES);
        $content=filter_var($content,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (strlen($content)==0){
            $error = "Empty Post!" ;
        }elseif(strlen($content)>=255){
            $error = "Text post is too long!";
        }else{                        
            $sql = "INSERT into posts(user_id,content,post_type) values(?,?,?)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$id,$content,"text"]) ;
        }
    }
    
    
    //DISPLAY POSTS--------------
    
    $sql = "select friend_id from friend where user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    $friends = $stmt->fetchAll(PDO::FETCH_COLUMN);
    //var_dump($friends);
    
    $sql = "select * from posts";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);    
    //var_dump($_GET);
    
        
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Main Page</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--	<link rel="stylesheet" type="text/css" href="css/main.css">-->
        <link href="css/mainCss.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	
	<div class="limiter">
		
            <div id="contFeed">
                <header>
                    <div id="profileDiv"><img id="profilepic" src="uploaded/<?=$profile?>"> <a href="profile.php" id="name"><?= $name . " " . $surname?></a>  <a id="logout" href="logout.php">Log out</a></div>
                     
                </header>
                <div id="addPost">
                    <button id="btnTxt" onClick="">Text</button>
                    <button id="btnImg" onClick="">Image</button>
                    <form method="post" action="" enctype="multipart/form-data">
                        <div id="newPost">
                            <p class='error'><?=$error?></p>
                        </div>
                    </form>
                </div>
                    
                <div id="posts">
                    <?php
                    //var_dump($posts);
                    foreach($posts as $p){
                        
                        $comError = "";
    
//                        if(isset($_GET["commentbtn"])){
////                           var_dump($comError);
//                           if($_GET["comment"]===""){ 
//                               $comError = "Empty Comment!";
//                           }else{
//                               $sql = "";
//                           }
//                        }
                        if(in_array($p["user_id"],$friends)){    
                            echo '<div class="post">';
                            
                            $sql = "select * from user where id = ?";
                            $stmt = $db->prepare($sql);
                            $stmt->execute([$p["user_id"]]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            echo "<img class='postphoto' src='uploaded/{$user["profile"]}'>".$user["name"]." ".$user["surname"]."<br>";
                            
                            if($p["post_type"] === "image") {
                                echo "<img class='postedImg' src='posts/{$p["content"]}'>";
                            }
                            else {
                                echo "<p class='postedText'>{$p["content"]}</p>";
                            }
                            
                            //comments
                            
                            echo "<div class='comments' id='com_{$p["post_id"]}'>";
                            
                            $sql = "select * from comments where post_id = ? ";
                            $stmt = $db->prepare($sql);
                            $stmt->execute([$p["post_id"]]);
                            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);    
                            //var_dump($comments);
                            
                            foreach ($comments as $c) {
                                
                                $sql = "select * from user where id = ? ";
                                $stmt = $db->prepare($sql);
                                $stmt->execute([$c["user_id"]]);
                                $c_user = $stmt->fetch(PDO::FETCH_ASSOC);
                                
                                echo "<img class='postphoto' src='uploaded/{$c_user["profile"]}'>".$c_user["name"]." ".$c_user["surname"];
                                echo "<p>{$c["comment"]}</p>";
                            }
                            
                            echo "</div>";
                            echo "<input class='comment' type='text' placeholder='New comment....'><input id='{$p["post_id"]}' type='button' class='commentbtn' value='Comment' name='commentbtn'><br><p style='color:red'>$comError</p>";
                            
                            //likes
                            
                            $sql = "select * from likes where post_id = ? and user_id = ?";
                            $stmt = $db->prepare($sql);
                            $stmt->execute([$p["post_id"], $id]);
                            $likes = $stmt->fetch(PDO::FETCH_ASSOC);  
                            
                            
                            echo "<div data-id='{$p["post_id"]}' class='likes'>";
                            if(empty($likes)){
                                echo "<img id='like_{$p["post_id"]}' src='images/like.png'>";
                            }else{
                                echo "<img id='like_{$p["post_id"]}' src='images/unlike.png'>";
                            }
                            echo "</div>";
                            
                            
                            echo '</div>';                            
                        }
                    }
                    
                    ?>
                    
                </div>
            </div>
	</div>
	

	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<script src="js/main.js"></script>
        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script>
            
            $(function() {
                $.ajaxSetup({cache: false});
                
                $(".commentbtn").click(function(event){
                    console.log("aefsef");
                    var post_id = event.target.id;
                    var comment = $(this).prev().val();
                    console.log(comment);
                    $.post( "comment.php", { post_id : post_id , comment : comment });
//                            .done(function(data) {
////                                  return data;
//                                
//                            });                           
                            
                    $("#com_"+post_id).append("<img class='postphoto' src='uploaded/<?=$profile?>'>"+"<?=$name?>"+" "+"<?=$surname?>" + "<p>"+comment+"</p>");
                });
                
                $(".likes").click(function(event){
                    var post_id = $(this).attr("data-id");
                    var liked = 0;
                    //alert("done");
                    if($("#like_"+post_id).attr("src") === "images/unlike.png"){
                        console.log("true");
                        liked = 1;
                    }
                    
                    $.post( "like.php", { post_id : post_id , user_id : <?=$id?> , liked: liked });
                            
                                if($("#like_"+post_id).attr("src") === "images/like.png"){
                                    $("#like_"+post_id).attr("src","images/unlike.png");
                                }else{
                                    $("#like_"+post_id).attr("src","images/like.png");
                            
                                }
                                    
                                              
                    
                });
                                
                $("#btnTxt").click(function(){
                    console.log("Test");
                    $("#newPost").html("<input name='content' type='text'><br><button type='submit' name='postTextbtn'>Post</button>");
                });
                
                $("#btnImg").click(function() {
                    $("#newPost").html("<input name='content' type='file'><br><input type='submit' name='postImgbtn' value='Post'>");
                });
                
            });
            
//            function text() {
//                document.getElementById("newPost").innerHTML = "<input name='content' type='text'><br><button type='submit' name='postTextbtn'>Post</button>";
//            }
//            function img() {
//                document.getElementById("newPost").innerHTML = "<input name='content' type='file'><br><input type='submit' name='postImgbtn' value='Post'>";
//            }
            
            
        </script>
</body>
</html>