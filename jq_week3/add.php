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

        for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
    
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
    
            if ( strlen($year) == 0 || strlen($desc) == 0 ) {
                $_SESSION['error'] = "All fields are required";
                header("Location: add.php");
                return;
            }
    
            if ( ! is_numeric($year) ) {
                $_SESSION['error'] = "Position year must be numeric";
                header("Location: add.php");
                return;
            }
        }

        try{
            $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)');

            $stmt->execute(array(
              ':uid' => $_SESSION['user_id'],
              ':fn' => $_POST['first_name'],
              ':ln' => $_POST['last_name'],
              ':em' => $_POST['email'],
              ':he' => $_POST['headline'],
              ':su' => $_POST['summary'])
            );

            $profile_id = $pdo->lastInsertId();
                        
            $rank = 1;
            for($i=1; $i<=9; $i++) {
              if ( ! isset($_POST['year'.$i]) ) continue;
              if ( ! isset($_POST['desc'.$i]) ) continue;
            
              $year = $_POST['year'.$i];
              $desc = $_POST['desc'.$i];
              $stmt = $pdo->prepare('INSERT INTO Position
                (profile_id, rank, year, description)
                VALUES ( :pid, :rank, :year, :desc)');
            
              $stmt->execute(array(
              ':pid' => $profile_id,
              ':rank' => $rank,
              ':year' => $year,
              ':desc' => $desc)
              );
            
              $rank++;
            
            }

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
<title>Ahmed Abdelhamid - 7530b57b</title>
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
<p>Position: <input type="button" value="+" onclick="addRow();return false;"></p>
<div id="position_fields"></div>
<input type="submit" name="add" value="Add"/>
<input type="submit" name="cancel" value="Cancel"/>
</p>
</form>
</div>
<script>
  var count = 0;

function addRow() {
  const div = document.createElement('div');
  count ++;
  if ( count < 10 ){
    div.id = `position${count}`;
    div.innerHTML = `
    <p>Year: <input type="text" name="year${count}" value="">
    <input type="button" value="-" onclick="$('#position${count}').remove();return false;"></p>
    <textarea name="desc${count}" rows="8" cols="80"></textarea>
    `;

    document.getElementById('position_fields').appendChild(div);
  }else{
    alert("Maximum of nine position entries exceeded");
  }
}
</script>
</body>
</html>