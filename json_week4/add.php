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
            if ( ! isset($_POST['edu_year'.$i]) ) continue;
            if ( ! isset($_POST['edu_school'.$i]) ) continue;

            $edu_year = $_POST['edu_year'.$i];
            $edu_school = $_POST['edu_school'.$i];
    
            if ( strlen($edu_year) == 0 || strlen($edu_school) == 0 ) {
                $_SESSION['error'] = "All fields are required";
                header("Location: add.php");
                return;
            }
    
            if ( ! is_numeric($edu_year) ) {
                $_SESSION['error'] = "Education year must be numeric";
                header("Location: add.php");
                return;
            }
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
              if ( ! isset($_POST['edu_year'.$i]) ) continue;
              if ( ! isset($_POST['edu_school'.$i]) ) continue;
            
              $edu_year = $_POST['edu_year'.$i];
              $edu_school = $_POST['edu_school'.$i];

              $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :prefix');
                $stmt->execute(array( ':prefix' => $edu_school ));
                $retval = array();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ( empty($row) ){
              $stmt = $pdo->prepare('INSERT INTO Institution (name)
              VALUES ( :edu_school)');
            
              $stmt->execute(array(
              ':edu_school' => $edu_school)
              );             

              $institution_id = $pdo->lastInsertId();
            }else{
              $institution_id = $row['institution_id'];
            }

              $stmt = $pdo->prepare('INSERT INTO Education
                (profile_id, rank, year, institution_id)
                VALUES ( :pid, :rank, :edu_year, :edu_school)');
            
              $stmt->execute(array(
              ':pid' => $profile_id,
              ':rank' => $rank,
              ':edu_year' => $edu_year,
              ':edu_school' => $institution_id)
              );
            
              $rank++;
            
            }

                                    
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
<title>Ahmed Abdelhamid - a9b3c24a</title>
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
<p>Education: <input type="button" value="+" onclick="addEdu();return false;"></p>
<div id="education_fields"></div>
<p>Position: <input type="button" value="+" onclick="addPos();return false;"></p>
<div id="position_fields"></div>
<input type="submit" name="add" value="Add"/>
<input type="submit" name="cancel" value="Cancel"/>
</p>
</form>
</div>
<script>
  var countPos = 0;
  var countEdu = 0;
  
function addPos() {
  const div = document.createElement('div');
  countPos ++;
  if ( countPos < 10 ){
    div.id = `position${countPos}`;
    div.innerHTML = `
    <p>Year: <input type="text" name="year${countPos}" value="">
    <input type="button" value="-" onclick="$('#position${countPos}').remove();return false;"></p>
    <textarea name="desc${countPos}" rows="8" cols="80"></textarea>
    `;

    document.getElementById('position_fields').appendChild(div);
  }else{
    alert("Maximum of nine position entries exceeded");
  }
}

function addEdu() {
    const div = document.createElement('div');
    countEdu ++;
    if ( countEdu < 10 ){
        div.id = `education${countEdu}`;
        div.innerHTML = `
        <p>Year: <input type="text" name="edu_year${countEdu}" value="">
        <input type="button" value="-" onclick="$('#education${countEdu}').remove();return false;"></p>
        <p>School: <input type="text" size="80" name="edu_school${countEdu}" class="school ui-autocomplete-input" value="" autocomplete="off"/></p>
        `;

        document.getElementById('education_fields').appendChild(div);
    }else{
        alert("Maximum of nine education entries exceeded");
    }

    $('.school').autocomplete({ source: "school.php" });
}

$('.school').autocomplete({ source: "school.php" });

</script>
</body>
</html>