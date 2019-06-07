<?php
/**
 * Created 2019-06-07
 */

use Arris\AppLogger;
use SteamBoat\BBParser;


if (!function_exists('getEngineVersion')) {

    /**
     * Загружает версию движка из GIT
     *
     * @return array
     * @throws Exception
     */
    function getEngineVersion()
    {
        $version_file = getenv('INSTALL_PATH') . getenv('VERSION_FILE');
        $version = [
            'date'      =>  (new \DateTime())->format('r'),
            'user'      =>  'local',
            'summary'   =>  'latest'
        ];

        if (getenv('VERSION')) {
            $version['summary'] = getenv('VERSION');
        } elseif (is_readable($version_file)) {
            $array = file($version_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            $version = [
                'date'  =>  $array[1],
                'user'  =>  'local',
                'summary'   =>  $array[0]
            ];
        }

        return $version;
    }
}


if (!function_exists('create_BBParser')) {
    /**
     * BB Parsing method (v FontankaFi)
     * Используется ТОЛЬКО для юзерконтента
     *
     * @param $text
     * @param string $mode
     * @return string|string[]|null
     * @throws Exception
     */
    function create_BBParser($text, $mode = "posts", $youtube_enabled = false)
    {
        $sizes = array(
            "posts" => array(560, 340),
            "comments" => array(320, 205),
        );

        $bbparsersizes = $sizes[$mode];

        if (getenv('DEBUG_LOG_BBPARSER')) AppLogger::scope('main')->debug('BBParser | input data', [$text]);

        $parser = new BBParser();
        $parser->setText($text);
        $parser->parse();
        $text = $parser->getParsed();

        if (getenv('DEBUG_LOG_BBPARSER')) AppLogger::scope('main')->debug('BBParser | getParsed', [$text]);

        $text = preg_replace("/(\-\s)/i", "&mdash; ", $text);
        $text = preg_replace("/(\s\-\s)/i", " &mdash; ", $text);

        if (getenv('DEBUG_LOG_BBPARSER')) AppLogger::scope('main')->debug('BBParser | mdash replacement', [$text]);


        if ($youtube_enabled) {
            $text = preg_replace_callback("/\[\youtube](.*)\[\/youtube\]/i", function ($matches) use ($bbparsersizes) {
                $matches = parse_url($matches[1]);
                if (!preg_match("/v=([A-Za-z0-9\_\-]{11})/i", $matches["query"], $res)) {
                    if (!preg_match("/([A-Za-z0-9\_\-]{11})/i", $matches["fragment"], $res)) {
                        return false;
                    }
                }
                $matches = $res;
                return '
<div class="video-youtube">
    <object width="' . $bbparsersizes[0] . '" height="' . $bbparsersizes[1] . '">
        <param name="wmode" value="opaque" />
        <param name="movie" value="http://www.youtube.com/v/' . $matches[1] . '?fs=1&amp;hl=ru_RU&amp;rel=0&amp;color1=0x5d1719&amp;color2=0xcd311b">
        <param name="allowFullScreen" value="true">
        <param name="allowscriptaccess" value="always">
        <embed src="http://www.youtube.com/v/' . $matches[1] . '?fs=1&amp;hl=ru_RU&amp;rel=0&amp;color1=0x5d1719&amp;color2=0xcd311b" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' . $bbparsersizes[0] . '" height="' . $bbparsersizes[1] . '" wmode="opaque">
    </object>
</div>';
            }, $text);
        } // if

        if (getenv('DEBUG_LOG_BBPARSER')) AppLogger::scope('main')->debug('BBParser | after youtube check', [$text]);

        $text = preg_replace_callback("/([\(]{3,})/i", function ($m) {
            return "((( ";
        }, $text);
        $text = preg_replace_callback("/([\)]{3,})/i", function ($m) {
            return ")))";
        }, $text);
        $text = preg_replace_callback("/([\!]{3,})/i", function ($m) {
            return "!!!";
        }, $text);
        $text = preg_replace_callback("/([\?]{3,})/i", function ($m) {
            return "???";
        }, $text);

        if (getenv('DEBUG_LOG_BBPARSER')) AppLogger::scope('main')->debug('BBParser | ()!? check', [$text]);

        return $text;
    }
} // create_BBParser

if (!function_exists('d')) {
    /**
     * @param $value
     */
    function d($value)
    {
        echo '<pre>';
        /*foreach (func_get_args() as $arg) {
            var_dump($value);
        }*/
        var_dump($value);
        echo '</pre>';
    }
} // d

if (!function_exists('dd')) {
    /**
     * @param $value
     */
    function dd($value)
    {
        d($value);
        die;
    }
} // dd

if (!function_exists('intdiv')) {
    /**
     * intdiv() for PHP pre 7.0
     *
     * @param $p
     * @param $q
     * @return int
     */
    function intdiv($p, $q)
    {
        return (int)floor(abs($p / $q));
    }
}

if (!function_exists('pluralForm')) {
    /**
     *
     * @param int $number
     * @param array $forms (array or string with glues, x|y|z or [x,y,z]
     *
     * @param string $glue
     * @return mixed|null
     */
    function pluralForm($number, $forms, $glue = '|')
    {
        if (is_string($forms)) {
            $forms = explode($forms, $glue);
        } elseif (!is_array($forms)) {
            return null;
        }

        if (count($forms) != 3) return null;

        return
            $number % 10 == 1 && $number % 100 != 11 ?
                $forms[0] :
                ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20)
                    ? $forms[1]
                    : $forms[2]
                );
    }
}

