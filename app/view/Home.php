<!DOCTYPE html>
<html lang="<?php echo $data['lang']; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $data['title']; ?></title>
  <link rel="stylesheet" href="<?php echo CSS_PATH.'global.css'; ?>">
</head>
<body>
    <div class="container">
        <?php
        //top menu
        require_once(VIEW_PATH.'menu.php');
        //content
        $content = '<h1>'.$data['title'].'</h1>';
        $content .= $data['content'];
        //output 
        echo $content;    
        ?>
    </div>
</body>
</html>