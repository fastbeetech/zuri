<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <title>Validation and Database File system</title>
</head>
<body>
    <div class="container">

<?php

//call to action for created  function
create();

login();

reset_password();

logout();

newpass();


/*
This is the registration logic area and i used a function called create to create a profile database 
in json format for the registrating user the extract function is being used here to get the form data
of the request

file system was used in this code we create a database with the user name into a directory called database
and name the user database based on the username provided
*/

//registration logic
function create(){
    // CHECK FOR REGISTRATION BUTTON
if(isset($_POST['register'])){
    extract($_REQUEST);

    if(empty($username) && empty($password) && empty($email)) { 
        flash('danger', 'You need to fill in the all fields');
        exit;
     }

    $db_name = 'database/'.$username.'.json';
    $file = fopen($db_name, 'w');
    
    $user_arr[] = array();

    $form_data = array(
        "username" => test_input($username),
        "email" => test_input($email),
        "password" => password_hash(test_input($password), PASSWORD_DEFAULT),
    );

    
    $user = json_encode($form_data, JSON_PRETTY_PRINT);

    if(fwrite($file, $user)){
        session_start();
        $_SESSION['type'] = 'success';
        $_SESSION['message'] ='Registration Successful you can now loggin!';
        header('location: index.php?login&msg');
    }
    else{
        echo 'something is wrong';
    }
    
    fclose($file);
    
    }
}

/*
This is the login logic and a function login was created and all the logic to be executed are written in 
the function block similar form in registration logic was implored here

file system was used in this code to get the user credential form the database 
and then compare with what the user has filled in the login form. 
If they tally a session is created and some information are also stored in
the session variable and a redirection is made to the welcome page.
*/

// login logic
function login(){
    
   if(isset($_POST['login'])){
       extract($_REQUEST);

       if(empty($username) && empty($password)) { 
          flash('danger', 'You need to fill in the all fields');
           exit;
        }


        $get_user = file_get_contents('database/'.$username.'.json');

        $db = json_decode($get_user);

        $user = $db->username;
        $pass = $db->password;

        //check for username
        if(test_input($username) == $user){

            //password verification
            if(password_verify(test_input($password), $pass)){
                session_start();
                $_SESSION['username'] = test_input($username);
                $_SESSION['message'] = "Welcome ".$_SESSION['username']."!";
                $_SESSION['type'] = 'success';
                $_SESSION['message'] ='You are now logged-in!';
                header('location: index.php?welcome');
            }
            
            else{ 
                $_SESSION['type'] = 'danger';
                $_SESSION['message'] ='invalid password or username!';
                header('location: index.php?login');
                }
                
        }
        
        else{
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] ='Incorrect credentials!';
            header('location: index.php?login');
        }

    }
}

/*
In the logout logic we destroy the user session and keeps the user out
 and redirects the user back to homepage
to login agian to start a new session
*/

// Logout Logic
function logout(){
    if(isset($_GET['logout'])){
        session_start();

        // Destroy the session.
        if( session_destroy()){
            // Redirect to Signin form
            session_start();
            $_SESSION['message'] = "You have been logged out!";
            $_SESSION['type'] = "info";
            header("location: index.php?home");
            exit;
        }

    }
}

/*THis is the input field validation function that takes care of checking the input the user
is input and fixing all that needs to be set that is why i call it test_input to test the 
input coming in.*/

//input validation
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if(!empty($data)){
        return $data;
    }
    else{
        session_start();
        $_SESSION['type'] = 'danger';
        $_SESSION['message']= 'You need to fill in the all fields';
        header('location: index.php?login');
        
    }
}

/*this is the part that takes care of reseting the password i used the extract function 
to get all the form input, thereafter performing the required logic to make it functioning*/

function reset_password(){

    if(isset($_POST['pwdReset'])){
        extract($_REQUEST);
        if(empty($username) && empty($password)) { flash('danger', 'You need to fill in the all fields');}
        $get_user = file_get_contents('database/'.$username.'.json');
    
        $db = json_decode($get_user);
        $userEmail = $db->email;
        $username;

        if($userEmail === $email) {
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            header('location: index.php?createPwd');
            exit;
        }

    }
}

