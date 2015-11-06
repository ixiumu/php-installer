<?php
header("Content-type:text/html;charset=utf-8");

if (file_exists('./install.lock')) {
  exit('您已经安装过该系统，如果需要重新安装，请删除 install 目录下的 install.lock 文件。');
}

@set_time_limit(1000);
date_default_timezone_set('PRC');
error_reporting(E_ALL & ~E_NOTICE);
define('INSPATH', dirname(__FILE__) . '/');
define('ABSPATH', realpath(substr(INSPATH, 0, -8)) . '/');

//数据库
$sqlFile = 'empty.sql';
//配置文件
$configSample = 'config-sample.php';
$configFile = ABSPATH . '/data/config.php';

if (!file_exists(INSPATH . $sqlFile) || !file_exists(INSPATH . $configSample)) {
    exit('缺少必要的安装文件!');
}
//标题
$title = "安装向导";
$version = '1.0.3';
$steps = array(
    '1' => '安装许可协议',
    '2' => '运行环境检测',
    '3' => '安装参数设置',
    '4' => '安装详细过程',
    '5' => '安装完成',
);

//需要写权限检查的目录
$checkFolder = array(
    'install',
    'data',
);

$step = isset($_GET['step']) ? $_GET['step'] : 1;

//地址
$scriptName = !empty($_SERVER["REQUEST_URI"]) ? $scriptName = $_SERVER["REQUEST_URI"] : $scriptName = $_SERVER["PHP_SELF"];
$rootpath = @preg_replace("/\/(I|i)nstall\/index\.php(.*)$/", "", $scriptName);
$domain = empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
if ((int) $_SERVER['SERVER_PORT'] != 80) {
    $domain .= ":" . $_SERVER['SERVER_PORT'];
}
$domain = $domain . $rootpath;

//标题
$title = $steps[$step] . ' - '. $title;

//输出内容
if ( ! ($step == 4 && isset($_GET['install'])) && !($step == 3 && isset($_GET['testdbpwd'])) ) {
?><!doctype html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"/>
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="./install.css?v=1.0" />
<script src="./js/jquery.min.js?v=1.9.0"></script>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1 class="logo">logo</h1>
    <div class="title_install f16">安装向导</div>
    <div class="version"><?php echo $version;?></div>
  </div>
<?php
}

