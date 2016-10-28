<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 21:55
 */

namespace support\database;


use support\Object;

class Transaction extends Object
{
    const READ_UNCOMMITTED = 'READ UNCOMMITTED';

    const READ_COMMITTED = 'READ COMMITTED';

    const REPEATABLE_READ = 'REPEATABLE READ';

    const SERIALIZABLE = 'SERIALIZABLE';

    public $db;

    private $level = 0;

    public function begin( $isolation = Transaction::REPEATABLE_READ )
    {
        if( !$this->db->isActivity()  ){
            $this->db->open();
        }

        if( $this->level == 0 ){
            $this->db->getSchema()
                ->setTransactionLevel( $isolation );

            $this->db->pdo->beginTransaction();
            $this->level = 1;
            return true;
        }

        $this->db->getSchema()
            ->createSavePoint( 'POINT_'.$this->level );
        $this->level++;
        return true;
    }

    public function rollback()
    {
        if( ! $this->db->isActivity() ){
            return false;
        }

        $this->level--;
        if($this->level == 0){
            $this->db->pdo->rollback();
            return true;
        }

        $this->db->getSchema()
            ->rollBackSavePoint('POINT_'.$this->level);
        return true;
    }

    public function commit()
    {
        if( ! $this->db->isActivity() ){
            return false;
        }

        $this->level--;
        if($this->level == 0){
            $this->db->pdo->commit();
            return true;
        }

        $this->db->getSchema()
            ->releaseSavePoint('POINT_'.$this->level);
    }

    public function getLevel()
    {
        return $this->level;
    }
}