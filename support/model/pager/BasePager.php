<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 2016/10/30
 * Time: 12:09
 */

namespace support\model\pager;

use support\Object;

/**
 * Class BasePager
 * @package tuzhi\model\pager
 */
class BasePager extends Object
{

    /**
     * @var string
     */
    public $pageGetKey = 'page';

    /**
     * @var string
     */
    public $pageSizeGetKey = 'pageSize';


    /**
     * @var int
     */
    public $page = 1;


    /**
     * @var int
     */
    public $pageSize = 30;

    /**
     * @var int
     */
    public $listLength = 9;

    /**
     * @var
     */
    public $total = 0;

    /**
     * @var
     */
    public $totalPage;

    /**
     * @var
     */
    public $prevPage;

    /**
     * @var
     */
    public $nextPage;

    /**
     * @var
     */
    protected $data;

    /**
     * @var array
     */
    protected $listIndex = [];


    /**
     * @param int $page
     * @return $this
     */
    public function setPage(  $page)
    {
        $this->page = $page;
        return $this;
    }


    /**
     * @param $pageSize
     * @return $this
     */
    public function setPageSize(  $pageSize )
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    /**
     * @param int $total
     * @return $this
     */
    public function setTotal(  $total )
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @param int $length
     * @return $this
     */
    public function setListLength(  $length)
    {
        $this->listLength = $length;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function prepareTotalPage()
    {
        $this->totalPage = ceil( $this->total/$this->pageSize );
    }

    /**
     * @return mixed
     */
    protected function preparePage()
    {
        $page = $this->page;
        $page = $page <= 1 ? 1 : $page;
        $page = $this->totalPage && $page >= $this->totalPage ? $this->totalPage : $page;
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    protected function preparePrevAndNext()
    {
        $this->prevPage = max( 1, intval($this->page - 1) );
        $this->nextPage = min( $this->totalPage ,intval($this->page + 1) );
    }

    /**
     * @return mixed
     */
    protected function prepareList()
    {
        $page  = $this->page;
        $listLength = $this->listLength;
        $totalPage = $this->totalPage;

        if(($page - intval($listLength/2)) <= 1){
            $starIndex = 1;
            $endIndex   = $listLength > $totalPage ? $totalPage : $listLength;
        }elseif(( $page + intval($listLength/2) >= $totalPage)){
            $starIndex = $totalPage > $listLength ? $totalPage - $listLength + 1 : 1;
            $endIndex   = $totalPage;
        }else{
            $starIndex = $page - intval($listLength/2);
            $endIndex   = $page + intval($listLength/2);
        }
        $this->listIndex =[$starIndex,$endIndex];
    }

    /**
     * @return mixed
     */
    public function build()
    {
        $this->prepareTotalPage();
        $this->preparePage();
        $this->prepareList();
        $this->preparePrevAndNext();

    }

    /**
     * @param $page
     * @return mixed
     */
    protected function createNode(  $page )
    {
        $node['active'] = (int) $this->page === $page ? true : false;
        $node['page']   = $page;
        $node['href']   = $this->buildUrl($page);
        return $node;
    }

    /**
     * @return array
     */
    protected function buildList()
    {
        $star = $this->listIndex[0];
        $list =[];
        for(; $star <= $this->listIndex[1]; $star++) {
            $list[] =$this->createNode($star);
        }
        // 是否需要记录
        $this->data['list'] = $list;
        return $list;
    }

    /**
     * @param $page
     * @return string
     */
    protected function buildUrl($page)
    {
        return Url::build([$this->pageGetKey=>$page],true);
    }

    /**
     * @return mixed
     */
    public function getFirstPage()
    {
        return $this->createNode(1);
    }

    /**
     * @return mixed
     */
    public function getPrevPage()
    {
        return $this->createNode($this->prevPage);
    }

    /**
     * @return array
     */
    public function getListPage()
    {
        return $this->buildList();
    }

    /**
     * @return mixed
     */
    public function getNextPage()
    {
        return $this->createNode($this->nextPage);
    }

    /**
     * @return mixed
     */
    public function getLastPage()
    {
        return $this->createNode($this->totalPage);
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return mixed
     */
    public function getTotalPage()
    {
        return $this->totalPage;
    }

}