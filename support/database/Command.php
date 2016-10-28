<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/12
 * Time: 23:54
 */

namespace support\database;

/**
 * Class Command
 * @package support\database
 */
class Command
{
    /**
     * @var
     */
    public $db;

    /**
     * @var
     */
    public $sql;

    /**
     * @var
     */
    public $statement;

    /**
     * Command constructor.
     * @param $sql
     * @param $db
     */
    public function __construct( $sql ,$db )
    {
        $this->sql = $sql;

        $this->db  = $db;
    }

    /**
     * @param bool $isRead
     * @throws \Exception
     */
    public function prepare( $isRead = true )
    {
        if( ! $this->db->isActivity() ){
            $this->db->open();
        }

        if( $this->db->runInTransaction() ){
            $pdo = $this->db->pdo;
        }else{
            $pdo = $isRead
                ? $this->db->getSlave()
                :$this->db->getMaster();
        }

        try{
            $this->statement = $pdo->prepare($this->sql);
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function query()
    {
        return $this->queryInterval('fetchAll');
    }

    /**
     * @param null $fetchMode
     * @return mixed
     * @throws \Exception
     */
    public function queryOne($fetchMode = null)
    {
        return $this->queryInterval('fetch',$fetchMode);
    }

    /**
     * @param null $fetchMode
     * @return mixed
     * @throws \Exception
     */
    public function queryAll($fetchMode = null)
    {
        return $this->queryInterval('fetchAll',$fetchMode);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function queryScalar()
    {
        $result = $this->queryInterval('fetchColumn',0);
        //stream_get_contents
        return $result;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function execute()
    {
        $this->prepare(false);

        try{

            $this->statement->execute();

            $num = $this->statement->rowCount();

        }catch(\Exception $e){
            $message = $e->getMessage() . "<br>Failed to query SQL: $this->sql";
            //$errorInfo = $e instanceof \PDOException ? $e->errorInfo : null;
            throw new \Exception($message);

        }
        return $num;
    }
    

    /**
     * @param $method
     * @param int $fetchMode
     * @return mixed
     * @throws \Exception
     */
    protected function queryInterval( $method ,$fetchMode = \PDO::FETCH_ASSOC )
    {
        $this->prepare(true);

        if($fetchMode === null){
            $fetchMode = \PDO::FETCH_ASSOC;
        }

        try{

            $this->statement->execute();

            $result = call_user_func_array([$this->statement,$method],[$fetchMode]);

            $this->statement->closeCursor();

        }catch( \Exception $e){
            $message = $e->getMessage() . "<br>Failed to query SQL: $this->sql";
            //$errorInfo = $e instanceof \PDOException ? $e->errorInfo : null;
            throw new \Exception($message);
        }

        return $result;
    }

}