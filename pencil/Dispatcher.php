<?php
class Dispatcher
{
    private $system_root;
    private $app_root;
    private $params;

    public function __construct($system_root, $app_root){
        $this->system_root = rtrim($system_root, "/");
        // document_root/app/controller/action?param=value => [document_root/, /controller/action?param=value]
        if($app_root == "."){
            $this->params = $_SERVER["REQUEST_URI"];
        }else{
            $this->params = explode($app_root, $_SERVER["REQUEST_URI"])[1];
        }
        $this->app_root = $app_root;
    }

    public function dispatch()
    {
        // /controller/action/param?param=xxx => [/controller/action/param, param=xxx]
        $request_url = explode("?", $this->params);
        // /controller/action/param/ => controller/action/param
        $param = ltrim(rtrim($request_url[0], "/"), "/");
        // hoge/foo => hoge, foo
        if ($param != "") {
            $params = explode("/", $param);
        }else{
            $params = array();
        }
        
        // Confirm first parameter as Controller.
        if (count($params) > 0) {
            // Generate instance from controller name.
            $controller = $this->getControllerInstance($params[0]);
            if ($controller == null) {
                header("HTTP/1.0 404 Not Found");
                exit;
            }
        }else{
            $params[] = "Index";
            $controller = $this->getControllerInstance($params[0]);
        }
        
        //Confirm second parameter as Action.
        if (count($params) > 1) {
            $action_name = $params[1];
            if (false == method_exists($controller, $params[1] . "Action")) {
                header("HTTP/1.0 404 Not Found");
                exit;
            }
        }else{
            $params[] = "Index";
        }
        $controller->run($params);
    }

    // Generate instance from controller name.
    private function getControllerInstance($controller_name)
    {
        // hello => HelloController
        $class_name = ucfirst(strtolower($controller_name)) . "Controller";
        $controller_file_path = "$this->system_root/$this->app_root/controllers/$class_name.php";
        // Check file exist.
        
        if (file_exists($controller_file_path) == false) {
            return null;
        }
        require_once $controller_file_path;
        if (class_exists($class_name) == false) {
            return null;
        }
        return new $class_name("$this->system_root/$this->app_root");
    }
}
?>
