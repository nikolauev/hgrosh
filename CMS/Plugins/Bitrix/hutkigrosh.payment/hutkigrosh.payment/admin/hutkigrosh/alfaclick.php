<?php
require 'HootkiGrosh.php';
$is_test = ($_POST['is_test'] == '1') ? true : false ;
$hg = new \Alexantr\HootkiGrosh\HootkiGrosh($is_test);
$name = $_POST['login'];
$pwd = $_POST['pwd'];
$res = $hg->apiLogIn($name, $pwd);
if (!$res) {
    echo $hg->getError();
    $hg->apiLogOut(); // Завершаем сеанс
    exit;
}
$data = array(
    'billid'=>$_POST['billid'],
    'phone'=>$_POST['phone'],
);
$responce = $hg->apiAlfaClick($data);
$responceXML =  simplexml_load_string($responce);
$hg->apiLogOut();
echo $responceXML->__toString();
