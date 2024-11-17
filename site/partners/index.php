<?
// define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (isset($_REQUEST["backurl"]) && mb_strlen($_REQUEST["backurl"])>0) 
    LocalRedirect($backurl);

$APPLICATION->SetTitle("Авторизация на сайте youtwig.ru");
$APPLICATION->SetPageProperty("description", "Авторизация для партнеров на сайте TWiG");

?>

<?
global $USER;
if ($USER->IsAuthorized())
{
?>

 <p>Вы зарегистрированы и успешно авторизовались.</p>
 <p><a href="<?=SITE_DIR?>">Вернуться на главную страницу</a></p>

<?
}
else{
?>
<?
$APPLICATION->IncludeComponent("bitrix:system.auth.authorize", "email", Array(
    "REGISTER_URL" => "/registration/",
    "FORGOT_PASSWORD_URL" => "",
    "PROFILE_URL" => "",
    "SHOW_ERRORS" => "Y"
    )
);
?>

<?  
}
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>