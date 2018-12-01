<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use app\common\model\Base;

/**
 * 文档基础模型
 */
class Models extends Base
{

    protected $table = DB_PREFIX . 'model';

    /**
     * 检查列表定义
     *
     * @param type $data
     */
    protected function checkListGrid($data)
    {
        return !empty($data);
    }

    /**
     * 新增或更新一个文档
     *
     * @return boolean fasle 失败 ， int 成功 返回完整的数据
     * @author huajie <banhuajie@163.com>
     */
    public function updateModels()
    {
        /* 获取数据对象 */
        $data = input('post.');

        if (empty($data)) {
            return false;
        }
        /* 添加或新增基础内容 */
        if (empty($data['id'])) {
            // 新增数据
            $data['create_time'] = time();
            $data['id'] = $this->insertGetId($data); // 添加基础内容
            if (!$data['id']) {
                $this->error = '新增模型出错！';
                return false;
            }
        } else {
            // 更新数据
            $model = $this->where('id', $data['id'])->find(); // 先取旧的模型名
            $data['update_time'] = time();
            $status = $this->isUpdate(true)->save($data); // 更新基
            if (false === $status) {
                $this->error = '更新模型出错！';
                return false;
            } elseif ($model['name'] != $data['name']) {
                // 同时更新模型对应的数据表，先判断数据表是否存在
                $table_name = DB_PREFIX . strtolower($model['name']);
                $new_table_name = DB_PREFIX . strtolower($data['name']);

                $sql = "SHOW TABLES LIKE '{$table_name}'";
                $res = M()->query($sql);
                if (count($res)) {
                    $sql = "ALTER TABLE `{$table_name}` RENAME TO `{$new_table_name}`";
                    $this->execute($sql);
                }
            }
        }
        // 清除模型缓存数据
        S('DOCUMENT_MODEL_LIST', null);

        // 记录行为
        action_log('update_model', 'model', $data['id'], UID);

        $this->buildFile($data['id']);
        // 内容添加或更新完成
        return $data;
    }

    /**
     * 处理字段排序数据
     */
    protected function getSortFields($fields)
    {
        return empty($fields) ? '' : json_encode($fields);
    }

    protected function getAttribute($fields)
    {
        return empty($fields) ? '' : implode(',', $fields);
    }

    /**
     * 获取指定数据库的所有表名
     */
    public function getTables()
    {
        return $this->db->getTables();
    }

