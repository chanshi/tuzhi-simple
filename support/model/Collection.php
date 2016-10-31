<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/16
 * Time: 17:06
 */

namespace support\model;
use support\database\query\Query;

use support\model\pager\BasePager;

/**
 * Class Collection
 * @package support\model
 */
class Collection extends Model
{
    /**
     * @var string 页
     */
    protected $pagerClass = 'support\model\pager\BasePager';

    /**
     * @var Query
     */
    protected $Query;

    /**
     * @var BasePager
     */
    public $Pager;

    /**
     * @var bool
     */
    protected $enablePage = true;

    /**
     * @var array
     */
    protected $dataCollection =[];

    /**
     * @var int
     */
    protected $page =1;

    /**
     * @var int
     */
    protected $pageSize = 30;


    /**
     * 初始化
     */
    public function init()
    {
        parent::init();

        $this->Query = \DB::Query();
        $this->Pager = new $this->pagerClass();

    }

    /**
     * @param $enable
     * @return $this
     */
    public function setEnablePager( $enable )
    {
        $this->enablePage = (boolean) $enable;
        return $this;
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @return $this
     */
    public function setPage( $page = 1 , $pageSize = 30 )
    {
        $this->page = max(1,$page);
        $this->pageSize = max(1, min(30,$pageSize) );
        return $this;
    }

    /**
     * @return mixed
     */
    public function buildQuery(){}


    /**
     *  默认查询
     */
    public function query()
    {
        $this->buildQuery();
        if( $this->enablePage && $this->Query instanceof Query) {
            $this->Query->limit(
                ($this->page - 1) * $this->pageSize,
                $this->pageSize
            );

            $this->Pager->setTotal($this->Query->count())
                ->setPage($this->page)
                ->setPageSize($this->pageSize)
                ->build();
        }
        $this->dataCollection = $this->Query->all();
        return $this->dataCollection;
    }


    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this->dataCollection );
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->Query;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->dataCollection;
    }

}