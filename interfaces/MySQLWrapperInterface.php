<?php

namespace SteamBoat;

use Exception;
use mysqli_result;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

interface MySQLWrapperInterface
{
    /**
     * MySQLWrapper constructor.
     *
     * @param $config
     * @param $logger
     * @param PDO $pdo_connector
     */
    public function __construct($config, PDO $pdo_connector, LoggerInterface $logger = null);

    /**
     * Коннект к базе
     */
    public function connect();

    /**
     * Закрыть соединение с БД
     */
    public function close();

    /**
     * Выполнить sql-запрос
     *
     * @param $query
     * @param bool $log_sql_request
     * @return bool|mysqli_result
     */
    public function query($query, $log_sql_request = false);

    /**
     * множественный запрос в базу
     *
     * @param $query
     * @param bool $debug
     * @return bool
     */
    public function multi_query($query, $debug = false);

    /**
     * Вернуть ассоциативный массив как результат
     *
     * @param $res
     * @param $row
     * @return mixed
     */
    public function result($res, $row);

    /**
     * получение данных
     *
     * @param $result
     * @return array|null
     */
    public function fetch($result);

    /**
     * Возвращает количество строк в результате запроса
     *
     * @param $res
     * @return int
     */
    public function num_rows($res);

    /**
     * Возвращает последний ID затронутый запросом
     *
     * @return int|string
     */
    public function insert_id();

    /**
     * Создает SQL-запрос на основе множества параметров
     * Warning: GOD METHOD
     *
     * @param $fields
     * @param $table
     * @param null $hash
     * @param null $joins
     * @param bool $needpages
     * @return string
     */
    public function create($fields, $table, $hash = null, $joins = null, $needpages = true):string;

    /**
     * Выполнить запрос через PDO, коннектор - передан в конструктор класса
     *
     * @param $query
     * @param $dataset
     * @return bool
     * @throws Exception
     */
    public function pdo_query(string $query, array $dataset);
    
    /**
     * Возвращает PDO Statement
     *
     * @return bool|PDOStatement
     */
    public function pdo_result();
    
    /**
     * Возвращает одну строку из результата запроса
     *
     * @param int $row
     * @return array|mixed
     */
    public function pdo_fetch($row = 0);
    
    /**
     * Возвращает колонку из результата запроса
     *
     * @param int $column
     * @param null $default
     * @return mixed|null
     */
    public function pdo_fetch_column($column = 0, $default = null);
    
    /**
     * Возвращает все строки из результата запроса
     *
     * @return array
     */
    public function pdo_fetch_all();
    
    /**
     * Last insert id сделанный через PDO-коннекшен
     *
     * @param null $name
     * @return string
     */
    public function pdo_last_insert_id($name = null);

    /**
     * Возвращает количество сделанных запросов
     *
     * @return int
     */
    public function getQueryCount();

    /**
     * Возвращает время, затраченное на запросы
     *
     * @return float
     */
    public function getQueryTime();
}