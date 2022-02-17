<?php
$user = '';
$password = ''; 
$database = ''; 
$servername='';

$mysqli = new mysqli($servername, $user, 
                $password, $database);
  
// Checking for connections
if ($mysqli->connect_error) {
    die('Connect Error (' . 
    $mysqli->connect_errno . ') '. 
    $mysqli->connect_error);
}
  
// SQL query to select data from database
$sql = "SELECT * FROM `users` ORDER BY `users`.`points` DESC";
$result = $mysqli->query($sql);
$mysqli->close(); 
?>
<!DOCTYPE html>
<html lang="en">
  
<head>
    <meta charset="UTF-8">
    <title>GFG User Details</title>
    <!-- CSS FOR STYLING THE PAGE -->
    <style>
        table {
            margin: 0 auto;
            font-size: large;
            width: 100%;
            height: auto;
            border: 1px solid black;
        }
  
        h1 {
            text-align: center;
            color: #00000;
            font-size: xx-large;
            font-family: 'Gill Sans', 'Gill Sans MT', 
            ' Calibri', 'Trebuchet MS', 'sans-serif';
        }
  
        td {
            background-color: black;
            color:  #00ff41;
            border: 1px solid black;
        }
  
        th,
        td {
            font-weight: bold;
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
        
        th {
            background-color: lightgrey;
            color: black;
        }
  
        td {
            font-weight: lighter;
        }
    </style>
</head>
  
<body>
    <section>
        <h1>Leaderboard</h1>
        <!-- TABLE CONSTRUCTION-->
        <table>
            <tr>
                <th>User Name</th>
                <th>Points</th>
            </tr>
            <!-- PHP CODE TO FETCH DATA FROM ROWS-->
            <?php   // LOOP TILL END OF DATA 
                while($rows=$result->fetch_assoc())
                {
             ?>
            <tr>
                <!--FETCHING DATA FROM EACH 
                    ROW OF EVERY COLUMN-->
                <td><?php echo $rows['username'];?></td>
                <td><?php echo $rows['points'];?></td>
            </tr>
            <?php
                }
             ?>
        </table>
    </section>
    
    <p style = "text-align: center;"> Made by Noah Wilson and Ethan Koen </p>
</body>
  
</html>