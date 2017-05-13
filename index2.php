<?php
/*
Create date:2017-04-19
Last update date:2017-04-27
Plugin Name: phpmysqli_dictionary
Plugin URI: http://www.bkk.tw
Description: PHP生成mysql數據庫字典工具
Version: 1.0.1
Author: david
 */
/*
適用PHP5.4~PHP7.0
 */
$thisDir = "."; //config.inc.php檔的相對路徑
$_file = basename(__FILE__); //自行取得本程式名稱
require $thisDir . "/config.inc.php"; // 載入主參數設定檔
require $thisDir . "/config.inc.mysql.php"; //載入資料庫帳號
if (background_switch) {
    require INCLUDE_PATH . "/inc_password.php"; // 載入密碼登入系統
} else {
}
require_once INCLUDE_PATH . "/mysql.inc.php"; // 載入資料庫函式
require_once INCLUDE_PATH . "/global_suffix.php"; // 載入資料庫函式
/***********************************************************************************************/
$typenamech2 = array('單個表列出', '全表列出', '自由拖拉');
$i_my = '後台';
$db_namec = '數據/資料庫字典生成工具';

$_GET['prefix'] = 'oc_'; //替換所有的表前綴
if (isset($_GET['prefix'])) {
    $prefix = $_GET['prefix'];
} else {
    $prefix = "";
}

/***********************************************************************************************/

$db = new Api_mysqli;
$dblink = $db->mysql_open(); //開資料庫連線================================================
//mysql_list_dbs --- 列出 MySQL 伺服器上可用的資料庫
$sql = 'SHOW DATABASES';
$DATANAME = $db->row_sql_a($sql, $dblink);

if (count($ok_show_databases) > 0) {
    //只顯示清單上的資料庫
    for ($i = 0; $i < count($DATANAME); $i++) {
        if (in_array($DATANAME[$i], $ok_show_databases)) {
            $DATANAME2[] = $DATANAME[$i];
        }
    }
} else {
    //或是
    //過濾不需要顯示的數據庫
    for ($i = 0; $i < count($DATANAME); $i++) {
        if (!in_array($DATANAME[$i], $no_show_databases)) {
            $DATANAME2[] = $DATANAME[$i];
        }
    }
}
$DATANAME = $DATANAME2;

//===========================================================
if (isset($_GET['DATANAME'])) {
    $database = $_GET['DATANAME'];
    $db->mysqluse($database, $dblink); //切換資料庫
}
//===========================================================
//有指定資料庫 情況 S
if ($database) {
    $sql = 'show tables';
    $tablesaaa = $db->row_sql_a($sql, $dblink);
//過濾不需要顯示的表名
    for ($i = 0; $i < count($tablesaaa); $i++) {
        if (!in_array($tablesaaa[$i], $no_show_databases)) {
            $tables[]['TABLE_NAME'] = $tablesaaa[$i];
        }
    }

//如果有參數//替換所有表的表前綴
    if ($prefix) {
        for ($i = 0; $i < count($tables); $i++) {
            $a = $tables[$i];
            $tables2[]['TABLE_NAME'] = str_replace($prefix, "", $a['TABLE_NAME']); //替換字串
        }
        //echo "替換表前綴" . $prefix . "替換成功！";
    }

//循環取得所有表的備註及表中列消息
    foreach ($tables as $k => $v) {
        //取得各表註解
        $sql = 'SELECT TABLE_COMMENT FROM ';
        $sql .= 'INFORMATION_SCHEMA.TABLES ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$v['TABLE_NAME']}'  AND table_schema = '{$database}'";
        $tables[$k]['TABLE_COMMENT'] = $db->row_sql1p($sql, 0, $dblink);
        //取得各表的每一欄註解
        //==============================================================
        $sql = 'SELECT * FROM ';
        $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";

        $fields = $db->assoc_sql($sql, $dblink);
        $tables[$k]['COLUMN'] = $fields; //子表內容
    }
//有指定資料庫 情況 END
}
$db->mysql_close($dblink); //關資料庫==============================================================
/***********************************************************************************************/

