<?php
require_once "pdo.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Ahmed Abdelhamid - 7530b57b</title>
</head>
<body>
<div class="container">
<h1>Chuck Severance's Resume Registry</h1>
<?php
    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
    if ( isset($_SESSION['success']) ) {
        echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['name'])){
        echo '<a href="logout.php">Logout</a>';
    }else{
        echo '<a href="login.php">Please log in</a>';
    }

    echo('<table border="1">'."\n");
    $stmt = $pdo->query("SELECT profile_id, user_id, first_name, last_name, email, headline, summary FROM Profile");
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
        echo "<tr><td>";
        echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a>');
        echo("</td><td>");
        echo(htmlentities($row['headline']));
        echo("</td><td>");
        if (isset($_SESSION['name'])){
            echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> /');
            echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
            echo("</td></tr>\n");
        }
    }
    echo '</table>';
    if (isset($_SESSION['name'])) echo '<p><a href="add.php">Add New Entry</a></p>';
    ?>
    <p><b>Note:</b> Your implementation should retain data across multiple logout/login sessions. This sample implementation clears all its data periodically - which you should not do in your implementation.</p>
</div>
</body>