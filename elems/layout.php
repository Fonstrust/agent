<! DOCTYPE html >
<html>
<script src="//code-ya.jivosite.com/widget/3ApnJvig7q" async></script>
    <head>
        <script src="https://use.fontawesome.com/dd70fb3d0b.js"></script>
        <meta charset="utf-8">
        <link rel="stylesheet" href='./main.css?<?=time()?>'>
        <title><?=$title?></title>
    </head>
    <body>
        <header>
            <div class="nav-bar">
                <a href="index.php" class="logo"><img src="img/logo.svg" width="120" height="44"></a>
                    <?php
                    echo $header;
                    ?>
            </div>
        </header>
        <?php
        include_once $content;
        ?>
    </body>
    <script type="text/javascript">
    let navAs = document.getElementsByClassName('nav-bar-a-tech')
    let navALen = navAs.length
    if (navALen > 0) {
        let prevVal = document.getElementById('prev_val')
        for (let i = 0; i < navALen; i++) {
            console.log(window.location, navAs[i].href)
            if (window.location == navAs[i].href) {
                navAs[i].classList.add('active-a')
            } else {
                navAs[i].classList.remove('active-a')
            }
        }
    }
    </script>
</html>