$html = ''; //循環所有表
//有指定資料庫 情況 S
if ($database) {
    foreach ($tables as $k => $v) {
        //如果有替換所有表的表前綴的行為
        if ($prefix) {
            $TABLE_NAME = $tables2[$k]['TABLE_NAME']; //表名
        } else {
            $TABLE_NAME = $v['TABLE_NAME'];
        }
        $b = cut_annotations($TABLE_NAME, $v['TABLE_COMMENT']); //參數英文名和註解
        //重整出 標題名
        $tablesB['TABLE_NAME0'][] = $v['TABLE_NAME']; //放表英文名未取代字串前
        $tablesB['TABLE_NAME'][] = $b['TABLE_NAME']; //放表英文名
        $tablesB['TABLE_COMMENT1'][] = $b['TABLE_COMMENT1']; //中文
        $tablesB['TABLE_COMMENT2'][] = $b['TABLE_COMMENT2']; //註解
        $tablesB['TABLE_COMMENT0'][] = $v['TABLE_COMMENT']; //註解 為拆解前
    }
    foreach ($tables as $k => $v) {
        //重複程式碼
        //參數$html,$tablesB=有放註解的數組,$k=第幾個,$v=表的各欄值,$editok=1編輯開關
        $html = combination_of_content($html, $tablesB, $k, $v, 0);
    }
//有指定資料庫 情況 END
}
/*
echo "表註解整理前";
print_r($tables);
echo "表註解整理後子表";
print_r($tablesB);
exit;
 */
$bn = "\n";
require INCLUDE_PATH . '/inc_head.php'; //載入表頭
/*
<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="skin/js/bootstrap/3.3.7/css/bootstrap.min.css">
<!-- 可選的Bootstrap主題文件（一般不用引入）-->
<link rel="stylesheet" href="skin/js/bootstrap/3.3.7/css/bootstrap-theme.min.css">
*/
?>
<!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- 可选的 Bootstrap 主题文件（一般不用引入） -->
<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<!-- 字體圖示 -->
<link rel="stylesheet" href="skin/css/font-awesome.min.css">
<!-- 本頁專用 -->
<link href="skin/css/admin.css" rel="stylesheet">
</head>
<body data-spy="scroll" data-target="#navbar-fixed-top" data-offset="20">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <h1>Tooltip 效果</h1>

                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="left" title="Tooltip on left">Tooltip on left</button>
                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Tooltip on top">Tooltip on top</button>
                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="Tooltip on bottom">Tooltip on bottom</button>
                <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="right" title="Tooltip on right">Tooltip on right</button>
            </div>
        </div>
    </div>
<!--滾動監聽 S-->
<div class="l_scroll">
<div class="upper_right" id="upper_right_close"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></div>
        <nav id="navbar-fixed-top">
            <div class="l_scroll-fluid">
            <ul class="nav nav-pills nav-stacked">

<?php
//有指定資料庫 情況 S
if ($database) {
    $c1 = $tablesB['TABLE_NAME'];
    for ($i = 0; $i < count($c1); $i++) {
        /*
        if($i==0)
        $classactive=' class="active"';
        else*/
        $classactive = '';
        echo "<li" . $classactive . ">" . $bn;
        echo "<a class=\"page-scroll target\" href=\"#" . $c1[$i] . "\">" . $c1[$i] . "</a>" . $bn;
        echo "</li>" . $bn;
    }
}
?>
            </ul>
            </div>
        </nav>

</div>
<!--滾動監聽 END-->
<!--下拉跳網址 S-->
<div align="center">
    <h3>
        <select name="goto_url">
            <option value="" selected>選擇資料庫</option>
            <?php
            for ($i = 0; $i < count($DATANAME); $i++) {
                if ($database == $DATANAME[$i]) {
                    $selected = "selected=\"selected\"";
                } else {
                    $selected = "";
                }
                            echo "<option value=\"" . $_file . "?DATANAME=" . $DATANAME[$i] . "\" " . $selected . " >" . $DATANAME[$i] . "</option>";
            }