switch ($step) {
    //step 1
    case '1':
?>
  <div class="section">
    <div class="main cc">
      <pre class="pact" readonly="readonly">软件使用协议

版权所有 (c)2008-<?php echo date("Y")?>，蓝芒网络 保留所有权利。
为了使你正确并合法的使用本软件，请你在使用前务必阅读清楚下面的协议条款：
本授权协议适用且仅适用于本软件任何版本，官方对本授权协议的最终解释权和修改权。

一、协议许可的权利
1、您可以在完全遵守本最终用户授权协议的基础上，将本软件应用于非商业用途，而不必支付软件版权授权费用。
2、您可以在协议规定的约束和限制范围内修改源代码或界面风格以适应您的网站要求。
3、您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。
4、获得商业授权之后，您才可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持内容。商业授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。

二、协议许可的权利和限制
1、未获商业授权之前，不得删除网站底部及相应的官方版权信息和链接。本程序著作权受到法律和国际公约保护。
2、未经官方许可，不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。
3、未经官方许可，禁止在程序的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。
4、如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。

三、有限担保和免责声明
1、本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。 
2、用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。
3、电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
</pre>
    
    </div>
    <div class="bottom tac"> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?step=2" class="btn">接 受</a> </div>
  </div>
</div>

<?php
        footer();
    //step 2
    case '2':
        $phpv = @ phpversion();
        if ($phpv < 5.3) {
            die('本系统需要PHP5+MYSQL >=5.3环境，当前PHP版本为：' . $phpv);
        }
        $os = PHP_OS;
        $os = php_uname();
        $tmp = function_exists('gd_info') ? gd_info() : array();
        $server = $_SERVER["SERVER_SOFTWARE"];
        $host = (empty($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_HOST"] : $_SERVER["SERVER_ADDR"]);
        $name = $_SERVER["SERVER_NAME"];
        $max_execution_time = ini_get('max_execution_time');
        $allow_reference = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $allow_url_fopen = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $safe_mode = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');

        $err = 0;
        if (empty($tmp['GD Version'])) {
            $gd = '<font color=red>[×]Off</font>';
            $err++;
        } else {
            $gd = '<font color=green>[√]On</font> ' . $tmp['GD Version'];
        }
        if (function_exists('mysql_connect')) {
            $mysql = '<span class="correct_span">&radic;</span> 已安装';
        } else {
            $mysql = '<span class="correct_span error_span">&radic;</span> 出现错误';
            $err++;
        }
        if (ini_get('file_uploads')) {
            $uploadSize = '<span class="correct_span">&radic;</span> ' . ini_get('upload_max_filesize');
        } else {
            $uploadSize = '<span class="correct_span error_span">&radic;</span>禁止上传';
        }
        if (function_exists('session_start')) {
            $session = '<span class="correct_span">&radic;</span> 支持';
        } else {
            $session = '<span class="correct_span error_span">&radic;</span> 不支持';
            $err++;
        }
?>
  <section class="section">
    <div class="step">
      <ul>
        <li class="current"><em>1</em>检测环境</li>
        <li><em>2</em>创建数据</li>
        <li><em>3</em>完成安装</li>
      </ul>
    </div>
    <div class="server">
      <table width="100%">
        <tr>
          <td class="td1">环境检测</td>
          <td class="td1" width="25%">推荐配置</td>
          <td class="td1" width="25%">当前状态</td>
          <td class="td1" width="25%">最低要求</td>
        </tr>
        <tr>
          <td>操作系统</td>
          <td>类UNIX</td>
          <td><span class="correct_span">&radic;</span> <?php echo $os; ?></td>
          <td>不限</td>
        </tr>
        <tr>
          <td>PHP版本</td>
          <td>>5.3.x</td>
          <td><span class="correct_span">&radic;</span> <?php echo $phpv; ?></td>
          <td>5.3.0</td>
        </tr>
        <tr>
          <td>MySQL版本 (Client)</td>
          <td>>5.x.x</td>
          <td><?php echo $mysql; ?></td>
          <td>4.2</td>
        </tr>
        <tr>
          <td>附件上传</td>
          <td>>2M</td>
          <td><?php echo $uploadSize; ?></td>
          <td>不限</td>
        </tr>
        <tr>
          <td>SESSION</td>
          <td>开启</td>
          <td><?php echo $session; ?></td>
          <td>开启</td>
        </tr>
      </table>
      <table width="100%">
        <tr>
          <td class="td1">目录、文件权限检查</td>
          <td class="td1" width="25%">写入</td>
          <td class="td1" width="25%">读取</td>
        </tr>
<?php
foreach($checkFolder as $dir){
     $Testdir = ABSPATH.$dir;
     dir_create($Testdir);
   if(TestWrite($Testdir)){
       $w = '<span class="correct_span">&radic;</span>可写 ';
   }else{
       $w = '<span class="correct_span error_span">&radic;</span>不可写 ';
     $err++;
   }
   if(is_readable($Testdir)){
       $r = '<span class="correct_span">&radic;</span>可读' ;
   }else{
       $r = '<span class="correct_span error_span">&radic;</span>不可读';
     $err++;
   }
?>
        <tr>
          <td><?php echo $dir; ?></td>
          <td><?php echo $w; ?></td>
          <td><?php echo $r; ?></td>
        </tr>
<?php
}
?>   
      </table>
    </div>
    <div class="bottom tac"> <a href="<?php echo $_SERVER['PHP_SELF']; ?>?step=2" class="btn">重新检测</a><a href="<?php echo $_SERVER['PHP_SELF']; ?>?step=3" class="btn">下一步</a> </div>
  </section>
</div>
<?php
        footer();
    //step 3
    case '3':

        if ($_GET['testdbpwd']) {
            $dbHost = $_POST['dbHost'] . ':' . $_POST['dbPort'];
            $conn = @mysql_connect($dbHost, $_POST['dbUser'], $_POST['dbPwd']);
            if ($conn) {
                exit('1');
            } else {
                exit;
            }
        }
?>
  <section class="section">
    <div class="step">
      <ul>
        <li class="on"><em>1</em>检测环境</li>
        <li class="current"><em>2</em>创建数据</li>
        <li><em>3</em>完成安装</li>
      </ul>
    </div>
    <form id="J_install_form" action="index.php?step=4" method="post">
      <input type="hidden" name="force" value="0" />
      <div class="server">
        <table width="100%">
          <tr>
            <td class="td1" width="100">数据库信息</td>
            <td class="td1" width="200">&nbsp;</td>
            <td class="td1">&nbsp;</td>
          </tr>
          <tr>
            <td class="tar">数据库地址：</td>
            <td><input type="text" name="dbhost" id="dbhost" value="localhost" class="input"></td>
            <td><div id="J_install_tip_dbhost"><span class="gray">数据库服务器地址，一般为localhost</span></div></td>
          </tr>
          <tr>
            <td class="tar">数据库端口：</td>
            <td><input type="text" name="dbport" id="dbport" value="3306" class="input"></td>
            <td><div id="J_install_tip_dbport"><span class="gray">数据库服务器端口，一般为3306</span></div></td>
          </tr>
          <tr>
            <td class="tar">数据库账号：</td>
            <td><input type="text" name="dbuser" id="dbuser" value="root" class="input"></td>
            <td><div id="J_install_tip_dbuser"></div></td>
          </tr>
          <tr>
            <td class="tar">数据库密码：</td>
            <td><input type="password" name="dbpw" id="dbpw" value="" class="input" autoComplete="off" onblur="TestDbPwd()"></td>
            <td><div id="J_install_tip_dbpw"></div></td>
          </tr>
          <tr>
            <td class="tar">数据库名：</td>
            <td><input type="text" name="dbname" id="dbname" value="" class="input"></td>
            <td><div id="J_install_tip_dbname"></div></td>
          </tr>
          <tr>
            <td class="tar">表前缀：</td>
            <td><input type="text" name="dbprefix" id="dbprefix" value="z_" class="input"></td>
            <td><div id="J_install_tip_dbprefix"><span class="gray">同一数据库多次安装时需更改</span></div></td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td class="td1" width="100">管理员设置</td>
            <td class="td1" width="200">&nbsp;</td>
            <td class="td1">&nbsp;</td>
          </tr>
          <tr>
            <td class="tar">帐号：</td>
            <td><input type="text" name="manager" value="admin" class="input"></td>
            <td><div id="J_install_tip_manager"></div></td>
          </tr>
          <tr>
            <td class="tar">密码：</td>
            <td><input type="password" name="manager_pwd" id="J_manager_pwd" class="input" autoComplete="off"></td>
            <td><div id="J_install_tip_manager_pwd"></div></td>
          </tr>
          <tr>
            <td class="tar">重复密码：</td>
            <td><input type="password" name="manager_ckpwd" class="input" autoComplete="off"></td>
            <td><div id="J_install_tip_manager_ckpwd"></div></td>
          </tr>
          <tr>
            <td class="tar">Email：</td>
            <td><input type="text" name="manager_email" class="input" value=""></td>
            <td><div id="J_install_tip_manager_email"></div></td>
          </tr>
        </table>
        <div id="J_response_tips" style="display:none;"></div>
      </div>
      <div class="bottom tac"> <a href="./index.php?step=2" class="btn">上一步</a>
        <button type="submit" class="btn btn_submit J_install_btn">创建数据</button>
      </div>
    </form>
  </section>
  <script src="./js/validate.js?v=1.0"></script>
  <script src="./js/ajaxForm.js?v=1.0"></script>
  <script>
$(function(){

  //聚焦时默认提示
  var focus_tips = {
    dbhost : '数据库服务器地址，一般为localhost',
    dbport : '数据库服务器端口，一般为3306',
    dbuser : '',
    dbpw : '',
    dbname : '',
    dbprefix : '同一数据库多次安装时需更改',
    manager : '管理员帐号，拥有站点后台所有管理权限',
    manager_pwd : '',
    manager_ckpwd : '',
    sitename : '',
    siteurl : '请以“/”结尾',
    sitekeywords : '',
    siteinfo : '',
    manager_email : ''
  };


  var install_form = $("#J_install_form"),
      reg_username = $('#J_reg_username'),            //用户名表单
      reg_password = $('#J_reg_password'),            //密码表单
      reg_tip_password = $('#J_reg_tip_password'),    //密码提示区
      response_tips = $('#J_response_tips');        //后端返回提示

  //validate插件修改了remote ajax验证返回的response处理方式；增加密码强度提示 passwordRank
  install_form.validate({
    //debug : true,
    //onsubmit : false,
    errorPlacement: function(error, element) {
      //错误提示容器
      $('#J_install_tip_'+ element[0].name).html(error);
    },
    errorElement: 'span',
    //invalidHandler : , 未验证通过 回调
    //ignore : '.ignore' 忽略验证
    //onkeyup : true,
    errorClass : 'tips_error',
    validClass    : 'tips_error',
    onkeyup : false,
    focusInvalid : false,
    rules: {
      dbhost: {
        required  : true
      },
      dbport:{
          required  : true
      },
      dbuser: {
        required  : true
      },
      /* dbpw: {
        required  : true
      }, */
      dbname: {
        required  : true
      },
      dbprefix : {
        required  : true
      },
      manager: {
        required  : true
      },
      manager_pwd: {
        required  : true
      },
      manager_ckpwd: {
        required  : true,
        equalTo : '#J_manager_pwd'
      },
      manager_email: {
        required  : true,
        email : true
      }
    },
    highlight  : false,
    unhighlight  : function(element, errorClass, validClass) {
      var tip_elem = $('#J_install_tip_'+ element.name);

        tip_elem.html('<span class="'+ validClass +'" data-text="text"><span>');

    },
    onfocusin  : function(element){
      var name = element.name;
      $('#J_install_tip_'+ name).html('<span data-text="text">'+ focus_tips[name] +'</span>');
      $(element).parents('tr').addClass('current');
    },
    onfocusout  :  function(element){
      var _this = this;
      $(element).parents('tr').removeClass('current');
      
      if(element.name === 'email') {
        //邮箱匹配点击后，延时处理
        setTimeout(function(){
          _this.element(element);
        }, 150);
      }else{
      
        _this.element(element);
        
      }
      
    },
    messages: {
      dbhost: {
        required  : '数据库服务器地址不能为空'
      },
      dbport:{
          required  : '数据库服务器端口不能为空'
      },
      dbuser: {
        required  : '数据库用户名不能为空'
      },
      dbpw: {
        required  : '数据库密码不能为空'
      },
      dbname: {
        required  : '数据库名不能为空'
      },
      dbprefix : {
        required  : '数据库表前缀不能为空'
      },
      manager: {
        required  : '管理员帐号不能为空'
      },
      manager_pwd: {
        required  : '密码不能为空'
      },
      manager_ckpwd: {
        required  : '重复密码不能为空',
        equalTo : '两次输入的密码不一致。请重新输入'
      },
      manager_email: {
        required  : 'Email不能为空',
        email : '请输入正确的电子邮箱地址'
      }
    },
    submitHandler:function(form) {
      form.submit();
      return true;
    }
  });

  var _data = {};
});


function TestDbPwd()
{
  var dbHost = $('#dbhost').val();
  var dbUser = $('#dbuser').val();
  var dbPwd = $('#dbpw').val();
  var dbName = $('#dbname').val();
  var dbPort = $('#dbport').val();
  data={'dbHost':dbHost,'dbUser':dbUser,'dbPwd':dbPwd,'dbName':dbName,'dbPort':dbPort};
  var url =  "<?php echo $_SERVER['PHP_SELF']; ?>?step=3&testdbpwd=1";
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    beforeSend:function(){
    },
    success: function(msg){
      if(msg){
        $('#J_install_tip_dbpw').html('<span for="dbname" class="tips_success">数据库连接成功</span>');
      }else{
        $('#dbpw').val("");
        $('#J_install_tip_dbpw').html('<span for="dbname" generated="true" class="tips_error">数据库链接配置失败</span>');
      }
    },
    complete:function(){
    },
    error:function(){
      $('#J_install_tip_dbpw').html('<span for="dbname" generated="true" class="tips_error">数据库链接配置失败</span>');
      $('#dbpw').val("");
    }
  });
}
</script> 
</div>
<?php
        footer();
    //step 4
    case '4':
        if (intval($_GET['install'])) {
            $n = intval($_GET['n']);
            $arr = array();

            $dbHost = trim($_POST['dbhost']);
            $dbPort = trim($_POST['dbport']);
            $dbName = strtolower(trim($_POST['dbname']));
            $dbHost = empty($dbPort) || $dbPort == 3306 ? $dbHost : $dbHost . ':' . $dbPort;
            $dbUser = trim($_POST['dbuser']);
            $dbPwd = trim($_POST['dbpw']);
            $dbPrefix = empty($_POST['dbprefix']) ? 'tuzi_' : trim($_POST['dbprefix']);
            
            //管理员信息
            $username = trim($_POST['manager']);
            $password = trim($_POST['manager_pwd']);
            $email    = trim($_POST['manager_email']);
            
            $conn = @ mysql_connect($dbHost, $dbUser, $dbPwd);
            if (!$conn) {
                $arr['msg'] = "连接数据库失败!";
                echo json_encode($arr);
                exit;
            }
            mysql_query("SET NAMES 'utf8'");
            $version = mysql_get_server_info($conn);
            if ($version < 4.1) {
                $arr['msg'] = '数据库版本太低!';
                echo json_encode($arr);
                exit;
            }

            if (!mysql_select_db($dbName, $conn)) {
                //创建数据时同时设置编码
                if (!mysql_query("CREATE DATABASE IF NOT EXISTS `" . $dbName . "` DEFAULT CHARACTER SET utf8;", $conn)) {
                    $arr['msg'] = '数据库 ' . $dbName . ' 不存在，也没权限创建新的数据库！';
                    echo json_encode($arr);
                    exit;
                }
                if (empty($n)) {
                    $arr['n'] = 1;
                    $arr['msg'] = "成功创建数据库:{$dbName}<br>";
                    echo json_encode($arr);
                    exit;
                }
                mysql_select_db($dbName, $conn);
            }

            //读取数据文件
            $sqldata = file_get_contents(INSPATH . $sqlFile);
            $sqlFormat = sql_split($sqldata, $dbPrefix);
            //创建写入sql数据库文件到库中 结束

            /**
              执行SQL语句
             */
            $counts = count($sqlFormat);

            for ($i = $n; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);

                if (strstr($sql, 'CREATE TABLE')) {
                    preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
                    mysql_query("DROP TABLE IF EXISTS `$matches[1]");
                    $ret = mysql_query($sql);
                    if ($ret) {
                        $message = '<li><span class="correct_span">&radic;</span>创建数据表' . $matches[1] . '，完成!<span style="float: right;">'.date('Y-m-d H:i:s').'</span></li> ';
                    } else {
                        $message = '<li><span class="correct_span error_span">&radic;</span>创建数据表' . $matches[1] . '，失败!<span style="float: right;">'.date('Y-m-d H:i:s').'</span></li>';
                    }
                    $i++;
                    $arr = array('n' => $i, 'msg' => $message);
                    echo json_encode($arr);
                    exit;
                } else {
                    $ret = mysql_query($sql);
                    $message = '';
                    $arr = array('n' => $i, 'msg' => $message);
                    //echo json_encode($arr); exit;
                }
            }

            if ($i == 999999)
                exit;
            
            //读取配置文件，并替换真实配置数据
            $strConfig = file_get_contents(INSPATH . $configSample);
            $strConfig = str_replace('#DB_HOST#', $dbHost, $strConfig);
            $strConfig = str_replace('#DB_NAME#', $dbName, $strConfig);
            $strConfig = str_replace('#DB_USER#', $dbUser, $strConfig);
            $strConfig = str_replace('#DB_PWD#', $dbPwd, $strConfig);
            $strConfig = str_replace('#DB_PORT#', $dbPort, $strConfig);
            $strConfig = str_replace('#DB_PREFIX#', $dbPrefix, $strConfig);
            
            @chmod($configFile,0777); //数据库配置文件的地址
            @file_put_contents($configFile,$strConfig); //数据库配置文件的地址

            //插入管理员表字段表
            $time = time();
            $create_date=date("Y-m-d h:i:s");
            $ip = get_client_ip();
            $ip =empty($ip)?"0.0.0.0":$ip;
            $password = md5(trim($_POST['manager_pwd']));
            mysql_query("INSERT INTO `{$dbPrefix}admin` (id,admin_name,admin_pass,admin_login,admin_email,admin_ip,admin_ok,admin_date,admin_type) VALUES ('1','$username','$password','1','$email','$ip','0','$create_date','0')");

            $message = '成功添加管理员<br />成功写入配置文件<br>安装完成．';
            $arr = array('n' => 999999, 'msg' => $message);
            echo json_encode($arr);
            exit;
        }
        //case '5'结束
