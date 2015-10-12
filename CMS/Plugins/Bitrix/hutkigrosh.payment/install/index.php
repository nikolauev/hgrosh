<?
IncludeModuleLangFile(__FILE__);
Class hutkigrosh_payment extends CModule
{
	const MODULE_ID = 'hutkigrosh.payment';
	var $MODULE_ID = 'hutkigrosh.payment';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $strError = '';

	function __construct()
	{
		$arModuleVersion = array();
		include(dirname(__FILE__)."/version.php");
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = GetMessage("hutkigrosh.payment_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("hutkigrosh.payment_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("hutkigrosh.payment_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("hutkigrosh.payment_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		RegisterModule(self::MODULE_ID);
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModule(self::MODULE_ID);
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		# /bitrix/php_interface/include/sale_payment
		if ( !is_dir($sale_payment = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment') )
		{
			mkdir($sale_payment, 0755);
		}

		if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID))
		{
			CopyDirFiles($p."/admin", $sale_payment, true, true) ;
		}

        if ( !is_dir($sale_payment = $_SERVER['DOCUMENT_ROOT'].'/payinfo') )
        {
            mkdir($sale_payment, 0755);
        }
        if (is_dir($p = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID))
        {
            CopyDirFiles($p."/payinfo", $sale_payment, true, true) ;
        }

		return true;
	}

	function UnInstallFiles()
	{
 		DeleteDirFilesEx('/bitrix/php_interface/include/sale_payment/hutkigrosh');
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;
		$this->InstallFiles();
		$this->InstallDB();
	}

	function DoUninstall()
	{
		global $APPLICATION;
		$this->UnInstallFiles();
		$this->UnInstallDB();
	}
}
?>
