<?php
    session_start();
    require "connectDatabase.php";
    // require "startSession.php";
    
    if (isset($_POST['userName']) && isset($_POST['passWord'])) {
        
        function validate($data){
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
         }
     
         $uname = validate($_POST['userName']);
         $pass = validate($_POST['passWord']);
     
         if (empty($uname)) {
             header("Location: loginPage.php?error=User Name is required");
             exit();
         }else if(empty($pass)){
             header("Location: loginPage.php?error=Password is required");
             exit();
         }else{
             $sql = "SELECT * FROM `User` WHERE userName = '$uname' AND userPass = '$pass'";
     
             $result = mysqli_query($conn, $sql);
     
             if (mysqli_num_rows($result) === 1) {
                 $row = mysqli_fetch_assoc($result);

                 if ($row['userName'] === $uname && $row['userPass'] === $pass) {
                     $_SESSION['userName'] = $row['userName'];
                     $_SESSION['userId'] = $row['userId'];
                     
                     header("Location: dashboardPage.php");
                     exit();
                 }else{
                     header("Location: loginPage.php?error=Incorect User name or password");
                     exit();
                 }
             }else{
                 header("Location: loginPage.php?error=Incorect User name or password");
                 exit();
             }
         }
    }else {
        header("Location: loginPage.php");
	    exit();
    }
?>
