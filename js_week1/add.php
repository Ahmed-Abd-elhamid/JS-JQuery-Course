<?php
    session_start();
    if (isset($_SESSION['name'])){
        require_once "pdo.php";
    }else{
        die ("ACCESS DENIED");
    }

if ( isset($_POST['cancel'])){
    header("Location: index.php");
    return;
}

if ( isset($_POST['add'])){
    if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {

        if ( empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['headline']) || empty($_POST['summary']) ){
            $_SESSION['error'] = 'All fields are required';
            header("Location: add.php");
            return;
        }

        if ( strpos($_POST['email'],'@') === false ) {
            $_SESSION['error'] = 'Email address must contain @';
            header("Location: add.php");
            return;
        }


        try{
            $stmt = $pdo->prepare('INSERT INTO Profile
            (user_id, first_name, last_name, email, headline, summary)
            VALUES ( :uid, :fn, :ln, :em, :he, :su)');
        
            $stmt->execute(array(
                ':uid' => $_SESSION['user_id'],
                ':fn' => $_POST['first_name'],
                ':ln' => $_POST['last_name'],
                ':em' => $_POST['email'],
                ':he' => $_POST['headline'],
                ':su' => $_POST['summary'])
            );
            $_SESSION['success'] = 'Profile added';
            header("Location: index.php");
            return;
        }catch (Exception $ex){
            
        }

    }else{
        $_SESSION['error'] = 'All fields are required';
        header("Location: add.php");
        return;  
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
<?php 
    echo '<h1>Adding Profile for '.$_SESSION['name'].'</h1>';

    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
?>
<form method="post">
<p>First Name: <input type="text" name="first_name" size="60"></p>
<p>Last Name: <input type="text" name="last_name" size="60"></p>
<p>Email: <input type="text" name="email" size="30"></p>
<p>Headine: <br><input type="text" name="headline" size="80"></p>
<p>Summary: <br><textarea name="summary" rows="8" cols="80"></textarea></p>
<p>
<input type="submit" name="add" value="Add"/>
<input type="submit" name="cancel" value="Cancel"/>
</p>
</form>
</div>
</body>
</html>