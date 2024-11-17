<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if(!checkQuantityRigths()){
    LocalRedirect('/');
    die();
}

?>
<?$APPLICATION->IncludeComponent("bitrix:forum", "impel", Array(
    "AJAX_POST" => "Y",	// Использовать AJAX в диалогах
    "ATTACH_MODE" => array(	// Как показывать прикрепленные рисунки (под сообщением)
        0 => "THUMB",
        1 => "NAME",
    ),
    "ATTACH_SIZE" => "90",	// Размер миниатюры рисунка (под сообщением, px)
    "CACHE_TIME" => "36000",	// Время кеширования (сек.)
    "CACHE_TIME_FOR_FORUM_STAT" => "3600",	// Время кеширования статистики (сек.)
    "CACHE_TIME_USER_STAT" => "60",	// Время кеширования списка пользователей на форуме (сек.)
    "CACHE_TYPE" => "A",	// Тип кеширования
    "CHECK_CORRECT_TEMPLATES" => "N",	// Проверять корректность шаблонов пути
    "DATE_FORMAT" => "d.m.Y",	// Формат показа даты
    "DATE_TIME_FORMAT" => "d.m.Y H:i:s",	// Формат показа даты и времени
    "EDITOR_CODE_DEFAULT" => "N",	// По умолчанию показывать невизуальный режим редактора
    "FID" => "",	// Показывать только выбранные форумы
    "FORUMS_PER_PAGE" => "30",	// Количество форумов на одной странице
    "HELP_CONTENT" => "",	// Путь к файлу, содержащему "Помощь по форуму" (при пустом поле ввода будет использовано значение по умолчанию)
    "IMAGE_SIZE" => "500",	// Размер рисунков в тексте сообщения (px)
    "MESSAGES_PER_PAGE" => "30",	// Количество сообщений на одной странице
    "NAME_TEMPLATE" => "",	// Формат имени
    "NO_WORD_LOGIC" => "N",	// Отключить обработку слов как логических операторов
    "PAGE_NAVIGATION_TEMPLATE" => "forum",	// Название шаблона для вывода постраничной навигации
    "PAGE_NAVIGATION_WINDOW" => "5",	// Количество страниц в постраничной навигации
    "PATH_TO_AUTH_FORM" => "/auth/",	// Путь к форме авторизации
    "PATH_TO_ICON" => "/bitrix/images/forum/icon/",
    "PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
    "RATING_ID" => "",	// Рейтинг
    "RATING_TYPE" => "like_graphic",	// Вид кнопок рейтинга
    "RESTART" => "N",	// Искать без учета морфологии (при отсутствии результата поиска)
    "RSS_CACHE" => "1800",	// Время кеширования RSS-ленты (сек.)
    "RSS_COUNT" => "30",	// Количество элементов для экспорта
    "RSS_TN_DESCRIPTION" => "",	// Описание ленты (при пустом поле ввода будет использовано значение по умолчанию)
    "RSS_TN_TITLE" => "",	// Название ленты (при пустом поле ввода будет использовано значение по умолчанию)
    "RSS_TYPE_RANGE" => array(	// Использовать спецификации
        0 => "",
    ),
    "RULES_CONTENT" => "",	// Путь к файлу, содержащему "Правила форума" (при пустом поле ввода будет использовано значение по умолчанию)
    "SEF_FOLDER" => "/forum/",	// Каталог ЧПУ (относительно корня сайта)
    "SEF_MODE" => "Y",	// Включить поддержку ЧПУ
    "SEF_URL_TEMPLATES" => array(
        "active" => "topic/new/",
        "help" => "help/",
        "index" => "index.php",
        "list" => "forum#FID#/",
        "message" => "messages/forum#FID#/message#MID#/#TITLE_SEO#/",
        "message_appr" => "message/approve/forum#FID#/topic#TID#/",
        "message_move" => "message/move/forum#FID#/topic#TID#/message#MID#/",
        "message_send" => "user/#UID#/send/#TYPE#/",
        "pm_edit" => "pm/folder#FID#/message#MID#/user#UID#/#mode#/",
        "pm_folder" => "pm/folders/",
        "pm_list" => "pm/folder#FID#/",
        "pm_read" => "pm/folder#FID#/message#MID#/",
        "pm_search" => "pm/search/",
        "profile" => "user/#UID#/edit/",
        "profile_view" => "user/#UID#/",
        "read" => "forum#FID#/topic#TID#/message#MID#/",
        "rss" => "rss/#TYPE#/#MODE#/#IID#/",
        "rules" => "rules/",
        "search" => "search/",
        "subscr_list" => "subscribe/",
        "topic_move" => "topic/move/forum#FID#/topic#TID#/",
        "topic_new" => "topic/add/forum#FID#/",
        "topic_search" => "topic/search/",
        "user_list" => "users/",
        "user_post" => "user/#UID#/post/#mode#/",
    ),
    "SEND_ICQ" => "A",
    "SEND_MAIL" => "E",	// Могут отправлять письмо (e-mail) из профиля
    "SEO_USER" => "Y",	// Как показывать имя пользователя (seo)
    "SEO_USE_AN_EXTERNAL_SERVICE" => "N",	// Использовать внешний сервис для перевода названия темы (seo)
    "SET_DESCRIPTION" => "Y",	// Устанавливать мета-тег 'Description' из первого сообщения темы
    "SET_NAVIGATION" => "Y",	// Показывать навигацию (хлебные крошки)
    "SET_PAGE_PROPERTY" => "Y",	// Устанавливать теги и описание темы в свойства страницы
    "SET_TITLE" => "Y",	// Устанавливать заголовок страницы
    "SHOW_AUTHOR_COLUMN" => "N",	// Показывать колонку "Автор" в списке тем
    "SHOW_AUTH_FORM" => "Y",	// Показывать форму авторизации
    "SHOW_FIRST_POST" => "N",	// Всегда показывать первое сообщение темы (при отключенной опции будет использоваться значение из настроек форума)
    "SHOW_FORUMS" => "N",	// Показывать форумы для быстрого доступа
    "SHOW_FORUMS_LIST" => "N",
    "SHOW_FORUM_ANOTHER_SITE" => "Y",	// Показывать администратору форумы других сайтов
    "SHOW_FORUM_USERS" => "N",	// Показывать список пользователей
    "SHOW_LEGEND" => "N",	// Показывать легенду
    "SHOW_NAVIGATION" => "Y",	// Показывать навигационную цепочку
    "SHOW_RATING" => "Y",	// Включить рейтинг
    "SHOW_STATISTIC_BLOCK" => array(	// Показывать блоки статистики
        0 => "",
    ),
    "SHOW_SUBSCRIBE_LINK" => "N",	// Показывать ссылку "Подписка" в верхнем меню
    "SHOW_TAGS" => "Y",	// Показывать теги
    "SHOW_VOTE" => "Y",	// Разрешить опросы
    "THEME" => "gray",	// Темы
    "TIME_INTERVAL_FOR_USER_STAT" => "10",	// Период для отображения статистики (сек.)
    "TMPLT_SHOW_ADDITIONAL_MARKER" => "",	// Дополнительный маркер для новых сообщений
    "TOPICS_PER_PAGE" => "30",	// Количество тем на одной странице
    "USER_FIELDS" => array(	// Показывать пользовательские поля сообщения
        0 => "",
    ),
    "USER_PROPERTY" => array(	// Показывать пользовательские поля в профиле
        0 => "",
    ),
    "USE_DESC_PAGE_MESSAGE" => "N",
    "USE_DESC_PAGE_TOPIC" => "Y",
    "USE_LIGHT_VIEW" => "Y",	// Использовать простой режим настройки
    "USE_NAME_TEMPLATE" => "N",	// Использовать указанный формат имени
    "USE_RSS" => "Y",	// Разрешить RSS
    "VOTE_CHANNEL_ID" => "1",	// Группа опросов
    "VOTE_GROUP_ID" => array(	// Группы пользователей, которые могут создавать опрос
        0 => "1",
    ),
    "VOTE_TEMPLATE" => "light",	// Шаблон для голосований
    "VOTE_UNIQUE" => array(	// Не голосовать дважды
        0 => "1",
        1 => "2",
        2 => "8",
    ),
    "VOTE_UNIQUE_IP_DELAY" => "10 D",	// Не голосовать дважды с одного IP в течение
    "WORD_LENGTH" => "50",	// Длина слова
    "WORD_WRAP_CUT" => "23",	// Длина фразы (если "0", то фраза не обрезается)
),
    false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>