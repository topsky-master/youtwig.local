<?

global $MESS;

IncludeModuleLangFile(__FILE__);

if (!class_exists("my_stat")){


class my_stat extends CModule
{
	var $MODULE_ID = "my.stat";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $PARTNER_NAME;
	var $PARTNER_URI;

	function my_stat()
	{
		$arModuleVersion            = array();

		include(dirname(__FILE__)."/version.php");

		$this->MODULE_VERSION       = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE  = $arModuleVersion["VERSION_DATE"];

		$this->MODULE_NAME          = GetMessage("MY_STAT_INSTALL_NAME");
		$this->MODULE_DESCRIPTION   = GetMessage("MY_STAT_INSTALL_DESCRIPTION");
		$this->PARTNER_NAME         = GetMessage("MY_STAT_PARTNER_NAME");
		$this->PARTNER_URI          = GetMessage("MY_STAT_PARTNER_URI");
	}


	function InstallDB()
	{
		global $DB;

		//$DB->RunSQLBatch(dirname(__FILE__)."/sql/install.sql");

		RegisterModule("my.stat");

        RegisterModuleDependences("main", "OnAdminListDisplay", "my.stat", "\\My\\Stat\\DataTable", "displayActPrint");
		RegisterModuleDependences("main", "OnBeforeProlog", "my.stat", "\\My\\Stat\\DataTable",  "OnBeforePrologHandler");

        //AddEventHandler("main", "OnBeforeProlog", "MyOnBeforePrologHandler", 50);
        //AddEventHandler("main", "OnAfterUserLogin", Array("AfterAuth", "OnAfterUserLoginHandler"));
        //AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("SendEventAfterElementAdd", "OnAfterIBlockElementAdd"));
        //AddEventHandler("main", "OnBeforeUserLogin", "OnBeforeUserLoginHandler");

        //RegisterModuleDependences("main", "OnBeforeProlog", "my.stat", "\\My\\Stat\\DataTable", "MyOnBeforePrologHandler");
        //RegisterModuleDependences("main", "OnAfterUserLogin", "my.stat", "\\My\\Stat\\DataTable", "OnAfterUserLoginHandler");
        //RegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "my.stat", "\\My\\Stat\\DataTable", "OnAfterIBlockElementAdd");
        //RegisterModuleDependences("main", "OnBeforeUserLogin", "my.stat", "\\My\\Stat\\DataTable", "OnBeforeUserLoginHandler");
        //RegisterModuleDependences("search", "BeforeIndex", "my.stat", "\\My\\Stat\\DataTable", "BeforeIndexHandler");

        //RegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", "my.stat", "\\My\\Stat\\DataTable", "send_afterbuy_event");

		return true;
	}

	function UnInstallDB()
	{
		global $DB;

		//$DB->RunSQLBatch(dirname(__FILE__)."/sql/uninstall.sql");

        UnRegisterModuleDependences("main", "OnAdminListDisplay", "my.stat", "\\My\\Stat\\DataTable", "displayActPrint");
		UnRegisterModuleDependences("main", "OnBeforeProlog", "my.stat", "\\My\\Stat\\DataTable",  "OnBeforePrologHandler");

        //UnRegisterModuleDependences("main", "OnBeforeUserRegister", "my.stat", "\\My\\Stat\\DataTable", "OnBeforeUserRegister");
   		//UnRegisterModuleDependences("main", "OnBeforeUserLogin", "my.stat", "\\My\\Stat\\DataTable", "OnBeforeUserLogin");
   		//UnRegisterModuleDependences("sale", "OnOrderUpdate", "my.stat", "\\My\\Stat\\DataTable", "checkStatusOfSaleOrderAdmin");
        //UnRegisterModuleDependences("sale", "OnSaleStatusOrder", "my.stat", "\\My\\Stat\\DataTable", "checkStatusOfSaleOrder");
        //UnRegisterModuleDependences("sale", "OnSaleComponentOrderOneStepComplete", "my.stat", "\\My\\Stat\\DataTable", "send_afterbuy_event");

        //UnRegisterModuleDependences("main", "OnBeforeProlog", "my.stat", "\\My\\Stat\\DataTable", "MyOnBeforePrologHandler");
        //UnRegisterModuleDependences("main", "OnAfterUserLogin", "my.stat", "\\My\\Stat\\DataTable", "OnAfterUserLoginHandler");
        //UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "my.stat", "\\My\\Stat\\DataTable", "OnAfterIBlockElementAdd");
        //UnRegisterModuleDependences("main", "OnBeforeUserLogin", "my.stat", "\\My\\Stat\\DataTable", "OnBeforeUserLoginHandler");
        //UnRegisterModuleDependences("search", "BeforeIndex", "my.stat", "\\My\\Stat\\DataTable", "BeforeIndexHandler");


        UnRegisterModule("my.stat");

		return true;
	}

	function InstallFiles()
	{
		//CopyDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
		//CopyDirFiles(dirname(__FILE__)."/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles(dirname(__FILE__)."/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles(dirname(__FILE__)."/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

		return true;
	}

	function DoInstall()
	{
		$this->InstallFiles();
		$this->InstallDB();
	}

	function DoUninstall()
	{
		$this->UnInstallDB();
		$this->UnInstallFiles();
	}
}

}