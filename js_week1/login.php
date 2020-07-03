<?php // Do not put any HTML above this line
    require_once "pdo.php";
    session_start();

if ( isset($_POST['cacnel'])){
    header("Location: index.php");
    return;  
}

if ( isset($_POST['email']) && isset($_POST['pass']) ) {

    $salt = 'XyZzy12*_';
    $stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // php123

    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        // $_SESSION['error'] = "User name and password are required";
        // header("Location: login.php");
        // return;
    } else {
        $check = hash('md5', $salt.htmlentities($_POST['pass']));

        if ( $check == $stored_hash ) { 
            if (filter_var(htmlentities($_POST['email']), FILTER_VALIDATE_EMAIL)){
                $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
                $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ( $row !== false ) {
                    error_log("Login success ".$row['name']);
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['user_id'] = $row['user_id'];
                    header("Location: index.php");
                    return;
                }
            }else{
                error_log("Login fail ".$_POST['email']." $check");
                // $_SESSION['error'] = "Email must have an at-sign (@)";
                // header("Location: login.php");
                // return;
            }
        } else {
            $_SESSION['error'] = "Incorrect password";
            header("Location: login.php");
            return;
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Ahmed Abdelhamid - 68b7007a</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>
<form method="POST">
User Name <input type="text" name="email" id="id_1623"><br/>
Password <input type="password" name="pass" id="id_1723"><br/>
<p>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel"/>
</p>
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
</p>
</div>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        pw = document.getElementById('id_1723').value;
        mail = document.getElementById('id_1623').value;
        console.log("Validating pw= "+pw);
        console.log("Validating mail= "+mail);
        if (pw == null || pw == "" || mail == null || mail == "") {
            alert("Both fields must be filled out");
        } else if (mail != 'umsi@umich.edu'){
            alert("Invalid email address");
        }else{
            return true;
        }
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</body>
