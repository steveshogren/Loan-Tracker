<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <script type="text/javascript" src="html/jquery.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <title>Break These Chains</title>
    <link href="html/stylesheet.css" rel="stylesheet" type="text/css" media="screen"/>
</head>

<body>
<div class="main">
<div class="header">
    <div class="header-top">
        <span class="sitename">Break These Chains</span>
        <div class="login-bg">
            <form action="index.php" method="post" name="logout">
                <a href='index.php?logout=true' class="white-link">Logout</a>
            </form>
        </div>
    </div>
    <div class="header-bottom">
        <div id="navcontainer">
            <ul class="navlist">
                <li class="active"><a href="index.php">Home</a></li>
                <li class=""><a href="about.php">About</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="body-main">
<div class="body-bottom">
    

<div id="whole_form">
<div class = "error_message"><?php echo($message);?></div>

    
    <div class="login-box">
    <form action="index.php" method="post">
    <div>
    <?php
        if ($createANewAcccount) {
            echo "To create a new account, choose a username and password. ";
            echo "We do not have password recovery yet, so don't lose it!<br /><br />";

        }
    ?>
    </div>
    <div class ="inputfields">
        <div>
            Username
            <input type="text" name="username"  />
        </div>

        <div>
            Password
            <input type="password" name="password"  />
        </div>
    <?php
        if($createANewAcccount){
            echo '<div>Re-type Password <input type="password" name="password2"  /></div>';
            echo '</div>';
            echo '<br /><input type="submit" name="Submit" value="Create Account" />';
            echo '<br /><br /><span id="create"><a href="index.php">Back to Login</a></span>';
            echo '<br /><input type="hidden" name="createAccount" value="create" />';
        } else {
            echo '</div>';
            echo '<br /><input type="submit" name="login" value="Login" />';
            echo '<br /><br /><span id="create"><a href="index.php?showCreateAccount=true">Create New Account</a></span>';
        }
    ?>
</form>
</div>

</div>
</div>
</body>
</html>