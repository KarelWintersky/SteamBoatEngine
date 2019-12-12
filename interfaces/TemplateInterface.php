<?php

namespace SteamBoat;

interface TemplateInterface {

    /**
     * Инициализирует враппер (статик шаблонизатора)
     *
     * @param $smarty   -- инстанс SMARTY
     * @param $that     -- инстанс класса логики
     */
    public static function init($smarty, $that, $options, $logger);

    /**
     * SMARTY Assign value
     *
     * @param $variable
     * @param $value
     */
    public static function assign($variable, $value);

    /**
     * Модифицирует META-данные
     *
     * @param $variable
     * @param $value
     */
    public static function addMeta($variable, $value);

    /**
     * Возвращает блок МЕТА-данных
     *
     * @return array
     */
    public static function getMeta();

    /**
     * Добавляет текст к массиву заголовков
     *
     * @param $title
     */
    public static function addTitle($title);

    /**
     * Возвращает отформатированный заголовок
     *
     * @return string
     */
    public static function getTitle():string;

    /**
     * Устанавливает режим рендера.
     *
     * Допустимые:
     * HTML - возвращается страница как HTML
     * JSON - возвращается страница в объекте [status, mode, data/page, html]
     * AJAX/AJAXHTML - чисто HTML в аяксе
     *
     * @param string $mode
     */
    public static function setResponseMode($mode = 'HTML');

    /**
     * Рендерит шаблон на основе режима и возвращает строку
     *
     * @return string
     * @throws \SmartyException
     */
    public static function render();

    /**
     * Устанавливает значение $ajur_adv_topic для таргетирования баннера по URL
     *
     * @param $value
     */
    public static function bindTopic($value);
}