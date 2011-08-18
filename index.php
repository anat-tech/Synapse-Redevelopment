<!DOCTYPE HTML>
<html>
<head>
    <title>Synapse Redevelopment Template Example</title>
    
</head>
<body>
    <h2>login</h2>
    <?php include_once 'UserUI.php'; include_once 'webTools.php' ?>
    <?php UserUI::loginForm(); ?>
    <?php echo UserUI::resetPass('cameron@anat.org.au'); ?>
    <h4><?php webTools::printCookies(); ?></h4>
    
</body>
</html>


