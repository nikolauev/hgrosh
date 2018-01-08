<?

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

Class sale_hutkigrosh extends CModule
{
	const MODULE_ID = 'sale.hutkigrosh';
	const MODULE_PATH = '/bitrix/php_interface/include/sale_payment/hutkigrosh';
	var $MODULE_ID = 'sale.hutkigrosh';
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
		$this->MODULE_NAME = GetMessage("hutkigrosh_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("hutkigrosh_MODULE_DESC");

		$this->PARTNER_NAME = GetMessage("hutkigrosh_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("hutkigrosh_PARTNER_URI");
	}

	function InstallDB($arParams = array())
	{
		RegisterModule(self::MODULE_ID);

        $psId = $this->addPaysys();
        if ($psId === false)
            throw new Exception(Loc::getMessage("hutkigrosh_ERROR_PS_INSTALL"));

        //сохранение paystsemId в настройках модуля
        Option::set( $this->MODULE_ID, "PAY_SYSTEM_ID",  $psId);

        //регистрируем обработчик пл. системы
        $handlersIds = $this->addPaysysHandler($psId);
        if(empty($handlersIds))
            throw new Exception(Loc::getMessage("hutkigrosh_ERROR_PS_ACTION_REG"));
        //сохраняем id обработчиков пл. системы
        Option::set( $this->MODULE_ID, "handlers_ids",  implode("|", $handlersIds));

		return true;
	}

	function UnInstallDB($arParams = array())
	{
        $this->deletePaysys();
        $this->deletePaysysHandler();
        Option::delete( $this->MODULE_ID );
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
	    $installDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.self::MODULE_ID.'/install';

		# /bitrix/php_interface/include/sale_payment
		if ( !is_dir($toDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment') )
		{
			mkdir($toDir, 0755);
		}

		if (is_dir($formDir = $installDir."/php_interface/include/sale_payment"))
		{
			CopyDirFiles($formDir, $toDir, true, true) ;
		}

        if ( !is_dir($toDir = $_SERVER['DOCUMENT_ROOT'].'/hutkigrosh') )
        {
            mkdir($toDir, 0755);
        }
        if (is_dir($formDir = $installDir."/hutkigrosh"))
        {
            CopyDirFiles($formDir, $toDir, true, true) ;
        }
		return true;
	}

	function UnInstallFiles()
	{
 		DeleteDirFilesEx(self::MODULE_PATH);
		DeleteDirFilesEx('/hutkigrosh');
		return true;
	}

	function DoInstall()
	{
        try {
            if( ! IsModuleInstalled("sale") )
                throw new Exception(Loc::getMessage("hutkigrosh_ERROR_SALE_MODULE_NOT_INSTALLED"));
            if( ! function_exists("curl_init") )
                throw new Exception(Loc::getMessage("hutkigrosh_ERROR_CURL_NOT_INSTALLED"));

            $this->InstallFiles();
            $this->InstallDB();


        } catch (Exception $e) {
            $this->DoUninstall();
            $GLOBALS["APPLICATION"]->ThrowException($e->getMessage());
            return false;
        }
	}

	function DoUninstall()
	{
        try {
            $this->UnInstallFiles();
            $this->UnInstallDB();
        } catch (Exception $e) {
        }
	}

    protected function addPaysys()
    {
        return CSalePaySystem::Add(
            array(
                "NAME" => Loc::getMessage("hutkigrosh_PS_NAME"),
                "DESCRIPTION" => Loc::getMessage("hutkigrosh_PS_DESC"),
                "LOGOTIP" => self::MODULE_PATH."/hgrosh.png",
                "ACTIVE" => "Y",
                "SORT" => 100,
            )
        );
    }

    protected function addPaysysHandler($psId )
    {
        $handlersIds = array();
        $fields = array(
            "PAY_SYSTEM_ID" => $psId,
            "NAME" => Loc::getMessage("hutkigrosh_PS_ACTION_NAME"),
            "DESCRIPTION" => Loc::getMessage("hutkigrosh_PS_DESC"),
            "ACTION_FILE" => self::MODULE_PATH,
            "LOGOTIP" => self::MODULE_PATH."/hgrosh.png",
            "NEW_WINDOW" => "N",
            "HAVE_PREPAY" => "N",
            "HAVE_RESULT" => "N",
            "HAVE_ACTION" => "N",
            "HAVE_PAYMENT" => "Y",
            "HAVE_RESULT_RECEIVE" => "Y",
            "ENCODING" => "utf-8",
        );
        $id = CSalePaySystemAction::Add($fields);
        $handlersIds[] = $id;
//        $personTypes = CSalePersonType::GetList(
//            array("SORT" => "ASC", "NAME" => "ASC"),
//            array()
//        );
//        while($pt = $personTypes->Fetch())
//        {
//            $fields["PERSON_TYPE_ID"] = $pt["ID"];
//            $id = CSalePaySystemAction::Add($fields);
//            if($id != false)
//                $handlersIds[] = $id;
//
//        }

        return $handlersIds;
    }

    protected function deletePaysys()
    {
        $psId = (int)Option::get( $this->MODULE_ID, "PAY_SYSTEM_ID");

        $order = CSaleOrder::GetList(array(), array("PAY_SYSTEM_ID" => $psId))->Fetch();
        if($order["ID"] > 0)
            throw new Exception(Loc::getMessage("hutkigrosh_ERROR_ORDERS_EXIST"));

        // verify that there is a payment system to delete
        if ($arPaySys = CSalePaySystem::GetByID($psId))
        {
            if(!CSalePaySystem::Delete($psId))
                throw new Exception(Loc::getMessage("hutkigrosh_ERROR_DELETE_EXCEPTION"));
        }

        return true;
    }

    protected function deletePaysysHandler()
    {
        $handlersIds = explode("|", Option::get( $this->MODULE_ID, "handlers_ids"));
        if(!empty($handlersIds))
            foreach($handlersIds as $id)
                CSalePaySystemAction::Delete($id);
        return true;
    }
}
?>
