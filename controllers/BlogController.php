<?php

class BlogController extends Controller
{
    
    public function indexAction(){
        header("Location: ./blog/top");
    }

    //トップページを表示する
    public function topAction()
    {
        $this->changeTemplate("index");
        $notes = Note::selectAll();
        $this->view->assign("notes", $notes);
    }

    //記事を表示するページ
    public function viewAction(){
        if(($note = Note::find("id", $this->params[0])) != null){
            $this->view->assign("note", $note);
        }
    }

    //記事を投稿する
    public function postAction()
    {
        $title = $this->request->getPost("title");
        $body = $this->request->getPost("body");
        if($title != null && $body != null){
            $note = new Note();
            $note->title = $title;
            $note->body = $body;
            $note->save();
        }
        header("Location: ./top");
    }

    //記事を削除する
    public function deleteAction()
    {
        $id = $this->request->getPost("delete");
        if($id != null){
            Note::deleteColumn("id", $id);
        }
        header("Location: ./top");
    }
}

?>
