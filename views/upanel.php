<?php
$id = isset($_GET['id']) ? $_GET['id'] : 'uLogin';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$forced = $_GET['forced'] ? true : false;
$response = '<div><div id="'.$id. '" style="float:left" x-ulogin-params='.
            '"display='.$type.'&fields=first_name,last_name,nickname,email,photo,sex,country,city,photo_big&'.
                    'providers=vkontakte,odnoklassniki,mailru,facebook&'.
                    'hidden=twitter,google,yandex,livejournal,openid&'.
                    'redirect_uri='.$_GET['redirect'].'"></div>';
if ($forced)                     
    $response.='<script>addPanel("'.$id.'")</script>';
echo $response;
                     
?>
