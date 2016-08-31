<?php
/**
 * Created by PhpStorm.
 * User: zhaoye
 * Date: 16/7/16
 * Time: 下午10:17
 */


namespace ZPHP\Model;



use ZPHP\Core\Log;
use ZPHP\Db\Db;

class Model {

    public $db;
    public $table='';
    public $select='*';
    public $where='';
    public $lastSql='';

    public $join='';
    public $use_index='';
    public $union='';
    public $group='';
    public $having='';
    public $order='';
    public $limit='';
    public $for_update='';
    // 数据库表达式
    protected $exp = array('eq'=>'=','neq'=>'<>','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','notlike'=>'NOT LIKE','like'=>'LIKE','in'=>'IN','notin'=>'NOT IN','not in'=>'NOT IN','between'=>'BETWEEN','not between'=>'NOT BETWEEN','notbetween'=>'NOT BETWEEN');

    function __construct(Db $Db, $db_key = 'master'){
        $this->db = $Db->getDb($db_key);
    }


    //执行查询部分
    public function query($sql){
        $sql = trim($sql);
        Db::getInstance()->setSql($sql);
        $pdo = $this->db->prepare($sql);
        $res = $pdo->execute();
        if($res){
            $data =  strtolower($sql[0])!=='s'?$pdo->rowCount():$pdo->fetchall();
            $pdo->closeCursor();
            return $data;
        }else{
            return $res;
        }
    }


    /**
     * value分析
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value) {
        if(is_string($value)) {
            $value =  strpos($value,':') === 0 && in_array($value,array_keys($this->bind))? $this->escapeString($value) : '\''.addslashes($value).'\'';
        }elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){
            $value =  addslashes($value[1]);
        }elseif(is_array($value)) {
            $value =  array_map(array($this, 'parseValue'),$value);
        }elseif(is_bool($value)){
            $value =  $value ? '1' : '0';
        }elseif(is_null($value)){
            $value =  'null';
        }
        return $value;
    }



    /*
     * 根据where解析查询条件
     * @zhaoye
     */
    function where($where){
        $whereStr = '';
        if(is_string($where)) {
            // 直接使用字符串条件
            $whereStr = $where;
        }else{ // 使用数组表达式
            // 默认进行 AND 运算
            $operate    =   ' AND ';
            foreach ($where as $key=>$val){
                if($key=='_string') {
                    // 解析特殊条件表达式
                    $whereStr   .=  '( '.$val.' )';
                }else{
                    $whereStr .= $this->parseWhereItem($key,$val);
                }
                $whereStr .= $operate;
            }
            $whereStr = substr($whereStr,0,-strlen($operate));
        }
        $this->where =  empty($whereStr)?'':' where '.$whereStr;
        return $this;
    }


    // where子单元分析
    protected function parseWhereItem($key,$val) {
        $whereStr = '';
        if(is_array($val)) {
            if(is_string($val[0])) {
                $exp	=	strtolower($val[0]);
                if(preg_match('/^(eq|neq|gt|egt|lt|elt)$/',$exp)) { // 比较运算
                    $whereStr .= $key.' '.$this->exp[$exp].' '.$this->parseValue($val[1]);
                }elseif(preg_match('/^(notlike|like)$/',$exp)){// 模糊查找
                    if(is_array($val[1])) {
                        $likeLogic  =   isset($val[2])?strtoupper($val[2]):'OR';
                        if(in_array($likeLogic,array('AND','OR','XOR'))){
                            $like       =   array();
                            foreach ($val[1] as $item){
                                $like[] = $key.' '.$this->exp[$exp].' '.$this->parseValue($item);
                            }
                            $whereStr .= '('.implode(' '.$likeLogic.' ',$like).')';
                        }
                    }else{
                        $whereStr .= $key.' '.$this->exp[$exp].' '.$this->parseValue($val[1]);
                    }
                }elseif('bind' == $exp ){ // 使用表达式
                    $whereStr .= $key.' = :'.$val[1];
                }elseif('exp' == $exp ){ // 使用表达式
                    $whereStr .= $key.' '.$val[1];
                }elseif(preg_match('/^(notin|not in|in)$/',$exp)){ // IN 运算
                    if(isset($val[2]) && 'exp'==$val[2]) {
                        $whereStr .= $key.' '.$this->exp[$exp].' '.$val[1];
                    }else{
                        if(is_string($val[1])) {
                            $val[1] =  explode(',',$val[1]);
                        }
                        $zone      =   implode(',',$this->parseValue($val[1]));
                        $whereStr .= $key.' '.$this->exp[$exp].' ('.$zone.')';
                    }
                }elseif(preg_match('/^(notbetween|not between|between)$/',$exp)){ // BETWEEN运算
                    $data = is_string($val[1])? explode(',',$val[1]):$val[1];
                    $whereStr .=  $key.' '.$this->exp[$exp].' '.$this->parseValue($data[0]).' AND '.$this->parseValue($data[1]);
                }else{
                    throw new \Exception("表达式错误");
                }
            }else {
                $count = count($val);
                $rule  = isset($val[$count-1]) ? (is_array($val[$count-1]) ? strtoupper($val[$count-1][0]) : strtoupper($val[$count-1]) ) : '' ;
                if(in_array($rule,array('AND','OR','XOR'))) {
                    $count  = $count -1;
                }else{
                    $rule   = 'AND';
                }
                for($i=0;$i<$count;$i++) {
                    $data = is_array($val[$i])?$val[$i][1]:$val[$i];
                    if('exp'==strtolower($val[$i][0])) {
                        $whereStr .= $key.' '.$data.' '.$rule.' ';
                    }else{
                        $whereStr .= $this->parseWhereItem($key,$val[$i]).' '.$rule.' ';
                    }
                }
                $whereStr = '( '.substr($whereStr,0,-4).' )';
            }
        }else {
            $whereStr .= $key.' = '.$this->parseValue($val);
        }
        return $whereStr;
    }


    /**
     * 获取表的所有数据
     * @return array
     */
    public function get(){
        return $this->query($this->makesql());
    }


    /**
     * 获取表的一条数据
     * @return array or null
     */
    public function find(){
        $this->limit(1);
        $data = $this->query($this->makesql());
//        Log::write('data:'.json_encode($data));
        return empty($data[0])?null:$data[0];
    }


    /**
     * 查询的条数
     * @param $limit
     * @return null
     */
    function limit($limit)
    {
        if (!empty($limit))
        {
            $_limit = explode(',', $limit, 2);
            if (count($_limit) == 2)
            {
                $this->limit = 'limit ' . (int)$_limit[0] . ',' . (int)$_limit[1];
            }
            else
            {
                $this->limit = "limit " . (int)$limit;
            }
        }
        else
        {
            $this->limit = '';
        }
    }

    //生成sql
    protected function makesql(){
        $sql = "select {$this->select} from {$this->table}";
        $sql .= implode(' ',
            array(
                $this->join,
                $this->use_index,
                $this->where,
                $this->union,
                $this->group,
                $this->having,
                $this->order,
                $this->limit,
                $this->for_update,
            ));

//        Log::write('sql:'.$sql);
        return $sql;
    }
}