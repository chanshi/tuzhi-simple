<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/13
 * Time: 00:04
 */
namespace support\database;


use support\Object;

class Dsn extends Object
{
    /**
     * @var
     */
    public $driver;

    /**
     * @var
     */
    public $host = 'localhost';

    /**
     * @var int
     */
    public $port = 3306;

    /**
     * @var
     */
    public $schema;

    /**
     * @var string
     */
    public $charset = 'UTF8';

    /**
     * @var
     */
    public $userName;

    /**
     * @var
     */
    public $password;

    /**
     * @var
     */
    public $dsn;

    

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDsn();
    }

    /**
     * @return string
     */
    public function getDsn()
    {
        if( $this->dsn == null ){
            $this->dsn = $this->driver .
                ':host='   . $this->host .
                ';port='   . $this->port .
                ';dbname=' . $this->schema .
                ';charset='. $this->charset;
        }
        return $this->dsn;
    }

}