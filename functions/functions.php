<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header('Content-Type: text/html; charset=utf-8');

function connections(){
    $file = '';
    if (empty($_GET['page'])){
        $file = 'main';
    }else{
        $file = $_GET['page'];
    }
    include 'tempate/header.php';
    include  $file . '.php';
    include 'tempate/footer.php';

}
function do_query($query)
{
    global $mysqli;

    $result = mysqli_query($mysqli, $query);
    return $result;
}

function comm(){
    if (isset($_POST['comm'])){
        $data = $_POST;
            $result = do_query("SELECT COUNT(*) as count FROM comment WHERE `name` = '{$data['name']}'");
            $result = $result->fetch_object();

            if ($data['name'] == '' or $data['name'] == ' '){
                $errors = 'Вы не ввели имя';
                if (trim($data['name']) <= 3 or trim($data['name']) >= 16){
                    $errors = 'имя должен составлять от 3 до 16 символов';

                }

            }

            if (empty($data['content'])) {
                $errors = 'Вы не написали отзыв';
                if ($data['content'] <= 10 or $data['content'] >= 1000) {
                    $errors = 'Отзыв должен составлять от 10 до 1000 символов';
                }
            }
            if (empty($errors)){
                if (!empty($result)) {
                    // сохраняет все данные в БД
                    $wer =  do_query("INSERT INTO comment (`name`,`content`)VALUES ('{$data['name']}','{$data['content']}')");
                    if (!empty($wer)){
                         echo '<div style="background: #4cae4c; color: #ffffff;">Успешно отправлено</div>';
                    }
                }
            }else{
                    echo '<div style="color: red;">'.$errors.'</div>';
            }
    }
    return;
}
function post_comm() {
    $query = do_query("SELECT `name`, `content`, `data` FROM `comment` ORDER BY `id` DESC");
    $myrow_otzivi = mysqli_fetch_array($query);

    if ($myrow_otzivi['name']) {
        do {
            $mydata = new DateTime($myrow_otzivi['data']);
            echo "<div class='comment'>";
            echo "<div class='row'><div class='col-lg-6 col-xs-6'><div class='glyphicon glyphicon-user'></div><span class='user'>" . $myrow_otzivi['name'] . "</span> </div><div class='col-lg-6 col-xs-6'><div class='glyphicon glyphicon-time'></div><span class='data'>" . $mydata->format('d.m.Y H:i:s') . "</span> </div></div>";
            echo "<div class='content-text'><div><p>" . $myrow_otzivi['content'] . "</p></div></div>";
            echo "</div>";
        } while ($myrow_otzivi = mysqli_fetch_array($query));
    } else {
        echo 'нет отзыва, вы будете первыми';
    }
return;
}
function events(){
    if (isset($_POST['submit'])){
        $data = $_POST;
        $errors = array();
        if (empty($data['name'])){
            $errors = 'Вы не ввели имя';
        }
        if (empty($data['surname'])){
            $errors = 'Вы не ввели фамилию';
        }
        if (empty($data['phone'])){
            $errors = 'Вы не ввели телефлон';
        }
        if (empty($data['data'])){
            $errors = 'Вы не ввели дату';
        }
        if (empty($errors)){
            $result = do_query("SELECT COUNT(*) as count FROM events WHERE `data` = '{$data['data']}'");

            $result = $result->fetch_object();
            if (!empty($result->count)){
                echo '<div style="background: red; color: white">К сожелению эта дата занята, выберите другой день!</div>';
            }
            else{
                // сохраняет все данные в БД
                $wer =  do_query("INSERT INTO `events` (`id`, `name`, `surname`, `phone`, `event`, `rooms`, `data`) VALUES (NULL, '{$data['name']}', '{$data['surname']}', '{$data['phone']}', '{$data['event']}', '{$data['rooms']}', '{$data['data']}')");
                if (!empty($wer)){
                    echo '<div style="background: #4cae4c; color: #ffffff;">Успешно отправлено! Подождите, когда наш оператор свяжется с вами</div>';
                }
            }
        }
        else{
            echo '<div style="color: red;">'.$errors.'</div>';
        }
    }
    return;
}
function event_mail(){

    if (isset($_POST)){


        $data = $_POST;

        if (!empty($data)){
            $mess = implode("", $data);
            $to      = 'kafe-lyi@yandex.ru';
            $subject = 'Заказ';
            $message = "$mess";
            $headers = 'From: segasle@kafe-lyi.ru' . "\r\n" .
                'Reply-To: segasle@kafe-lyi.ru' . "\r\n" .
                "Content-Type: text/plain; charset=\"UTF-8\"\r\n"
                .'X-Mailer: PHP/' . phpversion();

            mail("$to", "$subject", "$message", "$headers");

        }
    }
    return;
}
function menu_account($user_accont) {
    $out   = '';
    $ul_class = 'user_account';
    foreach ( $user_accont as $item ) {

        $out .= '<li><a href="?page=' . $item['way'] . '">' . $item['lnscription'] . '</a></li>';
    }

    $out = '<ul class="'.$ul_class.'">' . $out . '</ul>';

    return $out;
}
function meun_food($meun_food) {
    $out   = '';
    foreach ( $meun_food as $item ) {
        $out .= '<li><a href="?page=' . $item['way'] . '">' . $item['lnscription'] . '</a></li>';
    }

    $out = '<ul>' . $out . '</ul>';

    return $out;
}