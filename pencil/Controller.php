<?php

abstract class Controller
{
    protected $app_path;
    protected $controller_name;
    protected $action_name;
    protected $view;
    protected $request;
    protected $template_path;

    public function __construct($app_path)
    {
        $this->request = new Request();
        $this->app_path = $app_path;
    }

    protected function initView()
    {
        $this->view = new Smarty();
        $this->view->template_dir = $this->app_path . "/views/templates/";
        $this->view->compile_dir =  $this->app_path . "/views/templates_c/";
        $this->template_path = $this->app_path . "/views/templates/" . $this->controller_name . "/" . $this->action_name . ".html";
    }

    protected function changeTemplate($template_name){
        $this->template_path = $this->app_path . "/views/templates/" . $this->controller_name . "/" . $template_name . ".html";
    }

    protected function commonAction_(){}

    public function indexAction(){}

    public function run($params)
    {
        $this->controller_name = $params[0];
        $this->action_name = $params[1];
        $this->params = array_splice($params, 2, 1);
        
		try {
            $this->initView();
            $this->commonAction_();
            $method_name = $this->action_name . "Action";
            $this->$method_name();
            $this->view->display($this->template_path);
        } catch (Exception $e) {
            var_dump($e);
        }
    }
}
?>
