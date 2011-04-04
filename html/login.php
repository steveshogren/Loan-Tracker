<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Break These Chains - Login</title>
<link href="html/login.css" rel="stylesheet" type="text/css">
</head>
<body id="body">
<div id="loginbar">
    <form action="index.php" method="post" name="logout">
        <a href='index.php?logout=true'></a>
    </form>
  </div>
  <div id="heading">Break These Chains</div>
<div id="top_header"></div>
<div id="whole_form">
<div class = "error_message"><?php echo($message);?></div>
<div class ="textarea">
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
</body>
</html>