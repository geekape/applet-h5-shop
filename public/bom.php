<?php
if(function_exists('set_time_limit')){
	set_time_limit(0);
}
if (isset($_GET ['dir'])) { // 设置文件目录
    $basedir = $_GET ['dir'];
} else {
    $basedir = '.';
}
$auto = 1;
checkdir($basedir);
function checkdir($basedir)
{
    if ($dh = opendir($basedir)) {
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..' && $file != '.svn') {
                if (! is_dir($basedir . "/" . $file)) {
                    $code = detect_encoding("$basedir/$file");
                    if ($code=='EUC-CN') {
                        echo "filename: $basedir/$file " . $code . " <br>";
                    }
                    // $res = checkBOM("$basedir/$file");
                    // if ($res != 'BOM Not Found.') {
                    //     echo "filename: $basedir/$file " . $res . " <br>";
                    // }
                } else {
                    $dirname = $basedir . "/" . $file;
                    checkdir($dirname);
                }
            }
        }
        closedir($dh);
    }
}
function checkBOM($filename)
{
    global $auto;
    $contents = file_get_contents($filename);
    $charset [1] = substr($contents, 0, 1);
    $charset [2] = substr($contents, 1, 1);
    $charset [3] = substr($contents, 2, 1);
    if (ord($charset [1]) == 239 && ord($charset [2]) == 187 && ord($charset [3]) == 191) {
        if ($auto == 1) {
            $rest = substr($contents, 3);
            rewrite($filename, $rest);
            return ("<font color=red>BOM found, automatically removed.</font>");
        } else {
            return ("<font color=red>BOM found.</font>");
        }
    } else {
        return ("BOM Not Found.");
    }
}
function rewrite($filename, $data)
{
    $filenum = fopen($filename, "w");
    flock($filenum, LOCK_EX);
    fwrite($filenum, $data);
    fclose($filenum);
}
/**
 * 检测文件编码
 * @param string $file 文件路径
 * @return string|null 返回 编码名 或 null
 */
function detect_encoding($file)
{
    $str = file_get_contents($file);
    return mb_detect_encoding($str, ['UTF-8','ASCII','GB2312','GBK']);
}
// 浏览器友好的变量输出
function dump($var)
{
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    if (! extension_loaded('xdebug')) {
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        $output = '<pre style="text-align:left">' . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
    }
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo ($output);
}
