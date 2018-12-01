<?php
namespace mumbaicat\makeapidoc;

class ApiDoc
{

    private $mainRegex = '/(\/\*\*.*?\*\sapi.*?\*\/\s*(public|private|protected)?\s*function\s+.*?\s*?\()/s';
    protected $documentPath;
    protected $savePath;
    protected $name = 'api';
    protected $controllerChange = true;
    protected $controllerTimes = 1;
    protected $methodChange = true;
    protected $methodTimes = 2;

    public static function test(){
        echo 'hello';
    }
    public function __construct($documentPath,$savePath=null)
    {
        $this->documentPath = $documentPath;
        if($savePath == null){
            $this->savePath = getcwd().DIRECTORY_SEPARATOR;
        }else{
            $this->savePath = $savePath;
        }
    }

    /**
     * 设置项目名称
     * @param string $name 项目名称
     * @return void
     */
    public function setName($name){
        $this->name = $name;
    }

    /**
     * 设置是否开启驼峰转匈牙利
     * @param bool $controller 文件名 true/false
     * @param bool $method 方法名 true/false
     * @return void
     */
    public function setChange($controller=true,$method=true){
        $this->controllerChange = $controller;
        $this->methodChange = $method;
    }

    /**
     * 驼峰转匈牙利转换条件 (出现几次大写字母才转换)
     * @param integer $controller 文件名
     * @param integer $method 方法名
     * @return void
     */
    public function setTimes($controller=1,$method=2){
        $this->controllerTimes = $controller;
        $this->methodTimes = $method;
    }

    /**
     * 大驼峰命名法转匈牙利命名法
     * @param string $str 字符串
     * @param integer $times 出现几次大写字母才转换,默认1次
     * @return string
     */
    private function humpToLine($str,$times=1){
        if(preg_match_all('/[A-Z]/',$str) >= $times){
            $str = preg_replace_callback('/([A-Z]{1})/',function($matches){
                return '_'.strtolower($matches[0]);
            },$str);
            if($str[0]=='_'){
                $str = substr_replace($str,'',0,1);
            }
            return $str;
        }
        return $str;
    }

    /**
     * 递归法获取文件夹下文件
     * @param string $path 路径
     * @param array $fileList 结果保存的变量
     * @param bool $all 可选,true全部,false当前路径下,默认true.
     */
    private function getFileList($path, &$fileList = [], $all = true)
    {
        if (!is_dir($path)) {
            $fileList = [];
            return;
        }
        $data = scandir($path);
        foreach ($data as $one) {
            if ($one == '.' or $one == '..') {
                continue;
            }
            $onePath = $path . DIRECTORY_SEPARATOR . $one;
            $isDir = is_dir($onePath);
            $extName = substr($one, -4, 4);
            if ($isDir == false and $extName == '.php') {
                $fileList[] = $onePath;
            } elseif ($isDir == true and $all == true) {
                $this->getFileList($onePath, $fileList, $all);
            }
        }
    }

    /**
     * 获取代码文件中所有可以生成api的注释
     * @param string $data 代码文件内容
     */
    private function catchEvery($data)
    {
        preg_match_all($this->mainRegex, $data, $matches);
        if (empty($matches[1])) {
            return [];
        } else {
            return $matches[1];
        }
    }

    /**
     * 解析每一条可以生成API文档的注释成数组
     * @param string $data 注释文本 catchEvery返回的每个元素
     * @param string $fileName 文件名
     * @return array
     */
    private function parse($data,$fileName)
    {
        $fileName = basename($fileName,'.php');
        $return = [];
        preg_match_all('/(public|private|protected)?\s*function\s+(.*?)\(/', $data, $matches);
        $return['funcName'] = !empty($matches[2][0]) ? $matches[2][0] : '[null]';
        preg_match_all('/\/\*\*\s+\*\s+(.*?)\s+\*\s+api\s+/s', $data, $matches);
        $return['methodName'] = !empty($matches[1][0]) ? $matches[1][0] : '[null]';
        preg_match_all('/\s+\*\s+api\s+(.*?)\s+(.*?)\s+(\s+\*\s+@)?.*/', $data, $matches);
        $return['requestName'] = !empty($matches[1][0]) ? $matches[1][0] : '[null]';
        $return['requestUrl'] = !empty($matches[2][0]) ? $matches[2][0] : '[null]';

        if($this->controllerChange == true){
            $return['requestUrl'] = str_replace('{controller}',$this->humpToLine($fileName,$this->controllerTimes),$return['requestUrl']);
        }
        if($this->methodChange == true){
            $return['requestUrl'] = str_replace('{method}',$this->humpToLine($return['funcName'],$this->methodTimes),$return['requestUrl']);
        }

        preg_match_all('/\s+\*\s+@param\s+(.*?)\s+(.*?)\s+(.*?)\s/', $data, $matches);
        if(empty($matches[1])){
            $return['param'] = [];
        }else{
            for($i=0;$i<count($matches[1]);$i++){
                $type = !empty($matches[1][$i]) ? $matches[1][$i] : '[null]';
                $var = !empty($matches[2][$i]) ? $matches[2][$i] : '[null]';
                $about = !empty($matches[3][$i]) ? $matches[3][$i] : '[null]';
                $return['param'][] = [
                    'type' => $type,
                    'var' => $var,
                    'about' => $about,
                ];
            }
        }
        preg_match_all('/\s+\*\s+@return\s+(.*?)\s+(.*?)\s+(.*?)\s/', $data, $matches);
        $return['return'] = [];
        if(empty($matches[1])){
            $return['return'] = [];
        }else{
            for($i=0;$i<count($matches[1]);$i++){
                $type = !empty($matches[1][$i]) ? $matches[1][$i] : '[null]';
                $var = !empty($matches[2][$i]) ? $matches[2][$i] : '[null]';
                $about = !empty($matches[3][$i]) ? $matches[3][$i] : '[null]';
                if(strpos($about,'*/') !== false){
                    $about = $var;
                    $var = '';
                }


                if($var!='*/' and $var!=''){
                    // echo "<script>console.log('{$fileName}-{$return['funcName']}-{$var}')</script>";
                    $return['return'][] = [
                        'type' => $type,
                        'var' => $var,
                        'about' => $about,
                    ];
                }

            }
        }
        return $return;
    }

