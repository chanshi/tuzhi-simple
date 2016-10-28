<?php
/**
 * Created by PhpStorm.
 * User: 吾色禅师<wuse@chanshi.me>
 * Date: 16/5/16
 * Time: 17:09
 */

namespace support;
use support\helper\Url;

/**
 * Class Page
 * @see 早期封装的类
 */
class Pager {

    // 数据总量
    protected $rowsCount = 0;
    //每页显示数
    protected $rowList = 10;
    //当前页码
    protected $page = 1;
    // 前一页
    protected $prevPage = 0;
    // 下一页
    protected $nextPage = 0;
    // 尾页
    protected $lastPage = 0;
    // 显示页码个数 可以为偶数 也可以为基数
    protected $showNum = 9;

    // 页码部分 起始编号 和终结编号
    protected $listFrom ;

    protected $listTo;


    //初始化函数  当前页 数据总量 每页显示数
    public function init($rowsCount,$rowList = 10,$page = 1){
        // 初始化两个值
        $this->rowsCount = intval($rowsCount);
        $this->rowList = intval($rowList);
        // 算出 最后一页
        $this->countLastPage($rowsCount,$rowList);
        // 校验页码
        $this->parsePage($page);
        // 则计算出 最后一页
        $this->countPrevNext();
    }

    // 可以设置每页显示个数
    public function setRowList($rowList){
        return $this->rowList = intval($rowList);
    }

    // 设置页码个数
    public function setShowNum($showNum){
        return $this->showNum = intval($showNum);
    }
    // 设置页码显示类别
    public function setShowStyle($type){
        return $this->showStyle = strtolower($type);
    }

    //计算出 当前页码总数 也就是 页码总量
    private function countLastPage($rowsCount = '',$rowList = ''){
        $rowsCount = $rowsCount === '' ? $this->rowsCount : $rowsCount ;
        $rowList = $rowList === '' ? $this->rowList : $rowList;
        // 如果没有值 则返回错误
        if(empty($rowList) || empty($rowsCount)) return false;
        $countPage = ceil($rowsCount/$rowList);
        return $this ->lastPage = $countPage;
    }

    //计算出上下页 当前页码 和最后的页码
    private function countPrevNext($page = '', $last = ''){
        $page = ($page == '') ? $this->page : $page;
        $last = ($last == '') ? $this->lastPage : $last;
        if( empty($last) ) $this->countLastPage();
        $this->prevPage = intval($page - 1) <= 1 ? 1 : intval($page - 1);
        $this->nextPage = intval($page + 1) >= $last ? $last : intval($page +1);
    }

    // 校验页码是否超出了 正确的范围
    private function parsePage($page = '',$last = ''){
        $page = $page == '' ? $this->page : $page;
        $last = $last == '' ? $this->lastPage : $last;
        if(empty($last)) $this->countLastPage();
        $page = ($page <= 1)  ? 1 : $page;
        $page = ($page >= $last) ? $last :$page;
        return $this->page = $page;
    }
    // 表现层？

    //建立一个表现层的处理情况
    //  首页 上一页  ... 4 5 6 7 8 9 10 ... 下一页 尾页
    //生成 页码链条 中间的 根据当前页码生成
    protected function creatPageList($showNum = ''){
        $pagelist = array();
        $page    = $this->page;
        $showNum = $showNum == '' ? $this->showNum : $showNum;
        // 未计算的话 计算下值
        if(empty($this->lastPage)) $this->countLastPage();
        // 如果 超过的话 则 开始浮动 是根据页码进行的
        if(($page - intval($showNum/2)) <= 1){
            $from = 1;
            $to   = $showNum > $this->lastPage ? $this->lastPage : $showNum;
        }elseif(($page+intval($showNum/2) >= $this->lastPage)){
            $from = $this->lastPage > $showNum ? $this->lastPage - $showNum + 1 : 1;
            $to   = $this->lastPage;
        }else{
            $from = $page - intval($showNum/2);
            $to   = $page + intval($showNum/2);
        }
        // 齐了 其实的标号 和结尾的标号
        $this->listFrom = $from;
        $this->listTo = $to;
    }

    // HTML 显示
    public function toShow($showNum='',$style = ''){
        $style = $style == '' ? $this->showStyle : $style;
        $this->creatPageList($showNum);
        $showFunc = strtolower($style)."Html";
        $result = $this->$showFunc();
        return  $result;
    }

    // TODO拼装 URL
    protected function creatUrl($page){
        return Url::setGET(['page'=>$page],true);
    }

    protected function createList(){
        $result = array();
        for( $i = $this->listFrom;$i<= $this->listTo;$i++ ){
            $save = array();
            $save['selected'] = ($i == $this->page) ? true : false;
            $save['text'] = $i;
            $save['url'] = $this->creatUrl($i);
            $result[] = $save;
        }
        return $result;
    }
    /*fan*/
    public function getData( $showNum = 9 ){
        $this->creatPageList($showNum);
        $result['first']       = array('text'=>1,'url'=>$this->creatUrl(1));
        $result['prev']        = array('text'=>$this->prevPage,'url'=>$this->creatUrl( $this->prevPage ));
        $result['list']        = $this->createList();
        $result['next']        = array('text'=>$this->nextPage,'url'=>$this->creatUrl( $this->nextPage ));
        $result['last']        = array('text'=>$this->lastPage,'url'=>$this->creatUrl( $this->lastPage ));
        $result['total']       = $this->lastPage;
        $result['page']        = $this->page;
        $result['count'] = $this->rowsCount;
        $result['pageSize'] = $this->rowList;
        return $result;
    }
    
}//类定义结束