?>
  <section class="section">
    <div class="step">
      <ul>
        <li class="on"><em>1</em>检测环境</li>
        <li class="on"><em>2</em>创建数据</li>
        <li class="current"><em>3</em>完成安装</li>
      </ul>
    </div>
    <div class="install" id="log">
      <ul id="loginner">
      </ul>
    </div>
    <div class="bottom tac">
      <a href="javascript:;" class="btn_old">正在安装...</a>
    </div>
  </section>
  <script type="text/javascript">
    var n=0;
    var data = <?php echo json_encode($_POST);?>;
    $.ajaxSetup ({ cache: false });
    function reloads(n) {
        var url =  "<?php echo $_SERVER['PHP_SELF']; ?>?step=4&install=1&n="+n;
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            beforeSend:function(){
            },
            success: function(msg){
                if(msg.n=='999999'){
                    $('#dosubmit').attr("disabled",false);
                    $('#dosubmit').removeAttr("disabled");
                    $('#dosubmit').removeClass("nonext");
                    setTimeout(function(){
                      window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>?step=5';
                    },3000);
                }
                if(msg.n){
                    $('#loginner').append(msg.msg);
                    reloads(msg.n);  
                }else{
                    //alert('指定的数据库不存在，系统也无法创建，请先通过其他方式建立好数据库！');
                    alert(msg.msg);
                }
            }
        });
    }
    $(document).ready(function(){
        reloads(n);
    })
  </script>
