<?php
$str = 'echo "hello"';

var_dump(explode('^%&', 'fsdfds'));
var_dump(jiemi(StrToBin($str)));
$arr = preg_match('/(?<!^)(?!$)/u', $str);
    function StrToBin($str){
        //1.列出每个字符
        $arr = preg_split('/(?<!^)(?!$)/u', $str);
        //2.unpack字符
        $ret = [];
        foreach($arr as &$v){
            $temp = unpack('H*', $v);
            $v = base_convert($temp[1], 16, 2);
            $_str_arr = [];
            array_push($_str_arr, strlen($v));
            for($i = 0; $i < strlen($v); $i++) {
              if ($v[$i] == '1') {
                array_push($_str_arr, $i);
              }
            }
            array_push($ret, $_str_arr);
            unset($temp);
        }
        $ret_str = '';
        foreach($ret as $k => $v) {
          foreach($v as $kk => $vv) {
            $ret_str .= $vv . 'O';
          }
          $ret_str = rtrim($ret_str, 'O');
          $ret_str .= 'PHPpackJM1.0';
        }
        $ret_str = rtrim($ret_str, 'PHPpackJM1.0');
        return $ret_str;
    }

    function jiemi($str) {
      $arr = explode('PHPpackJM1.0', $str);
      $ret = [];
      foreach($arr as $k => $v) {
        $_arr = explode('O',$v);
        $len = array_shift($_arr);
        $first = array_shift($_arr);
        $_str = '';
        for($i =0; $i < $len; $i++) {
          if ($i == $first) {
            $_str .= '1';
            $first = array_shift($_arr);
          }else {
            $_str .= '0';
          }
        }
        array_push($ret, $_str);
      }
      foreach($ret as &$v){
          $v = pack("H".strlen(base_convert($v, 2, 16)), base_convert($v, 2, 16));
      }
      return join('', $ret);
    }


