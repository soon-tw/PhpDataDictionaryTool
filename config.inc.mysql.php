<?php
// 配置數據庫 第1組
$i=1;
$cfg['servers'][$i]['host'] = 'localhost';          //服務器位址
$cfg['servers'][$i]['port'] = '33306';                 //端口
$cfg['servers'][$i]['user'] = 'root';            //數據庫用戶名
$cfg['servers'][$i]['password'] = 'azsx1277'; //密碼
$cfg['servers'][$i]['database'] = 'cart_opencart_00';      //預設開啟資料庫
// 配置數據庫 第2組
$i++;//不用打2;++就是以此類推+1的意示
$cfg['servers'][$i]['host'] = '192.168.1.2';          //服務器位址
$cfg['servers'][$i]['port'] = 3306;                 //端口
$cfg['servers'][$i]['user'] = 'big1234';            //數據庫用戶名
$cfg['servers'][$i]['password'] = 'dada_0727_lala'; //密碼
$cfg['servers'][$i]['database'] = 'cart_opencart_00';      //預設開啟資料庫
// 配置數據庫 第3組
$i++;//不用打3;++就是以此類推+1的意示
$cfg['servers'][$i]['host'] = '192.168.1.3';          //服務器位址
$cfg['servers'][$i]['port'] = 3306;                 //端口
$cfg['servers'][$i]['user'] = 'big1234';            //數據庫用戶名
$cfg['servers'][$i]['password'] = 'dada_0727_lala'; //密碼
$cfg['servers'][$i]['database'] = 'credit';      //預設開啟資料庫
//**************************
$i=1;//本次使用第幾組設定
//**************************


//自機測試時
if(preg_match("@test.com@",$_SERVER['HTTP_HOST'])){
$cfg['servers'][$i]['host'] = 'localhost';
$cfg['servers'][$i]['port'] = '3306'; 
}

$cfg['servers']['y']=$i;
$database = $cfg['servers'][$i]['database'];//資料庫名
//**************************
unset($i);//釋放變數$i
//**************************