<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/15
 * Time: 00:06
 */

namespace support\database\query;

class DeleteQuery extends Query
{

    /**
     * DeleteQuery constructor.
     * @param null $table
     * @param array $config
     */
    public function __construct($table=null,array $config=[])
    {
        parent::__construct($config);

        if( $table ){
            $this->table($table);
        }
    }

    /**
     * @return mixed
     */
    public function getSqlString()
    {
        return $this->db->getQueryBuild()->buildDelete($this);
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        $sql = $this->getSqlString();

        return $this->db->createCommand($sql)->execute();
       
    }
}