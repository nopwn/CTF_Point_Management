<?php
// Initialize the session
session_start();
 
require_once "config.php";


// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$points = $_SESSION['points'];

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if flag is empty
    if(empty(trim($_POST["flag"]))){
        $flag_err = "Please enter a flag.";
    } else{
        $flag = trim($_POST["flag"]);
    }
    
    // Validate flag
    if(empty($flag_err)){
        // Prepare a select statement
        $sql = "SELECT flag, point_value FROM flags WHERE flag = ?"; // Flag search SQL statement
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_flag);
            
            // Set parameters
            $param_flag = $flag;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if flag exists, if yes then verify if user has done flag before
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $flag, $point_value);
                    if(mysqli_stmt_fetch($stmt)){
                        $verify = "SELECT user_name, flag from doubleVerify where user_name = '$_SESSION[username]' AND flag = '$flag'"; // Verify sql statement
                        if($verifyExe = mysqli_prepare($link, $verify)){ 
                            if (mysqli_stmt_execute($verifyExe)){
                                mysqli_stmt_store_result($verifyExe);
                                if(mysqli_stmt_num_rows($verifyExe) == 0){ // Checks to see if flag has not been done before by checking if rows = 0
                                    $_SESSION["points"] = $points + $point_value; //Adds points to session so it updates
                                    $updatePoints = "UPDATE users SET points = $_SESSION[points] WHERE username = '$_SESSION[username]'"; // SQL update points
                                    if($ftpt = mysqli_prepare($link, $updatePoints)){
                                        if(mysqli_stmt_execute($ftpt)){
                                                $updateVerify = "INSERT INTO `doubleVerify` (`id`, `user_name`, `flag`, `done_at`) VALUES (NULL, '$_SESSION[username]', '$flag', current_timestamp());"; // Updates verify table
                                                if ($verifyUpdate = mysqli_prepare($link, $updateVerify)){
                                                    if (mysqli_stmt_execute($verifyUpdate)){
                                                        echo '<script type="text/JavaScript"> 
                                                            alert("Flag Accepted");
                                                            </script>';
                                                    }
                                                    mysqli_stmt_close($verifyUpdate);
                                                }
                                        }
                                        mysqli_stmt_close($ftpt);
                                    }
                                }else {
                                echo '<script type="text/JavaScript"> 
                                    alert("Duplicate flag");
                                    </script>';
                                }                   

                            }
                            mysqli_stmt_close($verifyExe);
                        }


                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $flag_err = "Flag name does not exist.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <h1 class="my-5"><b><?php echo htmlspecialchars($_SESSION["username"]); ?></b> You have <b><?php echo $_SESSION["points"]; ?> Point(s)</b>.</h1>
    <div class="wrapper">
        <h2>Search Flag</h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Flag Name</label>
                <input type="text" name="flag" class="form-control <?php echo (!empty($flag_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $flag; ?>">
                <span class="invalid-feedback"><?php echo $flag_err; ?></span>
            </div>    
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Search">
                <a href="logout.php" class="btn btn-danger ml-3">Sign Out</a>
            </div>
        </form>
    </div> 


    <iframe width="500px" height="500px" src="/leaderboard.php" frameborder="0">

</body>
</html>