function newPass(){
    if(isset($_POST['newPass'])){
        extract($_REQUEST);
        session_start();
        if($password !== $confirm_password) {
            $_SESSION['type'] = 'danger';
            $_SESSION['message'] ='Password does not match';
            header('location: index.php?createPwd');
        }


        $db_name = 'database/'.$username.'.json';
        $file = fopen($db_name, 'w');
        
        $user_arr[] = array();
    
        $form_data = array(
            "username" => test_input($username),
            "email" => test_input($email),
            "password" => password_hash(test_input($password), PASSWORD_DEFAULT),
        );
    
        
        $user = json_encode($form_data, JSON_PRETTY_PRINT);
    
        if(fwrite($file, $user)){
            
            header('location: index.php?welcome');
        }
        else{
            echo 'something is wrong';
        }
        
        fclose($file);
        
        }
}

function flash($type, $message){
    $msg = '
        <div class="alert alert-'.$type.'">
            '.$message.'
        </div>
    ';
    echo $msg;
}


?>


    <div class="jumbotron h1 text-center">Form Validation, Session and Database File system</div>
        <?php 
            if(isset($_GET['home'])){  
                session_start(); 
                flash($_SESSION['type'], $_SESSION['message']);
                unset($_SESSION['message']);
                unset($_SESSION['type']);
            }
            ?>
     
    <div class="m-auto col-md-6">


    <?php if(!isset($_GET['login']) 
        && !isset($_GET['welcome'])  
        && !isset($_GET['createPwd'])  
        && !isset($_GET['reset'])): 
    ?>
        <!-- registration form -->
        <form action="" method="post">

            <div class="form-group">
                <label for="Username">Username</label>
                <input type="text" name="username" id="" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="Email">Email</label>
                <input type="email" name="email" id="" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="" class="form-control" required>
            </div>
            
            <div class="form-group">
                <button class="btn btn-success" name="register">Submit</button>
                <a href="index.php?login">Login</a>

            </div>

        </form>

        <?php 
        elseif(isset($_GET['welcome'])) :
        session_start();
        flash($_SESSION['type'], $_SESSION['message']);
        unset($_SESSION['message']);
        unset($_SESSION['type']);
        ?>
            <div class="card">
                <div class="card-header h1">Welcome <?= $_SESSION['username']; ?></div>
                <form>
                        <input type="submit" class="btn btn-primary" name="logout" value="Logout">
                </form>
            </div>

        <?php  elseif(isset($_GET['reset'])) :?>
            <!-- reset form -->
                <form action="" method="post">
                    <div class="form-group">
                        <label for="Username">Username</label>
                        <input type="text" name="username" id="" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="Email">Email</label>
                        <input type="email" name="email" id="" class="form-control" required>
                    </div>

                    <input type="submit" class="btn btn-primary btn-block" name="pwdReset" value="Reset Password">
                </form>

        <?php  elseif(isset($_GET['createPwd'])) :
                session_start();  
                flash($_SESSION['type'], $_SESSION['message']);
                unset($_SESSION['message']);
                unset($_SESSION['type']);
        ?>
                <!--  create new password form -->
                <form action="" method="post">
                <h5>Create new password</h5>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Confirm Password</label>
                        <input type="password" name="confirm_password" id="" class="form-control" required>
                    </div>

                    <input type="hidden" name="email" value="<?= $_SESSION['email'] ?>">
                    <input type="hidden" name="username" value="<?= $_SESSION['username'] ?>">

                    <input type="submit" class="btn btn-primary btn-block" name="newPass" value="Reset Password">
                </form>

        <?php else: ?>

        <?php 
            if(isset($_GET['login'])){
            session_start();
            $message = $_SESSION['message'] ?? '';
            $type = $_SESSION['type'] ?? '';
            flash($type, $message);
            unset($message);
            unset($type);
            }
        ?>
        <!-- login form -->
        <form action="" method="post">
       
            <div class="form-group">
                <label for="Username">Username</label>
                <input type="text" name="username" id="" class="form-control" required>
            </div>


            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="" class="form-control" required>
            </div>

            
            <div class="form-group">
                <button class="btn btn-success" name="login">Submit</button>
                <a href="index.php">Register</a>
               <p> Forgot password? <a href="index.php?reset">Reset Password</a> </p>
            </div>

        </form>
        
        <?php  endif ?>
    </div>
</div>
<!-- Link to preview this project on heroku is https://haycode.herokuapp.com 
     Link to github is https://github.com/fastbeetech/haycode
-->
</body>
</html>