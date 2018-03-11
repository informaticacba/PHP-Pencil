<?php

class IndexController extends Controller
{
    public function indexAction()
    {
        $this->view->assign("time", date("Y/m/d"));
    }

    //データベースを上書き新規作成するデバッグ用の動作
    public function debugAction()
    {
        $this->changeTemplate("index");
        $this->view->assign("time", date("Y/m/d H:i:s"));
        $db = new SQLite3("db/db.sqlite3");
        $result = $db->query("SELECT * FROM sqlite_master");
        $file_exist = false;
        while($res = $result->fetchArray()){
            if($res["name"] == "note"){
                $file_exist = true;
                var_dump(Note::selectAll());
            }
        }
        if($file_exist){
            $db->exec("DROP TABLE note");
            $db->exec("CREATE TABLE note (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, body TEXT)");
        }else{
            $db->exec("CREATE TABLE note (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, body TEXT)");
        }
        $db->close();
    }
}

?>