    /**
     * 每个API生成表格
     * @param array $data 每个API的信息 由parse返回的
     * @return string html代码
     */
    private function makeTable($data){
        $return = '<div id="'.base64_encode($data['requestUrl']).'" class="api-main">
        <div class="title">'.$data['methodName'].'</div>
        <div class="body">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>
                        '.$data['requestName'].'
                        </th>
                        <th rowspan="3">
                        '.$data['requestUrl'].'
                        </th>
                    </tr>
                </thead>
            </table>
        </div>';
        if(count($data['param'])!=0){
            $return .= '                    <div class="body">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>
                            请求名称
                        </th>
                        <th>
                            请求类型
                        </th>
                        <th>
                            请求说明
                        </th>
                    </tr>
                </thead>
                <tbody>';
            foreach($data['param'] as $param){
                $return .= '<tr>
                <td>
                    '.$param['var'].'
                </td>
                <td>
                '.$param['type'].'
                </td>
                <td>
                '.$param['about'].'
                </td>
            </tr>';
            }
            $return .= '</tbody>
            </table>
        </div>';
        }
        if(count($data['return'])!=0){
            $return .= '<div class="body">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>
                            返回名称
                        </th>
                        <th>
                            返回类型
                        </th>
                        <th>
                            返回说明
                        </th>
                    </tr>
                </thead>
                <tbody>';
            foreach($data['return'] as $param){
                $return .= '<tr>
                <td>
                    '.$param['var'].'
                </td>
                <td>
                '.$param['type'].'
                </td>
                <td>
                '.$param['about'].'
                </td>
            </tr>';
            }
            $return .= '</tbody>
            </table>
        </div>';
        }

        $return .= ' <hr>
        </div>';

        return $return;
    }

    /**
     * 生成侧边栏
     * @param array $rightList 侧边列表数组
     * @return string html代码
     */
    private function makeRight($rightList){
        $return = '';
        foreach($rightList as $d => $file){
            $return .= '<blockquote class="layui-elem-quote layui-quote-nm right-item-title">'.$d.'</blockquote>
            <ul class="right-item">';
            foreach($file as $one){
                $return .= '<li><a href="#'.base64_encode($one['requestUrl']).'"><cite>'.$one['methodName'].'</cite><em>'.$one['requestUrl'].'</em></a></li>';
            }
            $return .= '</ul>';
        }

        return $return;
    }

    /**
     * 开始执行生成
     * @param bool $fetch 是否方法返回,make(true) 可以用来直接输出
     */
    public function make($fetch=false)
    {
        $fileList = array();
        $this->getFileList($this->documentPath,$fileList);
        $inputData = ''; // 主体部分表格
        $rightList = array(); // 侧边栏列表
        foreach($fileList as $fileName){
            $fileData = file_get_contents($fileName);
            $data = $this->catchEvery($fileData);
            foreach ($data as $one) {
                $infoData = $this->parse($one,$fileName);
                $rightList[basename($fileName)][] = [
                    'methodName' => $infoData['methodName'],
                    'requestUrl' => $infoData['requestUrl'],
                ];
                $inputData .= $this->makeTable($infoData);
            }
        }
        $tempData = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'temp.html');
        $tempData = str_replace('{name}',$this->name,$tempData);
        $tempData = str_replace('{main}',$inputData,$tempData);
        $tempData = str_replace('{right}',$this->makeRight($rightList),$tempData);
        $tempData = str_replace('{date}',date('Y-m-d H:i:s'),$tempData);
        if($fetch==false){
            file_put_contents($this->savePath.$this->name.'.html',$tempData);
        }else{
            return $tempData;
        }
    }

}
