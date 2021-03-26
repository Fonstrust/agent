<div class="container-mini autorization-block">
    <form method="post" action="#">
        <div class="block-header">
            <img src="/elems/img/key.png">
            <span class="block-head">Авторизация</span>
        </div>
        <hr>
        <div class="autorization-block-container">
            <input placeholder="E-mail" type="text" name="mail" pattern="^[\w.-]+@[\w-]+.[\da-z]{2,3}$">
            <input placeholder="Пароль" type="password" name="pass" pattern="^[\w!]+$">
            <br>
            <input class="btn-green t18 mt40 fr" type="submit" name="to_lk" value="Войти">
            <br>
            <div class="auth-footer">
                <a href="?prev=restore_pass">Забыли пароль?</a>/
                <a href="?prev=register">Зарегистрироваться</a>
            </div>
        </div>
    </form>
</div>