?>
        </select>
    </h3>


</div>
<!--下拉跳網址 END-->
<!-- 標題區 -->
<div class="title-block">
    <h1 class="text_shadow">
        <?=$db_namec?>
    </h1>
</div>
<!--PC頁簽區hidden-xs-->
<div class="tab_memu">
    <ul class="nav nav-tabs" role="tablist">
        <?php
        for ($i = 0; $i < count($typenamech2); $i++) {
            //預設顯示3類中哪一類
            if ($i < 1) {
                $in = 'class="active"';
            } else {
                $in = "";
            }
                    $key = $i + 1;
                    echo '<li role="presentation" ' . $in . '><a href="#home' . $key . '" data-toggle="tab" role="tab" aria-controls="tab' . $key . '"  onclick="cookie_intype(' . $key . ');">' . $typenamech2[$i] . '</a></li>';
        }
?>
    </ul>
</div>
<!--PC頁簽區END-->
<div class="w100 height10">
    <!--間距-->
</div>
<div id="tabContent1" class="tab-content">
    <!--DIV切換區 S-->
    <!--第1區END-->
    <div role="tabpanel" class="tab-pane fade in active" id="home1">
        <!--預設選中-->
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <!--第1區 內容 S-->
                    <!--下拉跳資料表 S-->
                    <div align="center">
                        <h3>
                            <select name="goto_datasheet">
                                <option value="" selected>選擇資料表</option>
                                <?php
                                $b2 = $tablesB['TABLE_NAME0'];
                                foreach ($b2 as $k => $v) {
                                    $selected = "";
                                    //注意這裡一定要使用原來的表名
                                    echo "<option value=\"" . $v . "\" " . $selected . " >" . $v . "</option>";
                                }
?>
                            </select>
                        </h3>


                    </div>
                    <!--下拉跳網址 END-->
                    <hr>
                    <div id="div_home1"></div>

                </div>
            </div>
            <!--./row./col-md-12-->
        </div>
        <!--./container-->
    </div>
    <!--./home1-->
    <!--第1區END-->
    <!-------------------------------------------------------------------------------->
    <!--第2區開始-->
    <div role="tabpanel" class="tab-pane fade" id="home2">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php
