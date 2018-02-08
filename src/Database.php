<?php

namespace src;

use PDO;
use PDOException;

/**
 * Class Database
 * @package src
 */
class Database
{
    /**
     * @var PDO
     */
    private static $dbh;

    /**
     * @var Logger 
     */
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * @return PDO
     */
    public static function getConnection(){

        $dbConfig = Config::get('db');
        $host = $dbConfig['host'];
        $dbName = $dbConfig['dbname'];
        $userName = $dbConfig['username'];
        $password = $dbConfig['passwd'];



        if(!self::$dbh){
            try {
                $dsn = 'mysql:host=' . $dbConfig['host']. ';dbname=' . $dbConfig['dbname'];
                self::$dbh = new PDO($dsn, $dbConfig['username'], $dbConfig['passwd'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            } catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
        }
        
        return self::$dbh;
    }

    /**
     * @param $search
     * @return array
     */
    public function searchAllDb($search){
        $dbh = self::getConnection();
        $tables = $this->getTables();
        $results = [];

        $this->logger->log('scanning database');

        foreach($tables as $row) {
            $table = $row[0];

            $this->logger->log('process table - ' . $table);

            $sqlSearch = "SELECT * FROM `".$table."` WHERE ";
            $columns = $this->getColumns($table);

            $sqlSearchFields = [];
            foreach ($columns as $row2){
                $column = $row2['Field'];
                $sqlSearchFields[] = "`".$column."` LIKE '%".$search."%'";
            }

            $sqlSearch .= implode(" OR ", $sqlSearchFields);
            $records = $dbh->query($sqlSearch)->fetchAll(PDO::FETCH_ASSOC);

            if(count($records)){
                $results[] = [
                    'table' => $table,
                    'sql' => $sqlSearch,
                    'count' => count($records),
                    //'records' => $records,
                ];
            }
        }

        return $results;
    }

    /**
     * @return mixed
     */
    private function getTables(){
        $dbh = self::getConnection();
        $tables = $dbh->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);
        return $tables;
    }

    /**
     * @param $table
     * @return array
     */
    private function getColumns($table){
        $dbh = self::getConnection();
        $sql = 'SHOW COLUMNS FROM ' . $table;
        $columns = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $columns;
    }
}