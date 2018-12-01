<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace app\home\model;

use app\common\model\Base;

/**
 * 图片模型
 * 负责图片的上传
 */
class Picture extends Base
{

    /**
     * 自动完成
     *
     * @var array
     */
    /*
     * protected $_auto = array(
     * array('status', 1, MODEL_INSERT),
     * array('create_time', NOW_TIME, MODEL_INSERT),
     * );
     */
    
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
    public function upload($files, $setting, $driver = 'Local', $config = null)
    {
        /* 上传文件 */
        $info = upload_files($setting, $driver, $config);
        if (isset($info['msg'])) {
            $this->error = $info['msg'];
            return false;
        }
        if (empty($info['download']['msg'])) { // 文件上传成功，记录文件信息
            foreach ($info as $key => &$value) {
                /* 已经存在文件记录 */
                if (isset($value['id']) && is_numeric($value['id'])) {
                    continue;
                }
                
                /* 记录文件信息 */
                $value['status'] = 1;
                $value['create_time'] = NOW_TIME;
                $value['path'] = substr($value['rootPath'], 1) . $value['savename']; // 在模板里的url路径
                $value['wpid'] = get_wpid();
                
                $id = $this->allowField(true)->insertGetId($value);
                $value['url'] = SITE_URL . $value['path'];
                if ($id) {
                    $value['id'] = $id;
                } else {
                    // 文件上传成功，但是记录文件信息失败，需记录日志
                    unset($info[$key]);
                }
            }
            return $info; // 文件上传成功
        } else {
            $this->error = $info['download']['msg'];
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
            throw new \Exception('缺少参数:md5');
        }
        /* 查找文件 */
        $map = array(
            'md5' => $file['md5'],
            'sha1' => $file['sha1']
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

    /**
     * 清除数据库存在但本地不存在的数据
     *
     * @param
     *            $data
     */
    public function removeTrash($data)
    {
        // $this->where( wp_where(array('id'=>$data['id'],) ))->delete();
    }

    function addFile($file)
    {
        $data['md5'] = md5_file($file['tmp_name']);
        $id = $this->where('md5', $data['md5'])->value('id');
        if ($id > 0) {
            return $id;
        }
        
        $info = pathinfo($file['tmp_name']);
        $data['path'] = str_replace(SITE_PATH . '/public', '', $file['tmp_name']);
        
        $data['sha1'] = hash_file('sha1', $file['tmp_name']);
        $data['create_time'] = NOW_TIME;
        $data['status'] = 1;
        $data['wpid'] = get_wpid();
        
        $id = $this->insertGetId($data);
        return $id;
    }

    public function getPictureInfoById($id)
    {
        return $this->where('id', $id)
                    ->find();
    }
}
