<?php
class Model
{
    private static $db_dir;    

    public static function getDataBaseDir(){
        return self::$db_dir;
    }

    public static function setDataBaseDir($db_dir)
    {
        self::$db_dir = $db_dir;
    }

    public static function getTableName(){
        return strtolower(static::class);
    }

    //
    // stringであればダブルクォーテーションをつけるヘルパーメソッド
    //
    public static function convert($value){
        if(gettype($value) == "string"){
            return "\"" . $value . "\"";
        }
        return $value;
    }


    //
    // SQLite3 Resultで全て取得するメソッド
    //
    public static function selectAll(){
        $db = new SQLite3(self::getDataBaseDir());
        $table_name = self::getTableName();
        $sql_cmd = "SELECT * FROM $table_name";
        $result = $db->query($sql_cmd);

        $all_taple = array();
        while($tuple = $result->fetchArray(SQLITE3_ASSOC)){
            $all_taple[] = $tuple;
        }
        $db->close();
        //検索結果を全て格納する配列（自分の型）
        $objects = array();
        foreach($all_taple as $taple){
            //自分のインスタンスを生成する
            $class_name = static::class;
            $object = new $class_name();
            //インスタンスのプロパティに検索結果を代入する
            foreach($object as $key => $value){
                $object->$key = $taple[$key];
            }
            $objects[] = $object;
        }
        return $objects;
    }

    

    //
    // 条件（等号）に一致するデータを全てをObject Arrayで取得するメソッド
    //
    public static function where($column, $value){
        $value = self::convert($value);
        $db = new SQLite3(self::getDataBaseDir());
        $table_name = self::getTableName();
        $sql_cmd = "SELECT * FROM $table_name WHERE $table_name.$column = $value";
        $result = $db->query($sql_cmd);
        //検索結果を全て格納する配列（SQLite3Result）
        $all_taple = array();
        while($taple = $result->fetchArray(SQLITE3_ASSOC)){
            $all_taple[] = $taple;
        }
        $db->close();
        //検索結果がない場合
        if(count($all_taple) == 0){
            return false;
        }
        //検索結果を全て格納する配列（自分の型）
        $objects = array();
        foreach($all_taple as $taple){
            //自分のインスタンスを生成する
            $class_name = static::class;
            $object = new $class_name();
            //インスタンスのプロパティに検索結果を代入する
            foreach($object as $key => $value){
                $object->$key = $taple[$key];
            }
            $objects[] = $object;
        }
        return $objects;
    }

    //
    // 条件（等号）に一致するデータを１つ取得するメソッド
    //
    public static function find($column, $value, $no = 0){
        $object = self::where($column, $value);
        if($object == false){
            return false;
        }
        return $object[$no];
    }

    //
    // 条件（等号）に一致するデータの数を取得するメソッド
    //
    public static function count($column, $value){
        $value = self::convert($value);
        $table_name = self::getTableName();
        $db = new SQLite3(self::getDataBaseDir());
        $result = $db->query("SELECT COUNT(*) AS cnt FROM $table_name WHERE $table_name.$column = $value");
        $cnt = $result->fetchArray(SQLITE3_ASSOC)["cnt"];
        $db->close();
        return $cnt;
    }

    //
    // 条件（等号）に一致するデータを削除するメソッド
    //
    public static function deleteColumn($column, $value){
        $value = self::convert($value);
        $table_name = self::getTableName();
        return self::query("DELETE FROM $table_name WHERE $table_name.$column = $value");
    }

    //
    // SQL文を実行して結果を取得するメソッド
    //
    public static function query($sql_cmd){
        var_dump($sql_cmd);
        $db = new SQLite3(self::getDataBaseDir());
        $result = $db->query($sql_cmd);
        $db->close();
        return $result;
    }

    //
    // データを更新するメソッド
    //
    public function update($column, $value){
        $value = self::convert($value);
        $table_name = self::getTableName();
        return self::query("UPDATE $table_name SET $column = $value WHERE id = $this->id");
    }

    //
    // データを削除するメソッド
    //
    public function delete($no = 0){
        self::deleteColumn("id", $this->id, $no);
    }

    //
    // データを新しく挿入するメソッド
    //
    public function save(){
        $db = new SQLite3(self::getDataBaseDir());
        $table_name = self::getTableName();
        //挿入するカラム（自分の各プロパティ一名）
        $set = array();
        //挿入する値（自分の各プロパティ）
        $values = array();
        //自分のプロパティ名を$keyとして回す
        foreach($this as $key => $value){
            if($key == "id"){
                continue;
            }
            $set[] = $key;
            //プロパティが文字列なら""をつける
            $values[] = self::convert($this->$key);
        }
        //「,」で繋げる
        $set = implode(", ", $set);
        $values = implode(", ", $values);
        $sql_cmd = "INSERT INTO $table_name ($set) VALUES ($values)";
        $db->query($sql_cmd);
        $db->close();
    }

}
?>
