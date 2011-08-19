<!DOCTYPE HTML>
<html>
<head>
    <title>Synapse Redevelopment Template Example</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <?php include 'lib/UserUI.php'; include 'lib/webTools.php'; $ui = new UserUI; ?>
    <?php webTools::phpErrorsOn(); ?>    
    <?php $ui->loginForm(); ?>
    <?php UserUI::registerForm(); ?>
    <?php webtools::printCookies(); ?>
</body>
</html>