if (!function_exists('convertUTF16E_to_UTF8')) {
    /**
     * Эта кодировка называется ISO-8859-1 и для неё есть штатные механизмы
     * https://secure.php.net/manual/ru/function.utf8-decode.php
     * и
     * https://secure.php.net/manual/ru/function.utf8-encode.php
     */


    /**
     * Переименовываем в convertUTF16E_to_UTF8
     *
     * @param $t
     * @return string|string[]|null
     */
    function convertUTF16E_to_UTF8($t)
    {
        // return $t;
        /*return preg_replace_callback('#%u([0-9A-F]{4})#s', function ($match) {
            return iconv("UTF-16E", 'UTF-8', pack('H4', $match[1]));
        }, $t);*/

        return preg_replace_callback('#%u([0-9A-F]{4})#s', function (){
            iconv("UTF-16BE","UTF-8", pack("H4","$1"));
        }, $t);
    }

}

if (!function_exists('redirect')) {

    /**
     * @param $uri
     * @param bool $redir
     * @param int $code
     */
    function redirect($uri, $redir = false, $code = 302) {
        $default_scheme = getenv('REDIRECT_DEFAULT_SCHEME') ?: 'http://';

        if (strstr($uri, "http://") or strstr($uri, "https://")) {
            header("Location: " . $uri, $redir, $code);
        } else {
            header("Location: {$default_scheme}{$_SERVER['HTTP_HOST']}{$uri}", $redir, $code);
        }
    }
}

if (!function_exists('logSiteUsage')) {

    /**
     * @param $scope
     * @param $method
     * @throws Exception
     */
    function logSiteUsage($scope, $method)
    {
        if (getenv('DEBUG_LOG_SITEUSAGE')) AppLogger::scope($scope)->notice("Usage: ", [
            round(microtime(true) - $_SERVER['REQUEST_TIME'], 3),
            memory_get_usage(),
            $method,
            $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
        ]);
    }
}

if (!function_exists('getimagepath')) {

    /**
     *
     *
     * @param string $type
     * @param null $cdate
     * @return string
     */
    function getimagepath($type = "photos", $cdate = null)
    {
        global $CONFIG;
        $directory_separator = DIRECTORY_SEPARATOR;

        $cdate = is_null($cdate) ? time() : strtotime($cdate);

        $path
            = getenv('INSTALL_PATH')
            . "www/i/"
            . $type
            . DIRECTORY_SEPARATOR
            . date("Y{$directory_separator}m", $cdate)
            . DIRECTORY_SEPARATOR;

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }
}