<nav>
    <ul>
    <?php
    foreach($data['top-menu'] as $k=>$v){
        echo '<li><a href="'.$v['path'].'">'.$v['text'].'</a></li>';        
    }
    ?>
    </ul>
</nav>  

