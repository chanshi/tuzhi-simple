<?php

/**
 *************************************************
 * 数据检验主类 
 *************************************************
 * 
 * @author 戒色禅师
 * @copyright 2012
 */

namespace support;

/**
 * Class ValidData
 * @package support
 * @see SkyMvc/lib/ValidData
 *
 * 改造下
 */
class ValidData {
    

    private $error;
    
    /*规则*/
    private $rule;

    /**
     * @var
     */
    protected $model;

    public function __construct( $model )
    {
        $this->model = $model;
    }


    /*添加规则*/
    public function AddRule($field,$type,$baseValue,$otherValue=null,$emsg=null){
        $rule = array(
            'field'=>$field,
            'Type'=>strtolower($type),
            'BValue'=>$baseValue,
            'OValue'=>$otherValue,
            'emsg'=>$emsg);
        $this->rule[]=$rule;
        return $this;
    }


    public function valid( $data ){
        return $this->ValidRule($data);
    }

    /**
     * 兼容 Model
     * @param $rules
     */
    public function setRules( $rules ) {
        foreach($rules as $rule)
        {
            $field = $rule[0];
            switch ($rule[1]){

                default :
                    $type  =$rule[1];
                    $baseValue = isset($rule[2]) ? $rule[2] : null;
                    break;
            }
            $this->AddRule($field, $type, $baseValue);
        }
    }
    
    /*规则校验*/
    public function ValidRule( $data ){
        // 如果校验的是为空 则 直接退出
        //if( empty($this->rule) || empty( $data)) return true;
        foreach( $this->rule as $value ){
            if( $this->ValidRuleValue( $value ,$data ) === false ){
                return false;
            }
        }
        // 是否清空错误
        return true;
    }
    
    /*独立校验 (function/callback/regex/in/equal/confirm/unique)   */
    private function ValidRuleValue( $rule , $data  ){
         $field = $rule['field'];
         switch( $rule['Type'] ){
            /*回调函数*/
            case 'callback' :   $args[] = $data[$field];
                                $args[] = $data;
                                if( ( $result =  call_user_func_array( $rule['BValue'] ,$args ) ) !== true ){
                                    $this->error = !empty($rule['emsg']) ? $rule['emsg'] : $result;
                                    return false;
                                }
                                return true;
                                break;
             case 'require' :

                 if( !isset( $data[$field] ) || $data[$field] == null ){
                     $this->error = '请输入'.$this->model->getLabel($field);
                     return false;
                 }
                 return true;
                 break;

            /*正则验证*/
            case 'regex'     :

                if( $this->regex( $data[$field] , $rule['BValue'] ) === false ){
                    switch( $rule['BValue'] ){
                        case 'require' :
                            $message = '请输入'.$this->model->getLabel($field);
                            break;
                        default :
                            $message = $this->model->getLabel($field).'值 不符合规定要求';
                            break;
                    }
                    $this->error = $message;
                    return false;
                }
                return true;
                break;
                                
                 default     :  return true;
         }
        
    }
    
    
   // 正则表达式校验数据部分 值 和 规则
    private function regex($value,$rule) {
        //if( empty( $value) || empty($rule) ) return true;
        $ruleKey = strtolower($rule);
        $validate = array(
            'require'   => '/.+/',
            'email'     => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url'       => '/^[http:\/\/]?[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
            'currency'  => '/^\d+(\.\d+)?$/',
            'number'    => '/^\d+$/',
            'zip'       => '/^[1-9]\d{5}$/',
            'integer'   => '/^[-\+]?\d+$/',
            'double'    => '/^[-\+]?\d+(\.\d+)?$/',
            'english'   => '/^[A-Za-z]+$/',
            'discount'  => '/^[0|1](\.[0-9]{0,2})?$/',
            'price'     => '/^\d{0,8}(\.[0-9]{0,2})?$/',
            'mobile'    => '/^\d{11}$/'
        );
        // 检查是否有内置的正则表达式
        $rule  = isset( $validate[$ruleKey] ) ?  $validate[$ruleKey]  : $rule;
        return preg_match($rule,$value) === 1;
    }
    
    /**
     * 查询错误信息
     */
    public function getMsg(){
        return $this->error;
    }


    /**
     * @return mixed
     */
    public function getFirstError()
    {
        return $this->error;
    }
    
}

?>