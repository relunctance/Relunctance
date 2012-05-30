<?php
/**
 * Struct * 网站目录结构
 * 
 * @Package :	111
 * @Version :	$ID$
 * @Copyright :	Copyright
 * @Author :	Gao qilin <qilin@leju.sina.com.cn> 
 * @License :	PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class Struct {

	//框架中所有已经定义的常量
	STATIC private $const = array();

	/**
	 * init * 开始生产框架结构
	 * 
	 * @Access public
	 * @Return void
	 */
	STATIC public function init()
	{
		self::$const 		 = Myconst();
		self::$const['Classes']  = './Public/Classes/';
		self::$const['Controll'] = './' . rtrim(ltrim(APP_PATH ,'./') , '/') . '/Controll/';
		self::$const['Model']    = './' . rtrim(ltrim(APP_PATH ,'./') , '/') . '/Model/';
		self::$const['ActionModel']    = './' . rtrim(ltrim(APP_PATH ,'./') , '/') . '/Model/ActionModel/';
		self::$const['FactoryModel']    = './' . rtrim(ltrim(APP_PATH ,'./') , '/') . '/Model/FactoryModel/';
		self::$const['index']    = './home/View/'. trim(TEMPLATE_STYLE , '/') . '/index/' ;
		self::$const['Mycss']    = MYPUBLIC . 'css/';
		self::$const['Myimages'] = MYPUBLIC . 'images/';
		self::$const['Myjs']     = MYPUBLIC . 'js/';
		self::makeDirs(self::$const);					//调用创建目录
		self::createControll();
	}


	/**
	 * makeDirs * 创建目录
	 * 
	 * @Param $dirs 
	 * @Access private
	 * @Return void
	 */
	STATIC private function makeDirs($dirs){
		foreach($dirs as $key => $path){
			if(strrpos($path , '/')){
				if($key != 'WEB' && $key !='__ROOT__' && $key != 'APP_PATH'){
					$path = $GLOBALS['root']!= '/' ? str_replace($GLOBALS['root'], './', $path) : './' . ltrim($path,'./');
					!file_exists($path) ? mkdir($path , 0755 , TRUE) : '';
				}
			}
		}
	}

	/**
	 * CreateFile * 创建文件
	 * 
	 * @Param $path 
	 * @Param $content 
	 * @Access private
	 * @Return void
	 */
	STATIC private function CreateFile($path , $content )
	{
		!file_exists($path) ? file_put_contents($path , $content) : '';
	}


	/**
	 * createControll * 创建控制器 , 创建对应Success和error需要的CSS,JS,IMAGES,创建配置文件
	 * 
	 * @Access private
	 * @Return void
	 */
	STATIC private function createControll()
	{

		$CommonAction =<<<COMMONACTION
<?php
/**
 *这里是所有控制器共用的方法
 */
class CommonAction extends Action
{
	//所有的控制器操作都经过此方法
	function init()
	{
	}	
}
COMMONACTION;
		self::CreateFile( self::$const['Controll'] . 'CommonAction.class.php' , $CommonAction );

		$IndexAction =<<<INDEXACTION
<?php
/**
 *默认Index控制器中index方法
 */
class IndexAction extends CommonAction
{
	function index()
	{
		\$this->display('index');
	}
}
INDEXACTION;
		self::CreateFile( self::$const['Controll'] . 'IndexAction.class.php' , $IndexAction );

		$ConfigIncPhp = <<<CONFIGINCPHP
<?php
DEFINE('DB_DRIVER'		, 'pdo'	);
//DEFINE('DB_DRIVER'		, 'mongo');
DEFINE('MEMCACHE'		, 0	);		//是否开启Memcache
DEFINE('IS_SESSION_TO_MEMCACHE'	, 0	);		//是否保存Session到Memcache中
DEFINE('DB_CHARSET'		, 'utf8');
DEFINE('TPL_PREFIX'		, 'html');
DEFINE('LEFT_DELIMITER'  	, '<{'  );
DEFINE('RIGHT_DELIMITER' 	, '}>'  );

//主数据库
\$DB_MASTER = array(
	'db_host'=>'127.0.0.1',		//您的数据库地址
	'db_user'=>'root',		//数据库用户名
	'db_pass'=>'123456',		//数据库密码
	'db_name'=>'lt',		//库名
	'db_port'=>'3306',		//端口
	'db_prefix'=>'bbs_',		//表前缀
);

//从数据库
/**
 *
\$DB_SLAVE = array(

		array(
			'db_host'=>'192.168.8.45',
			'db_user'=>'root',
			'db_pass'=>'123456',
			'db_name'=>'lt',
			'db_port'=>'3306',
			'db_prefix'=>'bbs_',
		),
		array(
			'db_host'=>'192.168.8.44',
			'db_user'=>'root',
			'db_pass'=>'123456',
			'db_name'=>'lt',
			'db_port'=>'3306',
			'db_prefix'=>'bbs_',
		),
);
 */


/**如果需要切换数据库,请将此配置可放置在控制器中具体方法内,否则上面$DB_MASTER失效
\$GLOBALS['DB_OTHER']['MASTER'] = array(
	'db_host'=>'127.0.0.1',
	'db_user'=>'root',
	'db_pass'=>'123456',
	'db_name'=>'lt',
	'db_port'=>'3306',
	'db_prefix'=>'bbs_',
);
*/
/**同理MASTER
\$GLOBALS['DB_OTHER']['SLAVE'] = array(
);
 */

\$memServers = array(
//		'127.0.0.1'=>11211,
//		'www.a.com'=>11211,
//		'www.b.com'=>11211,
		);	
CONFIGINCPHP;
		self::CreateFile( './Public/Config.inc.php' , $ConfigIncPhp);
		include_once './Public/Config.inc.php';


		$UserFunction = './Public/Function/';
		!file_exists($UserFunction) ? mkdir($UserFunction , 0755 ) : '';
		self::CreateFile($UserFunction . 'Function.php' , "<?php\n//这里存放公共函数\n\n\n?".'>');

		$ClassesDir =  './Public/Classes/Common.class.php';
		self::CreateFile($ClassesDir , "<?php\n//这里存放公共类!命名格式为:demo.class.php\n//如果文件名首字母大写,那么类名首字母必须大写,如果文件名首字母小写,那么类名首字母也小写\n//本目录下所有类自动加载\nclass Common\n{\n\n\n}");




		//跳转页面开始
		$Success="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
        <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
		<title>".LEFT_DELIMITER."\$mess".RIGHT_DELIMITER."</title>
		<link type=\"text/css\" rel=\"stylesheet\" href=\"".LEFT_DELIMITER."\$public".RIGHT_DELIMITER."/css/successed.css\">		
		<script>
			var \$time = ".LEFT_DELIMITER."\$time".RIGHT_DELIMITER.";
			var \$url = ".LEFT_DELIMITER."if \$flag==0 && \$uurl==''".RIGHT_DELIMITER."'javascript:window.history.back();'".LEFT_DELIMITER."else".RIGHT_DELIMITER."'".LEFT_DELIMITER."\$uurl".RIGHT_DELIMITER."'".LEFT_DELIMITER."/if".RIGHT_DELIMITER.";
			var \$mess = '".LEFT_DELIMITER."\$mess".RIGHT_DELIMITER."';
		</script>
		<script src=\"".LEFT_DELIMITER."\$public".RIGHT_DELIMITER."/js/successed.js\"></script>
		<style>
			.success_a{
				width:350px;
				height:100px;
				font-size:12px;
				position:relative;
				z-index:1000;
			}
			.success_title{
				width:100%;
				height:20px;
				".LEFT_DELIMITER."if \$flag eq 1".RIGHT_DELIMITER."
					color:green;
				".LEFT_DELIMITER."else".RIGHT_DELIMITER."
					color:red;
				".LEFT_DELIMITER."/if".RIGHT_DELIMITER."
				line-height:20px;
				position:relative;
				z-index:1000;
			}
		</style>
                <script type=\"text/javascript\">
                        window.onload = function() {
                                new Alert('but', 'box', {
                                        title: \$mess,
					content: ' <div class=\"success_a\"> <div style=\"height:10px;width:100%;\"> </div> <div class=\"success_title\"> <div style=\"float:left; margin-left:5px;width:75px;text-align:center;\"><img src=\"".LEFT_DELIMITER."\$public".RIGHT_DELIMITER."/images/".LEFT_DELIMITER."if \$flag eq 1".RIGHT_DELIMITER."yes.png".LEFT_DELIMITER."else".RIGHT_DELIMITER."no.png".LEFT_DELIMITER."/if".RIGHT_DELIMITER."\" /> </div> <div style=\"float:left;margin-left:40px;text-align:left;width:230px;\"> '+\$mess+' </div> </div> <div style=\"margin-left:10px;background:;margin-top:20px;width:240px;float:left; color:#333333;\"> <span style=\"font-size:16px;color:red;margin-right:5px;\" id=\"sec\"> '+\$time+' </span> 秒后跳转,如果页面未跳转, <a href=\"'+\$url+'\"> 请点这里</a></div><div style=\"width:70px;float:left;margin-top:20px;\">  ".LEFT_DELIMITER."if \$debug ".RIGHT_DELIMITER."<button class=\"button\" style=\"width:50px;background:#11697E;\"> 停止 </button>".LEFT_DELIMITER."/if".RIGHT_DELIMITER." </div> </div> ' 
					,
                                        width: '',
                                        height: '',
                                        top: '',
                                        left: '',
                                        fixed: 'fixed',
                                        close: 'close'
                                });
				
				var time=\$time;
				var tt=setInterval(function(){
						time--;
						var seco=document.getElementById(\"sec\");
						seco.innerHTML=time;	
						if(time<=0){
						  time=1;
						}
				}, 1000);
                        };
                </script>
        </head>
        
        <body style=\"height:950px;\">
			<div  style=\"display:none;\">
				<a href=\"javascript:;\" id=\"but\"></a>
			</div>
        </body>

</html>
";
		$SuccessPath = $GLOBALS['root']!= '/' ? str_replace($GLOBALS['root'], './', MYPUBLIC) : './' . ltrim(MYPUBLIC,'./');
		$oldSuccessPath = glob( $SuccessPath . 'success.*', GLOB_BRACE);	
		if(!empty($oldSuccessPath)) 
			unlink($oldSuccessPath[0]); 	//当修改配置文件中TPL_PREFIX,或LEFT_DELIMITER时候重新生成success文件
		self::CreateFile( $SuccessPath . 'success.' . TPL_PREFIX , $Success);


		$indexPath = self::$const['index'] . 'index.' . TPL_PREFIX;
		$index  = <<<INDEX
index控制器 	<span style="color:blue;font-size:12px;">[欢迎使用Relunctance!]</span>
INDEX;
		self::CreateFile($indexPath , $index);


		$SucessJs=<<<JS
	var ttt = '';
	function \$(id) {
		return typeof id === "string" ? document.getElementById(id) : id;
	}
	function \$\$(oParent, elem) {
		return (oParent || document).getElementsByTagName(elem);
	}
	function \$\$\$(oParent, sClass) {
		var aElem = \$\$(oParent, '*');
		var aClass = [];
		var i = 0;
		for (i = 0; i < aElem.length; i++) if (aElem[i].className == sClass) aClass.push(aElem[i]);
		return aClass;
	}
	function Alert() {
		this.initialize.apply(this, arguments);
	}
	Object.extend = function(destination, source) {
		for (var property in source) {
			destination[property] = source[property];
		}
		return destination;
	};
	Alert.prototype = {
		initialize: function(obj, frame, onEnd) {
			if (\$(obj)) {
				var _this = this;
				this.obj = \$(obj);
				this.frame = frame;
				this.oEve(onEnd);
				this.oTitle = this.onEnd.title;
				this.oContent = this.onEnd.content;
				this.iWidth = this.onEnd.width;
				this.iHeight = this.onEnd.height;
				this.iTop = this.onEnd.top;
				this.iLeft = this.onEnd.left;
				this.iFixed = this.onEnd.fixed;
				this.iClose = this.onEnd.close;
				_this.create(),		//显示弹出层
				_this.backg();		//显示背景
				window.onresize = function() {
					_this.backg();
				};


				ttt=setTimeout(function(){
					demo();
				},\$time*1000);		//时间

			}
		},
		create: function() {
			this.oDiv = document.createElement('div');
			this.oAlert_backg = document.createElement('div');
			this.oAlert_frame = document.createElement('div');
			this.oTop_l = document.createElement('div');
			this.oTop_c = document.createElement('div');
			this.oTop_r = document.createElement('div');
			this.oCon = document.createElement('div');
			this.oCon_l = document.createElement('div');
			this.oCon_c = document.createElement('div');
			this.oCon_r = document.createElement('div');
			this.oBot_l = document.createElement('div');
			this.oBot_c = document.createElement('div');
			this.oBot_r = document.createElement('div');
			this.oDiv.id = this.frame;
			this.oAlert_backg.className = 'alert_backg';
			this.oAlert_frame.className = 'alert_frame';
			this.oTop_l.className = 'top_l';
			this.oTop_c.className = 'top_c';
			this.oTop_r.className = 'top_r';
			this.oCon.className = 'con';
			this.oCon_l.className = 'con_l';
			this.oCon_c.className = 'con_c';
			this.oCon_r.className = 'con_r';
			this.oBot_l.className = 'bot_l';
			this.oBot_c.className = 'bot_c';
			this.oBot_r.className = 'bot_r';
			document.body.appendChild(this.oDiv);
			this.box = \$(this.frame);
			this.box.appendChild(this.oAlert_backg);
			this.box.appendChild(this.oAlert_frame);
			this.oFra = \$\$\$(this.box, 'alert_frame')[0];
			this.oFra.appendChild(this.oTop_l);
			this.oFra.appendChild(this.oTop_c);
			this.oFra.appendChild(this.oTop_r);
			this.oFra.appendChild(this.oCon);
			this.oFra.appendChild(this.oBot_l);
			this.oFra.appendChild(this.oBot_c);
			this.oFra.appendChild(this.oBot_r);
			this.oCone = \$\$\$(this.box, 'con')[0];
			this.oCone.appendChild(this.oCon_l);
			this.oCone.appendChild(this.oCon_c);
			this.oCone.appendChild(this.oCon_r);
			this.tit = \$(this.frame);
			this.con = \$\$\$(this.tit, 'con_c')[0];
			this.oAlert_tit = document.createElement('div');
			this.oAlert_con = document.createElement('div');
			this.oH2 = document.createElement('h2');
			this.oAlert_tit.className = 'alert_tit';
			this.oAlert_con.className = 'alert_con';
			if (this.oTitle != "") {
				this.con.appendChild(this.oAlert_tit);
				this.con.appendChild(this.oAlert_con);
				this.oAlert_tit = \$\$\$(this.tit, 'alert_tit')[0];
				this.oH2.innerHTML = this.oTitle;
				this.oAlert_tit.appendChild(this.oH2);
			}
			this.content();
			this.width();
			this.height();
			this.top();
			this.left();
			this.fixed();
			this.close();
			this.Top = this.oFra.offsetTop;
			this.oFra.style.top = (this.Top - 40) + 'px';
			this.oFra.style.marginTop = 0;
			this.sMove(this.oFra, {
				top: this.Top,
				opacity: 100
			});
			this.sMove(this.oBackg, {
				opacity: 50
			});
		},
		oEve: function(onEnd) {
			this.onEnd = {};
			Object.extend(this.onEnd, onEnd || {});
		},
		content: function() {
			this.conent = \$\$\$(this.tit, 'alert_con')[0];
			this.conent == undefined ? this.con.innerHTML = this.oContent: this.conent.innerHTML = this.oContent;
			this.oButton = \$\$(this.tit, 'button');
			var i = 0;
			var _this = this;
			for (i = 0; i < this.oButton.length; i++) this.oButton[i].onclick = function() {
				clearTimeout(ttt)
				_this.em.onclick()
			};
		},
		width: function() {
			this.oBackg = \$\$\$(this.tit, 'alert_backg')[0];
			this.oFrame = \$\$\$(this.tit, 'alert_frame')[0];
			this.oCon = \$\$\$(this.oFrame, 'con')[0];
			this.oTop_c = \$\$\$(this.oFrame, 'top_c')[0];
			this.oCon_c = \$\$\$(this.oFrame, 'con_c')[0];
			this.oBot_c = \$\$\$(this.oFrame, 'bot_c')[0];
			this.oAlert_tit = \$\$\$(this.oFrame, 'alert_tit')[0];
			this.oAlert_con = \$\$\$(this.oFrame, 'alert_con')[0];
			if (this.iWidth != "") {
				this.oFrame.style.width = parseInt(this.iWidth) + 'px';
				this.oFrame.style.marginLeft = -parseInt(this.iWidth) / 2 + 'px';
				this.oTop_c.style.width = parseInt(this.iWidth) - 10 + 'px';
				this.oCon_c.style.width = parseInt(this.iWidth) - 10 + 'px';
				this.oBot_c.style.width = parseInt(this.iWidth) - 10 + 'px';
				this.oAlert_tit.style.width = parseInt(this.iWidth) - 12 + 'px';
				this.oAlert_con.style.width = parseInt(this.iWidth) - 10 + 'px';
			}
		},
		height: function() {
			if (this.iHeight != "") {
				this.oFrame.style.height = parseInt(this.iHeight) + 'px';
				this.oFrame.style.marginTop = -parseInt(this.iHeight) / 2 + 'px';
				this.oCon.style.height = parseInt(this.iHeight) - 10 + 'px';
				this.oAlert_con.style.height = parseInt(this.iHeight) - 40 + 'px';
			}
		},
		top: function() {
			if (this.iTop != "") this.oFrame.style.top = parseInt(this.iTop) + 'px',
			this.oFrame.style.marginTop = 0;
		},
		left: function() {
			if (this.iLeft != "") this.oFrame.style.left = parseInt(this.iLeft) + 'px',
			this.oFrame.style.marginLeft = 0;
		},
		backg: function() {
			this.oScrollHeight = document.documentElement.scrollHeight || document.body.scrollHeight;
			this.oScrollWidth = document.documentElement.scrollWidth || document.body.scrollWidth;
			this.oBackg.style.width = document.documentElement.clientWidth + (this.oScrollWidth - document.documentElement.clientWidth) + 'px';
			this.oBackg.style.height = document.documentElement.clientHeight + (this.oScrollHeight - document.documentElement.clientHeight) + 'px';
		},
		fixed: function() {
			if (this.iFixed == "fixed") {
				var _this = this;
				this.oFrame.style.position = 'fixed';
				this.oAlert_tit.style.cursor = 'move';
				this.oAlert_tit.onmousedown = function(e) {
					var _thisE = this;
					this.oEvent = e || event;
					this.X = this.oEvent.clientX - _this.oFrame.offsetLeft;
					this.Y = this.oEvent.clientY - _this.oFrame.offsetTop;
					document.onmousemove = function(e) {
						this.oEvent = e || event;
						this.L = this.oEvent.clientX - _thisE.X;
						this.T = this.oEvent.clientY - _thisE.Y;
						if (this.L < 0) {
							this.L = 0;
						} else if (this.L > document.documentElement.clientWidth - _this.oFrame.offsetWidth) {
							this.L = document.documentElement.clientWidth - _this.oFrame.offsetWidth
						}
						if (this.T < 0) {
							this.T = 0;
						} else if (this.T > document.documentElement.clientHeight - _this.oFrame.offsetHeight) {
							this.T = document.documentElement.clientHeight - _this.oFrame.offsetHeight;
						}
						_this.oFrame.style.left = this.L + 'px';
						_this.oFrame.style.top = this.T + 'px';
						_this.oFrame.style.margin = 0;
						return false;
					}
					document.onmouseup = function() {
						document.onmouseup = null;
						document.onmousemove = null;
					};
					return false;
				};
				if (this.oFrame) {
					if (!- [1, ] && !window.XMLHttpRequest) {
						document.documentElement.style.textOverflow = "ellipsis";
						this.oFrame.style.position = "absolute";
						this.oFrame.style.setExpression("top", "eval(documentElement.scrollTop + " + this.oFrame.offsetTop + ') + "px"');
					}
				}
			}
		},
		close: function() {
			if (this.iClose == "close" && this.oTitle != "") {
				var _this = this;
				this.clos = \$\$\$(this.tit, 'alert_tit')[0];
				var oEm = document.createElement('em');
				this.clos.appendChild(oEm);
				this.em = \$\$(this.tit, 'em')[0];
				this.em.onmouseover = function() {
					_this.em.className = 'hove';
				};
				this.em.onmouseout = function() {
					_this.em.className = '';
				};
				this.em.onclick = function() {
					//删除效果
					_this.sMove(_this.oFra, {
						top: (_this.Top - 40),
						opacity: 0
					},
					function() {
						document.body.removeChild(_this.em.parentNode.parentNode.parentNode.parentNode.parentNode);
					});
					_this.sMove(_this.oBackg, {
						opacity: 0
					});
				}
			}
		},
		getStyle: function(obj, attr) {
			return obj.currentStyle ? obj.currentStyle[attr] : getComputedStyle(obj, false)[attr];
		},
		sMove: function(obj, json, onEnd) {
			var _this = this;
			clearInterval(obj.timer);
			obj.timer = setInterval(function() {
				_this.dMove(obj, json, onEnd);
			},
			30);
		},
		dMove: function(obj, json, onEnd) {
			this.attr = '';
			this.bStop = true;
			for (this.attr in json) {
				this.iCur = 0;
				this.attr == 'opacity' ? this.iCur = parseInt(parseFloat(this.getStyle(obj, this.attr)) * 100) : this.iCur = parseInt(this.getStyle(obj, this.attr));
				this.iSpeed = (json[this.attr] - this.iCur) / 7;
				this.iSpeed = this.iSpeed > 0 ? Math.ceil(this.iSpeed) : Math.floor(this.iSpeed);
				if (json[this.attr] != this.iCur) this.bStop = false;
				if (this.attr == 'opacity') {
					obj.style.filter = 'alpha(opacity:' + (this.iCur + this.iSpeed) + ')';
					obj.style.opacity = (this.iCur + this.iSpeed) / 100;
				} else {
					obj.style[this.attr] = this.iCur + this.iSpeed + 'px';
				}
			}
			if (this.bStop) {
				clearInterval(obj.timer);
				if (onEnd) onEnd();
			}
		}
	};

	function demo(){
		window.location=\$url;		//跳转
	}
JS;

		$SucessJsPath = $SuccessPath . 'js/';
		self::CreateFile( $SucessJsPath . 'successed.js' , $SucessJs);

		$SuccessCss=<<<CSS
html, body {height:100%;}
body, h2, p {margin:0px;padding:0px; font-size:12px;}
body {font-size:12px; text-align:center; }
.alert_backg {width:100%;height:100%;top:0px;left:0px;position:absolute;background:#000;opacity:0;filter:alpha(opacity:0)}
.alert_frame {width:340px;height:120px;top:50%;left:50%;position:absolute;display:inline;margin:-60px 0 0 -170px;opacity:0;filter:alpha(opacity:0)}
.alert_frame .top_l {width:5px;height:5px;float:left;overflow:hidden;}
.alert_frame .top_c {width:330px;height:5px;float:left;overflow:hidden;}
.alert_frame .top_r {width:5px;height:5px;float:left;overflow:hidden;}
.alert_frame .con {width:100%;height:110px;float:left;overflow:hidden;}
.alert_frame .con_l {width:5px;height:100%;float:left;overflow:hidden;}
.alert_frame .con_c {width:330px;height:100%;float:left;overflow:hidden;background:#fff;}
.alert_frame .con_r {width:5px;height:100%;float:left;overflow:hidden;}
.alert_frame .bot_l {width:5px;height:5px;float:left;overflow:hidden;}
.alert_frame .bot_c {width:330px;height:5px;float:left;overflow:hidden;}
.alert_frame .bot_r {width:5px;height:5px;float:left;overflow:hidden;}
.alert_frame .alert_tit {width:328px;height:27px;float:left;color:#000;line-height:27px;border:1px solid #fff;}

.alert_frame .alert_tit h2 {float:left;text-indent:10px;font-size:14px;font-weight:bold;font-family:"\u5FAE\u8F6F\u96C5\u9ED1";}
.alert_frame .alert_tit em {width:8px;height:7px;overflow:hidden;float:right;cursor:pointer;}
.alert_frame .alert_tit em.hove {}
.alert_frame .alert_con {width:330px;height:81px;float:left;border-top:1px solid #e5e5e5;}
.size {width:100%;height:45px;text-align:center;line-height:45px;color:#000;}
.but {width:100%;height:22px;text-align:center;line-height:22px;}
.button {width:55px;height:22px;text-align:center;line-height:22px;color:#fff;}
.title {width:800px;height:35px;color:#fff;line-height:35px;font-family:Verdana;font-weight:bold;text-align:left;margin:20px auto 0 auto;font-size:24px;}
.table {width:800px;height:auto;overflow:hidden;margin:0 auto 20px auto;font-size:12px;border-top:1px solid #ddd;border-left:1px solid #ddd;background:#fff;}
.table ul {margin:0px;padding:0px;float:left;list-style-type:none;}
.table li {width:159px;height:30px;float:left;color:#3e3e3e;text-indent:10px;text-align:left;line-height:30px;font-family:Verdana,"微软雅黑"; border-right:1px solid #ddd;border-bottom:1px solid #ddd;}
.table li.td {color:#fff;font-size:14px;font-family:"微软雅黑";font-weight:bold;background:#424242;}
.table li.tit {width:479px;}
.table .tr {width:800px;height:30px;float:left;text-indent:10px;text-align:left;line-height:30px;font-size:14px;font-family:Verdana;border-right:1px solid #ddd;border-bottom:1px solid #ddd;}
.table .tr a {color:#ff6600;text-decoration:none;}
.table .tr a:hover {text-decoration:underline;}
CSS;
		$SucessCssPath = $SuccessPath . 'css/';
		self::CreateFile( $SucessCssPath . 'successed.css' , $SuccessCss);
		if( !file_exists($SuccessPath . 'images/yes.png') )
			copy('./Reluctance/Libs/Skip/yes.png' , $SuccessPath . 'images/yes.png');
		if( !file_exists($SuccessPath . 'images/no.png') )
			copy('./Reluctance/Libs/Skip/no.png' ,  $SuccessPath . 'images/no.png');
	}
}
