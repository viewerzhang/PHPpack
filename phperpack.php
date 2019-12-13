<?php
/**
 * PHPpack使用说明
 * -m 指定入口文件路径 可以是相当路径 也可以是绝对路径
 * -o 指定输出文件位置 及文件名，可以是相对路径，也可以是绝对路径
 * --v 开启变量名混淆
**/
new Main();


class Main {
  public $entry;
  public $outputPath;
  static public $varConfusion = False;
  public function __construct() {
    $this->_init();
  }

  public function _init() {
    global $argv;
    $this->init_before();
    $mainIndex = array_search('-m',$argv);
    $mainIndex || die(new Message("对不起进程无法继续，请输入入口文件名称", 'error'));
    $outputIndex = array_search('-o', $argv);
    $this->outputPath = $outputIndex ? $this->handlePath($argv[ $outputIndex + 1 ]) : './output.php';
    $mainFileName = $argv[$mainIndex + 1];
    if (!$mainFileName) {
      die(new Message('入口文件输入错误', 'error'));
    }
    if (array_search('--v', $argv)) {
      Main::$varConfusion = True;
    }

    $this->entry = $this->handlePath($mainFileName);
    new Handle($this->entry, $this->outputPath);
  }

  public function handlePath($path) {
    if (strpos($path, '/') || strpos($path, '\\')) {
      return $path;
    }else{
      return './' . $path;
    }
  }

  public function init_before() {
    echo new Message('-----------------------------------------------');
    echo new Message('|------------------👏欢迎使用👏---------------|');
    echo new Message('|------------------PHPpack 1.0----------------|');
    echo new Message('|------------------作者：张宇童---------------|');
    echo new Message('|--------------邮箱：admin@ecuuu.com----------|');
    echo new Message('|-----------------PHPpack构建工具-------------|');
    echo new Message('-----------------------------------------------');
  }
}


class Handle extends Main {
  private $preprocessing; // 预处理文件内容
  private $afterContent; // 处理后的内容
  private $queue = []; // 语法队列
  private $queueName = [];
  private $varMap = [];
  private $keyWordSpace = [
    'echo',
    'function',
    'class',
    'private',
    'public',
    'protected',
    'new',
    'extends',
  ];

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
    echo new Message('加载：'.__DIR__.rtrim(ltrim($filePath,'\'.'),'\''));
    $content = file_get_contents(__DIR__.rtrim(ltrim($filePath,'\'.'),'\''));
    $temp = [];
    foreach (token_get_all($content) as $k => $v) {
      if (is_array($v)) {
        $temp_str = trim(trim($v[1]) == '<?php' ? '' : $v[1]);
        $temp_str = $temp_str == '?>'? '' : $temp_str;
        if ($temp_str != '') {
          if ($v[0] == 320) {
            if (Main::$varConfusion) {
              $temp_str = $this->varMap[$temp_str] = '$_'.md5($temp_str);
            }
            array_push($temp, '\\'.$temp_str);
          }else if($v[0] == 377) {

          }else {
            array_push($temp, $temp_str);
          }
          if ($v[0] == 319) {
            array_push($temp, ' ');
          }
        }
      }else {
        array_push($temp, trim($v));
      }
    }

    $ret = [];
    while(list($k, $v) = each($temp)) {
      if ($v == 'include' || $v == 'include_once') {
        $this->handle_parse_include($temp, $temp, $randomName);
      }else {
        array_push($this->queue[$randomName], $v);
      }
      if (in_array($v, $this->keyWordSpace)) {
        array_push($this->queue[$randomName], ' ');
      }
    }
  }

  /**
   * 处理include
  **/
  function handle_parse_include($arr, &$origin_arr, $randomName) {
    list($k, $v) = each($arr);
    if ($v == '(') {
      $temp_random = '_'.(string)mt_rand(1000000, 9999999);
      array_push($this->queue[$randomName], " eval(\\\$$temp_random)");
      $this->handle(each($arr)['value'], $temp_random);
      for ($i = 0; $i < 3; $i++) {
        each($origin_arr);
      }
    }else {
      $temp_random = '_'.(string)mt_rand(1000000, 9999999);
      array_push($this->queue[$randomName], " eval(\\\$$temp_random)");
      $this->handle($v, $temp_random);
      each($origin_arr);
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
    echo new Message("👌PHPpack为您构建成功！\r\n🚗构建后文件位置：{$outputPath}", 'success');
  }
  /**
   * xxx
   **/

  function array_insert(&$array, $position, $insert_array) {
    $first_array = array_splice ($array, 0, $position);
    $array = array_merge ($first_array, $insert_array, $array);
  }
  
}

class Message {
  public function __construct($message, $type = 'info') {
    $this->case_type($type, $message);
  }

  public function case_type ($type, $message) {
    switch ($type) {
      case 'error':
        $this->message = "\033[31m{$message}\033[0m\r\n";
        break;
      case 'success':
        $this->message = "\033[32m{$message}\033[0m\r\n";
        break;
      case 'info':
        $this->message = "\033[36m{$message}\033[0m\r\n";
        break;
    }
  }

  public function __toString() {
    return $this->message;
  }
}