<?php

class Request
{
    private $query;
    private $post;

    public function __construct()
    {
        foreach($_GET as $key => $value){
            $this->query[$key] = $value;
        }
        foreach($_POST as $key => $value){
            $this->post[$key] = $value;
        }
    }

    public function getQuery($key = null)
    {
        if ($key == null) {
            return $this->query;
        }
        if (array_key_exists($key, $this->query) == false) {
            return null;
        }
        return $this->query[$key];
    }

    public function getPost($key = null)
    {
        if ($key == null) {
            return $this->post;
        }
        if (array_key_exists($key, $this->post) == false) {
            return null;
        }
        return $this->post[$key];
    }
}

?>
