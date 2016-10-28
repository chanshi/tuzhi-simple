<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/16
 * Time: 17:06
 */

namespace support\model;
use support\database\query\Query;

/**
 * Class Collection
 * @package support\model
 */
class Collection extends Model
{
    /**
     * @var string 页
     */
    protected $pagerClass = 'support\Pager';

    /**
     * 保护字段
     *
     * @var array
     */
    protected $denyAtt = ['data','pager','Pager','page','pageSize'];

    /**
     * @var 查询
     */
    protected $Query;

    /**
     * @var 页码
     */
    public $Pager;

    protected $enablePage = true; 


    /**
     * 初始化
     */
    public function init()
    {
        parent::init();

        $this->Query = \DB::Query();
        $this->Pager = new $this->pagerClass();
        $this->attributes['data'] = [];
        $this->setPage();
    }

    /**
     * @param $attribute
     * @return mixed|null
     */
    public function getFormatAtt($attribute)
    {
        $value = isset($this->attributes['data'][$attribute])
            ? $this->attributes['data'][$attribute]
            : null ;

        if(isset($this->attFormat[$attribute])){
            return call_user_func($this->attFormat[$attribute] ,$value);
        }

        return $value ;
    }

    /**
     * 设置是否分页
     */
    public function setEnabledPage($status){
        if (false !== $status) {
            $this->enablePage = true;
        }else{
            $this->enablePage = false;
        }
    }

    /**
     * 设置页码
     *
     * @param $page
     * @param $pageSize
     */
    public function setPage( $page = 1 , $pageSize = 30 )
    {
        $page = max(1,$page);
        $pageSize = min(30,$pageSize);  

        $this->attributes['page'] = $page;
        $this->attributes['pageSize'] =  $pageSize;
    }

    /**
     *
     * @return Query
     */
    public function buildQuery(){}

    /**
     * 初始化
     */
    public function buildPager()
    {
        if( $this->Query instanceof Query && $this->enablePage){
            $this->Query->limit( ($this->page -1 ) * $this->pageSize,$this->pageSize);
            $this->Pager->init( $this->Query->count() ,$this->pageSize,$this->page );
            $this->Pager = $this->Pager->getData();
        }
    }

    /**
     *  默认查询
     */
    public function query()
    {
        $this->buildQuery();
        $this->buildPager();
        $this->attributes['data'] = $this->Query->all();
        return $this->attributes['data'];
    }

    /**
     * 可打印SQL
     *
     * @return 查询
     */
    public function getQuery()
    {
        return $this->Query;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this->attributes['data'] );
    }

}