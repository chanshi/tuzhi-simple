<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/12
 * Time: 23:45
 */

namespace support\database;


use Config;
use support\Object;

class Connection extends Object
{
    /**
     * @var
     */
    public $pdo;


    /**
     * @var string
     */
    public $pdoClass ='PDO';

    /**
     * @var
     */
    public $master;

    /**
     * @var
     */
    public $slave;

    /**
     * @var string
     */
    protected $commandClass = 'support\database\Command';

    /**
     * @var string
     */
    protected $schema = 'support\database\Schema';

    /**
     * @var
     */
    protected $transaction ;

    /**
     * @var
     */
    public $cache;


    /**
     * 配置
     */
    public function init()
    {
        if($this->master ) {
            $this->master = new Dsn( Config::get( $this->master )  );
        }

        if( $this->slave ){
            foreach($this->slave as $index=>$slave) {
                $this->slave[$index] = new Dsn( Config::get($slave) );
            }
        }
        if( $this->cache == null )
        {
            // 获取缓存 
        }
    }


    public function isActivity()
    {
        return ($this->pdo instanceof $this->pdoClass) ? true : false;
    }

    public function getTransaction()
    {
        if( $this->transaction == null )
        {
            $this->transaction = new Transaction(['db'=>$this]);
        }
        return $this->transaction;
    }

    public function runInTransaction()
    {
        return $this->getTransaction()->getLevel()  == 0
            ? false
            : true;
    }

    public function transaction( $callback )
    {
        $transaction = $this->getTransaction();
        $transaction->begin();
        $level = $transaction->getLevel();

        try{
            $result = call_user_func($callback,$this);

            if( $transaction->getLevel() == $level ){
                $transaction->commit();
            }
        }catch(\Exception $e){
            if( $transaction->getLevel() == $level ){
                $transaction->rollback();
            }
            throw $e;
        }
        return $result;
    }


    public function open()
    {
        if(  ! $this->isActivity() ){
            //TODO:: 不处理调度
            $this->pdo = $this->createConnection( $this->master );
        }
        return $this->pdo;
    }


    public function createConnection( $dsn )
    {
        if( $dsn instanceof $this->pdoClass){
            return $dsn;
        }
        if( $dsn instanceof Dsn){
            try{

                $instance = new $this->pdoClass($dsn->getDsn(),$dsn->getUserName(),$dsn->getPassword());

                $instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            }catch(\PDOException $e){
                throw $e;
            }
            return $instance;
        }
        throw new \Exception('Invalid Param Dsn in Connection ');
    }

    /**
     * @return mixed
     */
    public function getMaster()
    {
        if( ! $this->isActivity() ){
            $this->open();
        }
        return $this->pdo;
    }

    /**
     *  使用随机调度
     */
    public function getSlave()
    {
        if( empty($this->slave) ){
            return $this->getMaster();
        }

        $id = array_rand($this->slave);
        return $this->slave[$id] = $this->createConnection($this->slave[$id]);
    }


    /**
     * @param $sql
     * @return mixed
     */
    public function createCommand($sql = null)
    {
        return new $this->commandClass($sql,$this);
    }

    /**
     * @return string|Schema
     */
    public function getSchema()
    {
        if( ! ( $this->schema instanceof Schema ) ){

            $this->schema = new $this->schema( ['db'=>$this] );
        }
        return $this->schema;
    }

    /**
     * @return query\Builder
     */
    public function getQueryBuild()
    {
        return $this->getSchema()->getBuilder();
    }

    public function getTableSchema($table)
    {
        return $this->getSchema()->getTableSchema($table);
    }

    /**
     * @param $value
     * @return string
     */
    public function quoteValue( $value )
    {
        return $this->getSchema()
            ->quoteValue( $value );
    }

    /**
     * @param $table
     * @return string
     */
    public function quoteTable( $table )
    {
        return $this->getSchema()
            ->quoteTableName( $table );
    }

    /**
     * @param $column
     * @return mixed|string
     */
    public function quoteColumn( $column )
    {
        return $this->getSchema()
            ->quoteColumn( $column );
    }

    /**
     * @param $sql
     * @return bool
     */
    public function isQuerySql( $sql )
    {
        $pattern = '/^\s*(SELECT|SHOW|DESCRIBE)\b/i';
        return preg_match($pattern, $sql) > 0;
    }

    
    public function close()
    {
        if( $this->isActivity() ){
            $this->pdo->close();
        }
        //以及关闭 slave;
        $this->pdo =null;
    }

}