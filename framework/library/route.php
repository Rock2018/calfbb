<?php
/* ========================================================================
 * 路由类
 * 主要功能,解析URL
 * ======================================================================== */
namespace  Framework\library;

class route
{
    public $ctrl;
    public $action;
    public $path;
    public $route;

    public function __construct()
    {
        $route = conf::all('route');
        //如果路由是第一种模式
        if($route['PATH_INFO']==2){
            $this->pathinfoTwo($route);

        }else{
            $this->pathinfoOne($route);
        }

    }

    public function pathinfoOne($route)
    {
        global $_G;
        if (isset($_SERVER['QUERY_STRING'])) {

            global $_GPC;
            if(isset($_GPC['m']) && isset($_GPC['c']) && isset($_GPC['a']) ){
                $this->module=$_GPC['m'];
                $this->ctrl=$_GPC['c'];
                $this->action=$_GPC['a'];
            }else{
                $this->module=$_G['config']['MASTER'];
                $this->ctrl = conf::get('DEFAULT_CTRL', 'route');
                $this->action = conf::get('DEFAULT_ACTION', 'route');
               // throw new \Exception('URL参数错误'.$_SERVER['QUERY_STRING'] );
            }

        }
    }

    public function pathinfoTwo($route)
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $pathStr = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);

            //丢掉?以及后面的参数
            $path = explode('?', $pathStr);

            //去掉多余的分隔符
            $path = explode('/', trim($path[0], '/'));

            if (isset($path[0]) && $path[0]) {
                $this->ctrl = $path[0];
            } else {
                $this->ctrl = $route['DEFAULT_CTRL'];
            }

            unset($path[0]);
            //检测是否包含路由缩写
            if (isset($route['ROUTE'][$this->ctrl])) {
                $this->action = $route['ROUTE'][$this->ctrl][1];
                $this->ctrl = $route['ROUTE'][$this->ctrl][0];
            } else {
                if (isset($path[1]) && $path[1]) {
                    $have = strstr($path[1], '?', true);
                    if ($have) {
                        $this->action = $have;
                    } else {
                        $this->action = $path[1];
                    }

                } else {
                    $this->action = $route['DEFAULT_ACTION'];
                }
                unset($path[1]);
            }

            $this->path = array_merge($path);

            $pathLenth = count($path);
            $i = 0;
            while ($i < $pathLenth) {
                if (isset($this->path[$i + 1])) {
                    $_GET[$this->path[$i]] = $this->path[$i + 1];
                }
                $i = $i + 2;
            }
        } else {

            $this->ctrl = conf::get('DEFAULT_CTRL', 'route');
            $this->action = conf::get('DEFAULT_ACTION', 'route');
        }
    }
    public function urlVar($num, $default = false)
    {
        if (isset($this->path[$num])) {
            return $this->path[$num];
        } else {
            return $default;
        }
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}