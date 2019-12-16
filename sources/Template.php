<?php

namespace SteamBoat;

use Monolog\Logger;
use Smarty;
use function Arris\setOption;

/**
 * Class Template
 * @package SteamBoat
 *
 * @todo: move to AstolfoEngine
 */
class Template implements TemplateInterface
{
    /**
     * @var Smarty
     */
    private static $smarty;

    /**
     * @var \stdClass
     */
    private static $steamboat_logic_instance;

    /**
     * @var array
     */
    private static $banners;

    /**
     * @var array
     */
    private static $response;

    /**
     * @var string
     */
    private static $title_delimeter;

    /**
     * @var string
     */
    private static $search_mask_puid40;
    /**
     * @var Logger|null
     */
    private static $logger;

    /*
     * META-теги
     */
    public static $meta = [
        'title'         =>  '',
        'keywords'      =>  '',
        'description'   =>  ''
    ];

    /**
     * @var array массив заголовков
     */
    public static $_title = [];

    /**
     * @var string
     */
    public static $ajur_adv_topic;

    /** ====================== */

    public static function init($smarty, $that = null, $options = [], $logger = null)
    {
        self::$title_delimeter = " " . setOption($options, 'title_delimeter', '&#8250;') . " ";
        self::$search_mask_puid40 = setOption($options, 'search_mask_puid40', '<!--#echo var="ADVTOPIC"-->');

        self::$logger
            = $logger instanceof Logger
            ? $logger
            : (new Logger('null'))->pushHandler(new \Monolog\Handler\NullHandler());

        self::$smarty = $smarty;
        self::$steamboat_logic_instance = $that;

        self::$banners = [];
        self::$response = [
            'status'    =>  null,
            'mode'      =>  'HTML',
            'data'      =>  [
                'page'      =>  1
            ],
            'html'      =>  '',
        ];
    }

    public static function assign($variable, $value, $nocache = false)
    {
        //@todo: А что насчет NESTED-variables? ('menu.opened' к примеру) ?
        self::$smarty->assign($variable, $value);
    }

    public static function addMeta($variable, $value)
    {
        self::$meta[ $variable ] = $value;
    }

    public static function getMeta()
    {
        return self::$meta;
    }

    public static function addTitle($title)
    {
        self::$_title[] = $title;
    }

    public static function getTitle():string
    {
        // преобразует кавычки-лапки в html-entities
        array_walk(Template::$_title, function ($t){
            self::escapeQuotes($t);
        });

        return implode(self::$title_delimeter, array_reverse(Template::$_title));
    }

    public static function setResponseMode($mode = 'HTML')
    {
        self::$response['mode']
            = (in_array($mode, ['HTML', 'JSON', 'AJAXHTML', 'AJAX']))
            ? strtoupper($mode)
            : 'HTML';
    }

    /**
     * Заменяет кавычки-лапки на html-entities
     *
     * @param $string
     * @return mixed
     */
    private static function escapeQuotes($string)
    {
        return str_replace(['«', '»'], ['&laquo;', '&raquo;'], $string);
    }

    public static function render()
    {
        if (self::$response['mode'] === 'JSON') {

            self::$response['html'] = self::$smarty->fetch("__ajax_template.tpl");
            self::$response['status'] = 'ok';

            return json_encode(self::$response);

        } elseif(self::$response['mode'] === 'AJAX' || self::$response['mode'] === 'AJAXHTML') {

            self::$response['html'] = self::$smarty->fetch("__ajax_template.tpl");
            self::$response['status'] = 'ok';

            return self::$response['html'];

        } else {

            self::$response['html'] = self::$smarty->fetch("__main_template.tpl");

            // self::bindBanners();

            return self::$response['html'];
        }
    }

    public static function bindTopic($value)
    {
        self::$ajur_adv_topic = $value;
    }

    /**
     * Заменяет в потоке данных в баннерах замещаемую переменную на значение поля $ajur_adv_topic
     *
     * Вызывается в методе render() для response-type == 'HTML'
     *
     * @todo: отключено, требует обдумывания "что же вставляем для каких типов контента"
     *
     * Если нам нужно будет подменять эти значения для других типов отдаваемого контента - нужно
     * добавить вызов в соотв. блоки
     */
    public static function bindBanners()
    {
        return null;
        self::$response['html'] = str_replace(self::$search_mask_puid40, self::$ajur_adv_topic, self::$response['html']);
    }


}

# -eof-