    /**
     * 根据数据表生成模型及其属性数据
     *
     * @author huajie <banhuajie@163.com>
     */
    public function generate($table, $name = '', $title = '')
    {
        // 新增模型数据
        if (empty($name)) {
            $name = $title = substr($table, strlen(DB_PREFIX));
        }
        $data = array(
            'name' => $name,
            'title' => $title
        );

        if ($data) {
            $res = $this->insertGetId($data);
            if (!$res) {
                return false;
            }
        } else {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    /**
     * 删除一个模型
     *
     * @param integer $id
     *            模型id
     * @author huajie <banhuajie@163.com>
     */
    public function del($id)
    {
        // 获取表名
        $model = $this->field(true)
            ->where('id', $id)
            ->find();
        $table_name = DB_PREFIX . strtolower($model['name']);

        // 删除模型数据
        $res = $this->where('id', $id)->delete();
        if ($res) {
            // 删除该表
            $sql = <<<sql
                DROP TABLE  IF EXISTS {$table_name};
sql;
            $res = M()->execute($sql);

            // 删除数据模型文件
            $this->delFile($model);
        }
        return $res !== false;
    }

    public function requireFile($model = [], $return_dir = false)
    {
        $name = parse_name($model['name'], 1);
        $model['addon'] = parse_name($model['addon']);
        $app_path = env('app_path');
        if (!empty($model['addon']) && $model['addon'] != 'core') {
            if (is_dir($app_path . $model['addon'])) {
                $dir = $app_path . $model['addon'] . '/data_table/';
            } else {
                $dir = $app_path . $model['addon'] . '/data_table/';
            }
        } else {
            $dir = $app_path . 'common/data_table/';
        }

        if ($return_dir) {
            return $dir;
        }

        $file = $dir . $name . 'Table.php';

        if (file_exists($file)) {
            return $file;
        } else {
            return false;
        }
    }

    public function delFile($model = [])
    {
        $file = $this->requireFile($model);
        $file === false || @unlink($file);
    }

    public function getFileInfo($model)
    {
        $type = gettype($model);
        if ($type != 'array' && $type != 'object') {
            if (is_numeric($model)) {
                $model = $this->find($model);
            } else {
                $map['name'] = $model;
                $model = $this->where($map)->find();
            }
        }

        $file = $this->requireFile($model);
        if ($file === false) {
            return false;
        }

        require_once $file;

        $name = parse_name($model['name'], 1);
        $class = $name . 'Table';

        $obj = new $class();
        // 补充默认字段后再输出
        foreach ($obj->list_grid as $n => &$g) {
            $g['name'] = $n;
            isset($g['function']) || $g['function'] = '';
            isset($g['width']) || $g['width'] = '';
            isset($g['is_sort']) || $g['is_sort'] = 0;
            isset($g['raw']) || $g['raw'] = 0;
            isset($g['come_from']) || $g['come_from'] = 0;
            isset($g['href']) || $g['href'] = [];
        }
        foreach ($obj->fields as $n => &$f) {
            $f['name'] = $n;
            isset($f['value']) || $f['value'] = '';
            isset($f['placeholder']) || $f['placeholder'] = '请输入内容';
            isset($f['remark']) || $f['remark'] = '';
            isset($f['is_show']) || $f['is_show'] = 0;
            isset($f['is_must']) || $f['is_must'] = 0;
            isset($f['extra']) || $f['extra'] = '';

            isset($f['validate_type']) || $f['validate_type'] = 'regex';
            isset($f['validate_rule']) || $f['validate_rule'] = '';
            isset($f['validate_time']) || $f['validate_time'] = 3;
            isset($f['error_info']) || $f['error_info'] = '';

            isset($f['auto_rule']) || $f['auto_rule'] = '';
            isset($f['auto_time']) || $f['auto_time'] = 3;
            isset($f['auto_type']) || $f['auto_type'] = 'function';

            if ($f['type'] == 'file') {
                isset($f['validate_file_exts']) || $f['validate_file_exts'] = '';
                isset($f['validate_file_size']) || $f['validate_file_size'] = 10485760;
            }
        }
        $obj->datatable_path = $file;
        return $obj;
    }

    public function buildFile($model, $fields = null, $list_grid = null, $config = null)
    {
        // dump ( $model );dump ( $fields );dump ( $list_grid );dump ( $config );
        if (empty($model) || ($fields === null && $list_grid === null && $config === null)) {
            return false;
        }
        $type = gettype($model);
        if ($type != 'array' && $type != 'object') {
            $model = $this->find($model);
        }

        $old = $this->getFileInfo($model);
        // dump($old);
        if ($old !== false) {
            $fields === null && $fields = $old->fields;
            $list_grid === null && $list_grid = $old->list_grid;
            $config === null && $config = $old->config;
        }
        if (empty($config)) {
            $config = $model;
        }
        $dir = $this->requireFile($config, true);
        $this->checkDataTablesDir($dir);

        $name = parse_name($config['name'], 1);
        // dump($config);exit;
        $configStr = $this->wp_var_export($config, 1);
        // dump ( $list_grid );
        // exit ();

        $list_grid_str = $this->wp_var_export($list_grid, 1);
        $fieldsArr = [];
        if (is_array($fields)) {
            foreach ($fields as $fname => $f) {
                unset($f['id'], $f['name'], $f['update_time'], $f['create_time'], $f['model_name'], $f['model_id'], $f['status']);
                if (empty($f['auto_rule'])) {
                    unset($f['auto_rule'], $f['auto_time'], $f['auto_type']);
                }
                if (empty($f['validate_rule'])) {
                    unset($f['validate_rule'], $f['validate_time'], $f['error_info'], $f['validate_type']);
                }
                foreach ($f as $k => $i) {
                    if ($i == '') {
                        unset($f[$k]);
                    }
                }
                $fieldsArr[$fname] = $f;
            }
        }
        $fieldsStr = $this->wp_var_export($fieldsArr, 1);

        $content = <<<str
<?php
/**
 * {$name}数据模型
 */
class {$name}Table {
    // 数据表模型配置
    public \$config = {$configStr};

    // 列表定义
    public \$list_grid = {$list_grid_str};

    // 字段定义
    public \$fields = {$fieldsStr};
}
str;
        $content = str_replace('&nbsp;', '  ', $content);
        // dump ( $content );
        // exit ();
        file_put_contents($dir . $name . 'Table.php', $content);
        if ($model['addon'] != $config['addon']) {
            // 删除旧文件
            $file = $this->requireFile($model);
            @unlink($file);
        }

        // 更新model数据库
        if (isset($model['id']) && !empty($model['id'])) {
            $this->save($config, [
                'id' => $model['id']
            ]);
        } else {
            $this->insertGetId($config);
        }
        return true;
    }

    public function wp_var_export($arr, $count)
    {
        if (empty($arr))
            return '[ ]';

        $html = $space = '';
        for ($i = 0; $i < $count; $i++) {
            $space .= '&nbsp;';
        }
        foreach ($arr as $k => $v) {
            $v_html = '';
            if (is_array($v)) {
                $v = $this->wp_var_export($v, $count + 2);
                if (empty($v)) {
                    $v_html = "'{$k}' => [ ]," . PHP_EOL;
                } else {
                    $v_html = "'{$k}' => {$v}," . PHP_EOL;
                }
            } elseif (is_numeric($v)) {
                $v_html = "'{$k}' => {$v}," . PHP_EOL;
            } else {
                $v = addslashes($v);
                $v_html = "'{$k}' => '{$v}'," . PHP_EOL;
            }

            $html .= $space . '&nbsp;&nbsp;' . $v_html;
        }

        if (empty($html)) {
            return '[ ]';
        } else {
            $html = trim($html);
            $html = rtrim($html, ",");
            $html = '[' . PHP_EOL . $html . PHP_EOL . $space . ']';
        }
        return $html;
    }

    public function checkDataTablesDir($dir)
    {
        if (is_dir($dir)) {
            return true;
        }

        return mkdirs($dir);
    }

    /**
     * 检查同一张表是否有相同的字段
     *
     * @author huajie <banhuajie@163.com>
     */
    public function checkName()
    {
        $name = I('post.name');
        $model_id = I('post.model_id');
        $id = I('post.id');
        $map = array(
            'name' => $name,
            'model_id' => $model_id
        );
        if (!empty($id)) {
            $map['id'] = array(
                'neq',
                $id
            );
        }
        $res = $this->where($map)->find();
        return empty($res);
    }

    /**
     * 检查当前表是否存在
     *
     * @param intger $model_id
     *            模型id
     * @return intger 是否存在
     * @author huajie <banhuajie@163.com>
     */
    public function checkTableExist($model)
    {
        // 当前操作的表
        if (is_numeric($model)) {
            $name = $this->where([
                'id' => $model
            ])->value('name');
        } elseif (isset($model['name'])) {
            $name = $model['name'];
        } else {
            $name = $model;
        }

        $table_name = $this->table_name = DB_PREFIX . strtolower($name);
        $sql = "SHOW TABLES LIKE '{$table_name}'";
        $res = $this->query($sql);
        return count($res);
    }

    /**
     * 新建表字段
     *
     * @param array $field
     *            需要新建的字段属性
     * @return boolean true 成功 ， false 失败
     * @author huajie <banhuajie@163.com>
     */
    public function addField($field)
    {
        // 检查表是否存在
        $table_exist = $this->checkTableExist($field['model_id']);

        // 获取默认值
        if ($field['value'] === '') {
            $default = '';
        } elseif (is_numeric($field['value'])) {
            $default = ' DEFAULT ' . $field['value'];
        } elseif (is_string($field['value'])) {
            $default = ' DEFAULT \'' . $field['value'] . '\'';
        } else {
            $default = '';
        }

        if ($table_exist) {
            $sql = <<<sql
                ALTER TABLE `{$this->table_name}` ADD COLUMN `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}';
sql;
        } else {
            // 新建表时是否默认新增“id主键”字段
            $model_info = M('model')->field('engine_type,need_pk')->getById($field['model_id']);
            if ($model_info['need_pk']) {
                $sql = <<<sql
                CREATE TABLE IF NOT EXISTS `{$this->table_name}` (
                `id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键' ,
                `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}' ,
                PRIMARY KEY (`id`)
                )ENGINE={$model_info['engine_type']} DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
sql;
            } else {
                $sql = <<<sql
                CREATE TABLE IF NOT EXISTS `{$this->table_name}` (
                `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}'
                )ENGINE={$model_info['engine_type']} DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
sql;
            }
        }
        $res = $this->execute($sql);
        return $res !== false;
    }

    /**
     * 更新表字段
     *
     * @param array $field
     *            需要更新的字段属性
     * @return boolean true 成功 ， false 失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateField($field, $old)
    {
        // 检查表是否存在
        $table_exist = $this->checkTableExist($field['model_id']);

        // 获取原字段名
        $last_field = $old['name'];

        // 获取默认值
        if ($field['value'] === '') {
            $default = '';
        } elseif (is_numeric($field['value'])) {
            $default = ' DEFAULT ' . $field['value'];
        } elseif (is_string($field['value'])) {
            $default = ' DEFAULT \'' . $field['value'] . '\'';
        } else {
            $default = '';
        }

        $sql = <<<sql
            ALTER TABLE `{$this->table_name}` CHANGE COLUMN `{$last_field}` `{$field['name']}`  {$field['field']} {$default} COMMENT '{$field['title']}' ;
sql;

        $res = $this->execute($sql);
        return $res !== false;
    }

    /**
     * 删除一个字段
     *
     * @param array $field
     *            需要删除的字段属性
     * @return boolean true 成功 ， false 失败
     * @author huajie <banhuajie@163.com>
     */
    public function deleteField($field, $model_id)
    {
        // 检查表是否存在
        $table_exist = $this->checkTableExist($model_id);

        $sql = <<<sql
            ALTER TABLE `{$this->table_name}` DROP COLUMN `{$field['name']}`;
sql;
        $res = $this->execute($sql);
        return $res !== false;
    }

    public function parseExtra($extra, $val = null)
    {
        $arr = parse_config_attr($extra);

        if ($val !== null) {
            return isset($arr[$val]) ? $arr[$val] : '';
        } else {
            return $arr;
        }
    }
}
