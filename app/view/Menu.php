<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
        <?php
        foreach($data['top-menu'] as $k=>$v){
            echo '<li class="nav-item ';
            if($v['active']){
                echo ' active ';
            }
            echo '"><a class="nav-link" href="'.$v['path'].'">'.$v['text'].' <span class="sr-only">(current)</span></a></li>';
        }
        ?>
        </ul>
    </div>
    <span class="navbar-text">
      For the fun of testing
    </span>    
</nav>  

