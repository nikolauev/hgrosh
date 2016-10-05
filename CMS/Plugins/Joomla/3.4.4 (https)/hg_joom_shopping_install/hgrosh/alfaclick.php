<?php
require 'HootkiGrosh.php';

$hg = new \Alexantr\HootkiGrosh\HootkiGrosh($_SESSION['hg_test']);
$res = $hg->apiLogIn($_SESSION['hg_login'], $_SESSION['hg_pwd']);
if (!$res) {
    echo $hg->getError();
    $hg->apiLogOut(); // Завершаем сеанс
    exit;
}
$data = array(
    'billid'=>htmlspecialchars($_POST['billid']),
    'phone'=>htmlspecialchars($_POST['phone']),
);
$hg->apiLogOut();
$responceXML =  simplexml_load_string($hg->apiAlfaClick($data));
echo $responceXML->__toString();