//有指定資料庫 情況 S
                    if ($database) {
                        ?>
                        <h1 style="text-align:center;">
                            <?=$database?>數據庫字典&nbsp;
                            <button type="button" id="replace_editingid" class="btn btn-default" data-placement="right" title="點此按鈕可以切換編輯狀態" onclick="javascript:replace_editing();" ><i class="icon-pencil"></i></button>
                        </h1>
                        <hr>

                        <!--響應式表格-->
                        <div class="generallist">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr class="info2">
                                        <th class="sno">序號</th>
                                        <th>表名</th>
                                        <th>別人打的</th>
                                        <th>我打的</th>
                                        <th class="w50px">修改</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        //列出所有表

                                            $c1 = $tablesB['TABLE_NAME'];
                                            $c2 = $tablesB['TABLE_COMMENT0']; //註解
                                    for ($i = 0; $i < count($c1); $i++) {
                                        $cid = "edit_" . $tablesB['TABLE_NAME0'][$i]; //用原來的名 ID的規則是不能有點
                                        echo "<tr class=\"csssssss\">" . $bn;
                                        echo "<td class=\"cc\">" . ($i + 1) . "</td>" . $bn;
                                        echo "<td><a class=\"page-scroll\" href=\"#" . $c1[$i] . "\">" . $c1[$i] . "</a></td>" . $bn;
                                        echo "<td class=\"c4\">" . _lang($c1[$i], $lang_tablenames) . "</td>" . $bn;
                                        echo "<td><textarea class=\"w100 h100\" name=\"edit_text\" rows=\"1\" id=\"" . $cid . "\">" . $c2[$i] . "</textarea></td>" . $bn; //修改
                                        echo '<td>';
                                        echo '<button type="button" class="btn5 w50px" data-container="body" data-toggle="popover" data-placement="right" id="b' . $cid . '">修改</button>';
                                        echo '</td>' . $bn; //修改按鈕END
                                        echo "</tr>" . $bn;
                                    }
                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <hr>
                                            <div id="div_home2">
                                                <div class="warp" >
                                                <?php echo $html; ?>
                            </div>
                        </div>
                                        <script>
                                         $(function() {

                            //工具提示啟用
                            $("[data-toggle='popover']").popover({html: true,content:function (e){
                                var s=$(this).attr('id');
                                return '<div id="popoverid_'+s+'">儲存中...</div>';
                                }
                            });

                            //var storage_status=0;
                            //監測
                            $("[data-toggle='popover']").on('shown.bs.popover', function (e) {
                            //if (storage_status==0){
                                var gid2=e.currentTarget.id;
                                var gid3=gid2.replace(/\./g,"\\.");//如果欄位名中有.這樣可以讓他正常
                                var gid=gid3.substring(6);//截短6個字
                                var name='#'+gid3.substring(1);
                                var edittext=$(name).val();
                                    if(edittext==undefined){
                                         console.log('錯誤'); //測試使用
                                    }
                                var pdata = new Object();
                                pdata.database ='<?=$database?>';
                                pdata.tablename =gid2.substring(6);//截短6個字
                                pdata.edittext =edittext;
                                var el=$(this);
                                el.attr('disabled', 'disabled');//按鈕禁用
                                //console.log( pdata ); //測試使用
                                //storage_status++;
                                $.post( "ajax/goto_edit.php", pdata, function ( data ) {
                                    if ( data.ok ) {
                                        //成功 S
                                        console.log(data); //測試使用
                                        setTimeout(function () {
                        //el.popover('destroy');//銷毀
                        //el.popover("hide");//關掉彈出框
                        //el.popover({content :data.sms});//改內容
                        $('#popoverid_'+gid2).text(data.sms);//改內容
                            setTimeout(function () {
                            el.popover("hide");//關掉彈出框
                                setTimeout(function () {
                                el.removeAttr('disabled');//按鈕能用
                                //storage_status=0;//恢復監聽
                                },200);
                            },500);
                                        },500);
                                        //成功 END
                                    } else {
                                        //失敗
                        //console.log(data.sms); //測試使用
                        //關鍵不能同一時間2個指令
                        setTimeout(function () {
                        $('#popoverid_'+gid2).text(data.sms);//改內容
                            setTimeout(function () {
                            el.popover("hide");//關掉彈出框
                                setTimeout(function () {
                                el.removeAttr('disabled');//按鈕能用
                                //storage_status=0;//恢復監聽
                                },200);
                            },700);
                        },500);
                                        }
                                    }, 'json' );
                           // }//storage_status if END
                            });

                                        });//function END

                                        </script>
                                        <?php
                    } //有指定資料庫 情況 END
?>
                </div>
            </div>
            <!--./row./col-md-12-->
        </div>
        <!--./container-->
    </div>
    <!--./home2-->
    <!--第2區END-->
    <!-------------------------------------------------------------------------------->
    <!--第3區開始-->
    <div role="tabpanel" class="tab-pane fade" id="home3">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <button id="zero">歸零</button>
                    <button id="btn">顯示數據</button>
                    <br/>
                    <div id="svg123"></div>
                </div>
            </div>
            <!--./row./col-md-12-->
        </div>
        <!--./container-->
    </div>
    <!--./home2-->
    <!-------------------------------------------------------------------------------->
</div>
<!--/tabContent1 DIV切換區 END-->
<div class="w100 height10">
    <!--間距-->
</div>






<?php
require '_inc/inc_footer_s.php'; //載入表尾
?>
</body>
<script src="skin/js/jquery.cookie.min.js" type="text/javascript"></script>
<!--jqueryui-->
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script type="text/javascript">
    //載入完就啟用 開始*****************************/
    $(function() {

        $('[data-toggle="tooltip"]').tooltip(); //提示訊息-啟用 
    }); //function END
</script>
</html>