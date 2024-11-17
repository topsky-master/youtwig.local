<?php if(defined('ERROR_404')){ ?>
<?php global $APPLICATION; 
$APPLICATION->SetTitle('404 - страница не найдена');
$APPLICATION->AddChainItem("404 - страница не найдена", ""); ?>
<style>
	.map-columns{ display: none}
</style>
<div id="404page">
<h1>
    Сожалеем, но страница не найдена
</h1>
<p>Сожалеем, но указанная Вами страница не найдена</p>
<ol class="rounded-list">
    <li>
        <span>
            Проверьте правильность ввода url в адресной строке, Вы ввели:
            <strong>
				<?php echo (IMPEL_PROTOCOL) . $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>
            </strong>
        </span>
    </li>
    <li>
        <span>
            Возможно, Вы найдете нужный Вам товар или материал, используя меню нашего сайта или в информационных блоках внизу экрана
        </span>
    </li>
    <li>
        <span>
            Воспользуйтесь поиском по центру верхнего меню
        </span>
    </li>
    <li>
        <span>
            Возможно, Вам поможет
                <a href="https://youtwig.ru/sitemap.php">
                    карта сайта
                </a>
        </span>
    </li>
    <li>
        <span>
            Свяжитесь с нами, используя
            <a href="https://youtwig.ru/info/kontakty/">
                контакты
            </a>
        </span>
    </li>
    <li>
        <span>
            Напишите нам письмо:
            <a href="mailto:info@youtwig.ru ">
                info@youtwig.ru
            </a>
        </span>
    </li>
    <li>
        <span>
            Посредине экрана в левой части находится блок
                <strong>
                    Задать Вопрос
                </strong>,
            используйте его для связи с нами
        </span>
    </li>
</ol>
</div>
<?php }; ?>