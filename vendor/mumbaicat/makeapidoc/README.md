# php注释自动生成api文档   v1.6
作者: [Dust](http://dust101.lofter.com)  
----
```  
composer require mumbaicat/makeapidoc
```  

## 注释示例:
	与普通注释互不影响，要生成记得在第三行开头要有 api 请求方式 url 。  
    注释中备注不要有空格，建议使用标点符号来断句。  
```
    /**
     * 获取所有列表
     * api GET api.php/index/index/all
     * @param integer $page 页数
     * @param integer $limit 每页个数
     * @return integer $code 状态码
     * @return string $msg 返回消息
     * @return array $void 结果
     */
    public function all($page,$limit){
    	// 地址中新增两个占位符写法
    	// api.php/index/{controller}/{method}
    	// {method} 会自动换成对应的方法名
    	// {controller} 会自动换成文件名(大驼峰会转成匈牙利)。
    	//--------------------------------
    	// 默认是文件名和方法名都开启大驼峰转换
    	// 文件名是大写字母出现1次以及以上就转换
    	// 方法名是大写字母出现2次以及以上就转换
    	// 可以通过下面方法去改变,参数1是文件名,参数2是方法名
	// $doc->setChange(true,true);
	// $doc->setTime(1,2);
    }

    /**
     * 获取我的列表
     * api GET api.php/index/index/my_list
     * @param integer $page 页数
     * @param integer $limit 每页个数
     * @return integer $code 状态码
     * @return string $msg 返回消息
     * @return array 一些数据
     */
    public function my_list($page,$limit){
    	.....
    }

    /**
     * 获取我的名称
     * api GET/POST api.php/index/index/get_my_name
     * @return string 名称
     */
    public function get_my_name(){
    	.....
    }

    /**
     * 添加数据
     * api POST api.php/index/index/insert
     * @param string $name 姓名
     * @param string $age 年龄
     * @return inetger $code 状态码
     */
    public function insert(){
    	.....
    }
```    

## 如何使用  

### 1.放置文件
	如果你是Thinkphp5，则将 extend 目录里的 mumbaicat 放在 TP5 的 extend 目录下 。

### 2.引入
	// use mumbaicat\apidoc\ApiDoc;    旧版
    use mumbaicat\makeapidoc\ApiDoc;    // 新版

### 3.在合适地方实例化
	$doc = new ApiDoc('../application');
	//参数1是代码目录，参数2是保存路径，参数2默认是当前路径。 注意斜杠,windows是/ ，Linux/Mac是\ ，建议使用PHP常常量 DIRECTORY_SEPARATOR
	$doc->setName('api');
	//设置项目名称，不写此行默认是api，生成 项目名称.html 的文件，注意保存路径下是否有同名的文件，会被覆盖。
	$doc->make();
	//生成 或者 echo $doc->make(true); 生成并返回页面.

### 4.查看文档
	项目名称.html ,默认是api.html  

## 将来版本:
 * 在线ajax  
 * 点击URL自动复制  
 * 兼容更低版本的PHP
 * ...  

## 截图:
![代码](https://github.com/mumbaicat/makeapidoc/raw/master/screenshot/code.png)  
![效果](https://github.com/mumbaicat/makeapidoc/raw/master/screenshot/html.png)  
![f12](https://github.com/mumbaicat/makeapidoc/raw/master/screenshot/request.png)  
