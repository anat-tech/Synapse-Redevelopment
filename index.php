<!DOCTYPE HTML>
<html>
<head>
    <title>Synapse Redevelopment Template Example</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <?php include 'lib/UserUI.php'; include 'lib/webTools.php'; $ui = new UserUI; ?>   
    <?php $ui->loginForm(); ?>
    <?php $ui->logoutForm(); ?>
    <!--?php $ui->updateCredentialsForm(); ?-->
    <?php $ui->registerForm(); ?>
    <?php $ui->updateProfileForm(); ?>
    <form>
        <p><label><input type="submit" class="button" value="Home"></label></p>
        <label>You are logged in as: <?php $ui->checkCookie() ?></label>
        <p>Current cookie: <?php webTools::printCookies(); ?></p> 
    </form>
</body>
</html>