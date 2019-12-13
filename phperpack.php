<?php
echo token_name(377);
$entry = './index.php';
new Handle($entry, './output.php');

class Handle {
  private $preprocessing; // 预处理文件内容
  private $afterContent; // 处理后的内容
  private $queue = []; // 语法队列
  private $queueName = [];

  function __construct($filename, $outputPath) {
    $this->handle($filename);
    // $this->parse();
    $this->output($outputPath);
  }
  /**
   * 预处理文件内容
   **/
  private function handle($filePath, $randomName = '') {
    if (!$randomName) {
      $randomName = '_'.(string)mt_rand(1000000,9999999);
    }
    $this->queueName[] = $randomName;
    $this->queue[$randomName] = [];
    $content = file_get_contents(__DIR__.rtrim(ltrim($filePath,'\'.'),'\''));
    $temp = [];
    var_dump(token_get_all($content));
    foreach (token_get_all($content) as $k => $v) {
      if (is_array($v)) {
        $temp_str = trim(trim($v[1]) == '<?php' ? '' : $v[1]);
        $temp_str = $temp_str == '?>'? '' : $temp_str;
        if ($temp_str != '') {
          if ($v[0] == 320) {
            array_push($temp, '\\'.$temp_str);
          }else {
            array_push($temp, $temp_str);
          }
        }
      }else {
        array_push($temp, trim($v));
      }
    }


    $jump = 0;
    $ret = [];
    foreach($temp as $k => $v) {
      if ($jump) {$jump--; continue;}
      if ($v == 'include') {
        if ($temp[$k+1] == '(') {
          $temp_random = '_'.(string)mt_rand(1000000, 9999999);
          array_push($this->queue[$randomName], " eval(\\\$$temp_random)");
          $this->handle($temp[$k+2], $temp_random);
          $jump = 3;
        }else {
          $temp_random = '_'.(string)mt_rand(1000000, 9999999);
          array_push($this->queue[$randomName], " eval(\\\$$temp_random)");
          $this->handle($temp[$k+1], $temp_random);
          $jump = 1;
        }
      }else {
        array_push($this->queue[$randomName], $v);
      }
      if ($v == 'echo') {
        array_push($this->queue[$randomName],  ' ');
      }
    }
  }
  /**
   * 输出文件
  **/
  function output($outputPath) {
    $output_str = "<?php \r\n";
    $reverse_code = array_reverse($this->queue);
    foreach($reverse_code as $k => $v) {
      $output_str .= "\${$k} = <<<{$k}\r\n";
      foreach($v as $kk => $vv) {
        $output_str .= $vv;
      }
      $output_str .= "\r\n{$k};\r\n";
    }
    $reverse_name = array_reverse($this->queueName);
    $reverse_name = array_reverse($reverse_name);
    $output_str .= "eval(\$$reverse_name[0]);";
    file_put_contents($outputPath, $output_str);
  }
  /**
   * xxx
   **/

  function array_insert(&$array, $position, $insert_array) {
    $first_array = array_splice ($array, 0, $position);
    $array = array_merge ($first_array, $insert_array, $array);
  }
  
}