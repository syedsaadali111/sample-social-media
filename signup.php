<?php

session_start();
require './db.php';
$proferror = "";
$error = "";
if(isset($_POST["btnSignup"])){
    
    extract($_POST);
    $name = filter_var($name,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $surname = filter_var($surname,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($email,FILTER_SANITIZE_EMAIL);
    $pass = password_hash($pass, PASSWORD_BCRYPT) ;
    $profile = $_FILES["profile"]["name"];
    
    //var_dump($_FILES);
    $extension = strtolower( pathinfo($profile, PATHINFO_EXTENSION) ) ;
    $whitelist = array( "jpg", "png") ;
    if (!in_array($extension, $whitelist)){
        $proferror = "Wrong format!" ;
    }
    else {
        $filename = uniqid() . "_" . "$profile";
        
        move_uploaded_file($_FILES["profile"]["tmp_name"], "uploaded/$filename");
        
        $sqlEmail = "Select count(*) as total from user where email = ?";
        $stmt = $db->prepare($sqlEmail);
        $stmt->execute([$email]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)["total"];
        
        if ($count == 0) {
            $sql = "INSERT into user(name,surname,email,bdate,profile, gender, password) values(?,?,?,?,?,?,?)";
            $stmt = $db->prepare($sql);
            $stmt->execute( [$name, $surname,$email,$bdate,$filename,$gender,$pass]) ;
            header("Location: signin.php?added=");
        } else {
            $error = "Email is used!";
        }
    }
    
}

?>
<html lang="en">
<head>
	<title>Sign Up</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100" style="background-image: url('images/bg-01.jpg');">
			<div class="wrap-login100">
				<form class="login100-form validate-form" method="post" enctype="multipart/form-data">
					<span class="login100-form-logo">
						<i class="zmdi zmdi-landscape"></i>
					</span>

					<span class="login100-form-title p-b-34 p-t-27">
						Sign Up
					</span>
                                        <div class="wrap-input100 validate-input" data-validate = "Enter First Name">
						<input class="input100" type="text" name="name" placeholder="First Name" value="<?= isset($name)?$name:"" ?>">
						<span class="focus-input100" data-placeholder="&#xf207;"></span>
					</div>
                                    
                                        <div class="wrap-input100 validate-input" data-validate = "Enter Surname">
						<input class="input100" type="text" name="surname" placeholder="Surname" value="<?= isset($surname)?$surname:"" ?>">
						<span class="focus-input100" data-placeholder="&#xf207;"></span>
					</div>
                                    
                                        <div class="wrap-input100 validate-input" data-validate = "Enter Birth Date" >
                                            <input class="input100" type="date" name="bdate" placeholder="Birth Date" value="<?= isset($bdate)?$bdate:"" ?>">
						<span class="focus-input100" data-placeholder="&#128197;"></span>
					</div>
                                    
                                        <div class="wrap-input100" >
                                            <p class="input100" style="text-align: center;padding-left: 0;">
                                                <input class="" type="radio" name="gender" value="F" <?= (isset($gender)&&$gender=="F")?"checked":"" ?>> Female &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input class="" type="radio" name="gender" value="M" <?= (isset($gender)&&$gender=="F")?"checked":"" ?>> Male             
                                                
                                            <!--<span class="focus-input100" data-placeholder="&#x26A5;"></span>-->
                                            </p>
					</div>
                                    
                                        <div class="wrap-input100 validate-input" data-validate = "Upload a Profile Photo">
						<input class="input100" type="file" name="profile" value="<?= isset($profile)?$profile:"" ?>">
                                                <p style="color:red;"><?=$proferror?></p>
					</div>
                                    
					<div class="wrap-input100 validate-input" data-validate = "Enter Email">
                                            
						<input class="input100" type="text" name="email" placeholder="Email" value="<?= isset($email)?$email:"" ?>">
						<span class="focus-input100" data-placeholder="&#xf207;"></span>
                                                <p style="color:red;"><?= $error ?></p>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<input class="input100" type="password" name="pass" placeholder="Password">
						<span class="focus-input100" data-placeholder="&#xf191;"></span>
					</div>

					<div class="container-login100-form-btn">
						<button class="login100-form-btn" name="btnSignup" >
							Sign Up
                                                </button>
                                        </div>
                                        <div class="text-center p-t-30">                                            
                                                <a class="txt1" href="signin.php">
							Back to Login
						</a>
                                            
					</div>

				</form>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<script src="js/main.js"></script>

</body>
</html>