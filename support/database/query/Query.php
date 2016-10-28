<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/14
 * Time: 22:27
 */

namespace support\database\query;

use support\Object;

use support\database\query\section\GroupTrait;
use support\database\query\section\HavingTrait;
use support\database\query\section\LimitTrait;
use support\database\query\section\OrderTrait;
use support\database\query\section\SelectTrait;
use support\database\query\section\TableTrait;
use support\database\query\section\WhereTrait;

use DB;

class Query extends Object
{

    public $db;

    const _AS ='AS';

    const JOIN ='JOIN';
    const LEFTJOIN ='LEFT JOIN';
    const RIGHTJOIN='RIGHT JOIN';

    const F_COUNT = 'COUNT';
    const F_SUM = 'SUM';
    const F_AVG = 'AVG';

    const ASC = 'ASC';
    const DESC ='DESC';

    const _AND ='AND';
    const _OR  ='OR';

    const EQ  = '=';
    const NEQ = '<>';
    const GT  = '>';
    const GE  = '>=';
    const LT  = '<';
    const LE  = '<=';

    const BETWEEN = 'BETWEEN';
    const NOT_BETWEEN = 'NOT BETWEEN';

    const LIKE = 'LIKE';
    const NOT_LIKE = 'NOT LIKE';

    const IS_NULL = 'IS NULL';
    const IS_NOT_NULL = 'IS NOT NULL';

    const IN ='IN';
    const NOT_IN ='NOT IN';

    const EXISTS ='EXISTS';
    const NOT_EXISTS = 'NOT EXISTS';

    const REGEXP ='REGEXP';
    const NOT_REGEXP= 'NOT REGEXP';

    const UNIQUE ='UNIQUE';


    /**
     * Select
     */
    use SelectTrait;

    /**
     * Table
     */
    use TableTrait;

    /**
     * Where
     */
    use WhereTrait;

    /**
     *
     */
    use GroupTrait;

    /**
     *
     */
    use HavingTrait;

    /**
     *
     */
    use OrderTrait;

    /**
     *
     */
    use LimitTrait;


    public function init()
    {
        if( $this->db == null ){
            $this->db = DB::getDb();
        }
    }

    /**
     * @return mixed
     */
    public function one()
    {
        return $this->db->createCommand( $this->getSqlString() )->queryOne();
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->db->createCommand( $this->getSqlString() )->queryAll();
    }

    /**
     * @param string $column
     * @return mixed
     */
    public function count( $column = '*' )
    {
        return $this->queryScalar("count($column)");
    }

    /**
     *
     * @param $column
     * @return mixed
     */
    public function sum( $column )
    {
        $column = $this->db->quoteColumn($column);
        return $this->queryScalar("SUM({$column})");
    }

    /**
     * 统计值
     * @param $selectExpression
     * @return mixed
     */
    public function scalarExpression(  $selectExpression )
    {
        $select = $this->select;
        $limit = $this->limit;

        $this->limit = null;
        if( empty($this->group) && empty($this->having) ){
            if( is_array($selectExpression) ){
                $this->select = $selectExpression;
            }else{
                $this->select = [$selectExpression];
            }
            $sql = $this->db->getQueryBuild()->build($this);
        }else{
            $Query = (new Query())
                ->select($selectExpression)
                ->table($this ,'a');
            $sql = $this->db->getQueryBuild()->build($Query);
        }

        $this->select = $select;
        $this->limit = $limit;

        return $this->db->createCommand($sql)->queryOne();
    }

    /**
     * @param $selectExpression
     * @return mixed
     */
    protected function queryScalar( $selectExpression )
    {
        $select = $this->select;
        $limit = $this->limit;

        $this->limit = null;

        if( empty( $this->group) && empty($this->having) ){
            $this->select = [$selectExpression];
            $sql = $this->db->getQueryBuild()->build($this);
        }else{
            $Query = (new Query())
                ->select(DB::Expression( $selectExpression ))
                ->table($this,'a');
            $sql = $this->db->getQueryBuild()->build($Query);
        }

        $this->select = $select;
        $this->limit = $limit;
        return $this->db->createCommand($sql)->queryScalar();

    }

    /**
     * @return mixed
     */
    public function getSqlString()
    {
        return $this->db->getQueryBuild()->build($this);
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->getSqlString();
    }

}