<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\model;

use app\common\model\Base;

/**
 * 文件模型
 * 负责文件的下载和上传
 */
class File extends Base
{

    /**
     * 文件模型自动完成
     *
     * @var array
     */
    protected $_auto = array(
        array(
            'create_time',
            NOW_TIME
        )
    );

    /**
     * 文件模型字段映射
     *
     * @var array
     */
    protected $_map = array(
        'type' => 'mime'
    );

    /**
     * 文件上传
     *
     * @param array $files
     *            要上传的文件列表（通常是$_FILES数组）
     * @param array $setting
     *            文件上传配置
     * @param string $driver
     *            上传驱动名称
     * @param array $config
     *            上传驱动配置
     * @return array 文件上传成功后的信息
     */
    public function upload($setting = [], $driver = 'Local', $config = null)
    {
        /* 上传文件 */
        $setting['callback'] = array(
            $this,
            'isFile'
        );
        
        $info = upload_files($setting, $driver, $config, 'download');
        
        /* 设置文件保存位置 */
        $this->_auto[] = array(
            'location',
            'Ftp' === $driver ? 1 : 0
        );
        
        if ((isset($info['msg']) && empty($info['msg'])) || (isset($info['download']['msg']) && empty($info['download']['msg']))) { // 文件上传成功，记录文件信息
            $fileData = $this->column('id', 'md5');
            foreach ($info as $key => &$value) {
                $value['create_time'] = NOW_TIME;
                if (isset($fileData[$value['md5']])) {
                    $value['id'] = $fileData[$value['md5']];
                }
                
                $pathinfo = pathinfo($value['savename']);
                /* 已经存在文件记录 */
                if (isset($value['id']) && is_numeric($value['id'])) {
                    $value['savepath'] = $pathinfo['dirname'] . '/';
                    $value['savename'] = $value['name'];
                    $value['name'] = $value['old_name'];
                    // $value['path'] = substr($setting['rootPath'], 1).$value['savepath'].$value['savename']; //在模板里的url路径
                    $fsave['savename'] = $value['savename'];
                    $fsave['savepath'] = $value['savepath'];
                    $fsave['create_time'] = $value['create_time'];
                    $res = $this->allowField(true)
                        ->isUpdate(true)
                        ->save($fsave, [
                        'id' => $value['id']
                    ]);
                    $skey = 'File_' . $value['id'];
                    S($key, null);
                    continue;
                } else {
                    
                    $value['savepath'] = $pathinfo['dirname'] . '/';
                    $value['savename'] = $value['name'];
                    $value['name'] = $value['old_name'];
                    // $value['path'] = substr($setting['rootPath'], 1).$value['savepath'].$value['savename']; //在模板里的url路径
                    /* 记录文件信息 */
                    $data = input('post.');
                    $id = $this->allowField(true)->insertGetId($value);
                    if ($id) {
                        $value['id'] = $id;
                    } else {
                        // TODO: 文件上传成功，但是记录文件信息失败，需记录日志
                        unset($info[$key]);
                    }
                }
            }
            return $info; // 文件上传成功
        } else {
            $this->error = isset($info['download']['msg']) ? $info['download']['msg'] : $info['msg'];
            return false;
        }
    }

    /**
     * 下载指定文件
     *
     * @param number $root
     *            文件存储根目录
     * @param integer $id
     *            文件ID
     * @param string $args
     *            回调函数参数
     * @return boolean false-下载失败，否则输出下载文件
     */
    public function download($root, $id, $callback = null, $args = null)
    {
        /* 获取下载文件信息 */
        $file = $this->where('id', $id)->find();
        if (! $file) {
            $this->error = '不存在该文件！';
            return false;
        }
        
        /* 下载文件 */
        switch ($file['location']) {
            case 0: // 下载本地文件
                $file['rootpath'] = $root;
                return $this->downLocalFile($file, $callback, $args);
            case 1: // TODO: 下载远程FTP文件
                break;
            default:
                $this->error = '不支持的文件存储类型！';
                return false;
        }
    }

    /**
     * 检测当前上传的文件是否已经存在
     *
     * @param array $file
     *            文件上传数组
     * @return boolean 文件信息， false - 不存在该文件
     */
    public function isFile($file)
    {
        if (empty($file['md5'])) {
            exception('缺少参数:md5');
        }
        /* 查找文件 */
        $map = array(
            'md5' => $file['md5']
        );
        return $this->field(true)
            ->where(wp_where($map))
            ->find();
    }

    /**
     * 下载本地文件
     *
     * @param array $file
     *            文件信息数组
     * @param callable $callback
     *            下载回调函数，一般用于增加下载次数
     * @param string $args
     *            回调函数参数
     * @return boolean 下载失败返回false
     */
    private function downLocalFile($file, $callback = null, $args = null)
    {
        if (is_file($file['rootpath'] . $file['savepath'] . $file['savename'])) {
            /* 调用回调函数新增下载数 */
            is_callable($callback) && call_user_func($callback, $args);
            
            /* 执行下载 */
            // TODO: 大文件断点续传
            header("Content-Description: File Transfer");
            header('Content-type: ' . $file['type']);
            header('Content-Length:' . $file['size']);
            if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { // for IE
                header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
            }
            readfile($file['rootpath'] . $file['savepath'] . $file['savename']);
            exit();
        } else {
            $this->error = '文件已被删除！';
            return false;
        }
    }

    function addFile($file)
    {
        $data['md5'] = md5_file($file['tmp_name']);
        $id = $this->where('md5', $data['md5'])->value('id');
        if ($id > 0) {
            return $id;
        }
        
        $info = pathinfo($file['tmp_name']);
        $data['name'] = $data['savename'] = $file['name'];
        $data['savepath'] = str_replace([
            SITE_PATH . '/public/uploads/download',
            $file['name']
        ], '', $file['tmp_name']);
        $data['ext'] = $info['extension'];
        $data['mine'] = $file['type'];
        $data['size'] = $file['size'];
        
        $data['sha1'] = hash_file('sha1', $file['tmp_name']);
        $data['create_time'] = NOW_TIME;

        $id = $this->insertGetId($data);
        return $id;
    }
}