</div>
<?php
        footer();
    //step 5
    case '5':
        $locked = @touch('./install.lock');
?>
  <section class="section">
    <div class="success_tip cc">
      <span class="f16 b">安装成功</span>
      <?php if(!$locked) echo '<p style="color:red;font-weight:bold">创建install.lock文件失败</p>';?>
      <p>为确保您的站点安全，安装完成后可以将网站根目录<br />下的“install”文件夹删除。<p>
    </div>
    <div class="bottom tac"> 
      <a href="../" class="btn">进入前台</a>
      <a href="../admin/" class="btn btn_submit J_install_btn">进入后台</a>
    </div>
  </section>
</div>
<?php
        footer();
}

function footer() {
    exit('<div class="footer"> &copy; 2008-' . date("Y") . '</div>
</body>
</html>');
}

function testwrite($d) {
    $tfile = "is_writable.html";
    $fp = @fopen($d . "/" . $tfile, "w");
    if (!$fp) {
        return false;
    }
    fclose($fp);
    $rs = @unlink($d . "/" . $tfile);
    if ($rs) {
        return true;
    }
    return false;
}

function sql_execute($sql, $tablepre) {
    $sqls = sql_split($sql, $tablepre);
    if (is_array($sqls)) {
        foreach ($sqls as $sql) {
            if (trim($sql) != '') {
                mysql_query($sql);
            }
        }
    } else {
        mysql_query($sqls);
    }
    return true;
}

function sql_split($sql, $tablepre) {
    if ($tablepre != "empty_")
        $sql = str_replace("empty_", $tablepre, $sql);
    $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);

    if ($r_tablepre != $s_tablepre)
        $sql = str_replace($s_tablepre, $r_tablepre, $sql);
    $sql = str_replace("\r", "\n", $sql);
    $ret = array();
    $num = 0;
    $queriesarray = explode(";\n", trim($sql));
    unset($sql);
    foreach ($queriesarray as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        $queries = array_filter($queries);
        foreach ($queries as $query) {
            $str1 = substr($query, 0, 1);
            if ($str1 != '#' && $str1 != '-')
                $ret[$num] .= $query;
        }
        $num++;
    }
    return $ret;
}

// 获取客户端IP地址
function get_client_ip() {
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos)
            unset($arr[$pos]);
        $ip = trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

function dir_create($path, $mode = 0777) {
    if (is_dir($path))
        return TRUE;
    $ftp_enable = 0;
    $path = dir_path($path);
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir))
            continue;
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}

function dir_path($path) {
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/')
        $path = $path . '/';
    return $path;
}