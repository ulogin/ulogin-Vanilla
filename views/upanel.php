<?php
$id = isset($_GET['id']) ? $_GET['id'] : 'uLogin';
$type = isset($_GET['type']) ? $_GET['type'] : 'small';
$forced = $_GET['forced'] ? true : false;
$type = $_GET['type'];
$redirect = $_GET['redirect'];
$providers = $_GET['providers'];
$hidden = $_GET['hidden'];
if ($type == 'window'){
    $response = '<a href="#" id="'.$id.'"'.
                'x-ulogin-params="display=window&fields=first_name,last_name,nickname,email,photo,sex,country,city,photo_big&'.
                'redirect_uri='.$redirect.'" style="border:0px;"><img src="http://ulogin.ru/img/button.png" width=187 height=30/></a>'.
                '<div style="clear:both;"></div>';
}else{
$response = '<div><div id="'.$id. '" style="float:left" x-ulogin-params='.
            '"display='.$type.'&fields=first_name,last_name,nickname,email,photo,sex,country,city,photo_big'.
                    '&providers='.$providers.
                    '&hidden='.$hidden.
                    '&redirect_uri='.$redirect.'"></div><div style="clear:both;"></div>';
}
if ($forced)                     
    $response.='<script>uLogin.initWidget("'.$id.'")</script>';
echo $response;
                     
?>
