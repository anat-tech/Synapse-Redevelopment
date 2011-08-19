<!DOCTYPE HTML>
<html>
<head>
    <title>Synapse Redevelopment Template Example</title>  
</head>
<body>
    <?php include 'lib/UserUI.php'; include 'lib/webTools.php'; ?>
    <?php webTools::phpErrorsOn(); ?>
    <h2>Login</h2>
    <?php UserUI::loginForm(); ?>
    <h3>Register</h3>
    <?php UserUI::registerForm(); ?>
    
</body>
</html>


