<?php session_start(); ?>
<?php include("includes/register_header.php"); ?>
<?php include("config/init.php"); ?>
<?php include("includes/form_handlers/handle_register.php"); ?>
<?php include("includes/form_handlers/handle_login.php"); ?>

<div class="wrapper">
    <div class="login_box">
        <div class="login_header">
            <h1>NewSFeeD</h1>
            Login or signup below!
        </div>
            <div class="login">
              <h2 id="login_succeed"></h2>
              <form action="" method="POST">
                <input type="text" name="username_email_login" placeholder="Enter username or e-mail" value="<?php echo isset($username_email) ? $username_email : null?>">
                <br>
                <input type="password" name="password_login" placeholder="Enter password" >
                <br>
                <?php echo in_array("Fill out both fields!", $login_errors) ? "Fill out both fields!<br>" : null ?>
                <?php echo in_array("Username/email or password is wrong!", $login_errors) ? "Username/email or password is wrong!<br>" : null ?>
                <input type="submit" value="Login" name="submit_login">
            </form>
            <p id='login'> <a href="#"><i>Are you new member? Register here!</i></a> </p>
        </div>  
        
        <div class="register">
          <form action="" method="POST" class="reg_form">
            <?php echo in_array("You must specify name between 2 and 25 characters!", $errors_array) ? "You must specify name between 2 and 25 characters!<br>" : null ?>
            <br>
            <input type="text" name="reg_first" placeholder="Enter your name.." value="<?php echo isset($first_name) ? $first_name : null ?>" required>
            <br>

            <?php echo in_array("You must specify last name between 2 and 25 characters!", $errors_array) ? "You must specify last name between 2 and 25 characters!<br>" : null ?>  
            <input type="text" name="reg_last" placeholder="Enter your last name.."  value="<?php echo isset($last_name) ? $last_name : null ?>" required>
            <br>

            <?php echo in_array("Username must be between 5 and 25 characters!", $errors_array) ? "Username must be between 5 and 25 characters!<br>" : null ?>
            <?php echo in_array("Username already exists!", $errors_array) ? "Username already exists!<br>" : null ?>
            <input type="text" name="reg_username" placeholder="Enter username.." value="<?php echo isset($username) ? $username : null ?>" required>
            <br>

            <?php echo in_array("Please use regular e-mail format!", $errors_array) ? "Please use regular e-mail format!<br>" : null ?>
            <?php echo in_array("E-mail already exist!", $errors_array) ? "E-mail already exist!<br>" : null ?>  
            <input type="email" name="reg_email" placeholder="Enter email.." value="<?php echo isset($email) ? $email : null ?>" required>
            <br>

            <?php echo in_array("Your password must be between 2 and 25 characters!", $errors_array) ? "Your password must be between 2 and 25 characters!<br>" : null ?>  
            <?php echo in_array("Your password can contain only standard characters!", $errors_array) ? "Your password can contain only standard characters!<br>" : null ?>
            <input type="password" name="reg_password" placeholder="Enter password.." required>
            <br>
            <?php echo in_array("Passwords doesn't match!", $errors_array) ? "Passwords doesn't match!<br>" : null ?>  
            <input type="password" name="reg_confirm" placeholder="Confirm password.." required>
            <br>
            <input type="submit" value="Register" name="submit">
          </form>
          <p id='register'> <a href="#"> <i>Already have an account? Login here!</i> </a> </p>
        </div>  
    </div>
<?php include("includes/register_footer.php");  ?>
