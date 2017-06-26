<?php
file_get_contents("http://".$_SERVER["SERVER_NAME"]."/index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_hg&purchaseid=".$_GET['purchaseid']);
?>