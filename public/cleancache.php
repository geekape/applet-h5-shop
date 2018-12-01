<?php
//清文件缓存
define('SITE_PATH', dirname(dirname(__FILE__)));
$dirs   =   array(SITE_PATH.'/runtime/');


//清理缓存
foreach ($dirs as $value) {
    rmdirr($value);
    echo "<div style='border:2px solid green; background:#f1f1f1; padding:20px;margin:20px;width:800px;font-weight:bold;color:green;text-align:center;'>\"".$value."\" have been cleaned clear! </div> <br /><br />";
}

@mkdir(SITE_PATH.'/runtime', 0777, true);

function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }
    $dir = dir($dirname);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }
    }
    $dir->close();
    return rmdir($dirname);
}
function U()
{
    return false;
}
