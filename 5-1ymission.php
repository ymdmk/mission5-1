<?php

//1と6は、最初と最後にやればよい
//1.データベースに接続する。
//2.実行したいSQL文をセットする。
//3.SQLに対してパラメーターをセットする。【任意】
//4.実際にSQLを実行する。
//5.結果を取得する。【任意】
//6.データーベースから切断する。

// データベースに接続設定
$dsn='mysql:dbname=データベース名;host=localhost';
$user='ユーザー名';
$password='パスワード';
$pdo = new PDO($dsn, $user, $password, 
array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));



 // bbsという名前のテーブル作成（もし存在しなかった場合）
// SQL文を変数に入れておく
 $sql = "CREATE TABLE IF NOT EXISTS bbs"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "password INT,"
    . "date datetime"
    .");";
// sqlを実行
$stmt = $pdo->query($sql);
//$stmtは変数名なのでほかの名前でも一応問題はない 	
        	
// フォームに送信された値を変数に変数に入れる
if(isset($_POST["name"])){$name = $_POST["name"];}
if(isset($_POST["comment"])){$comment = $_POST["comment"];}
if(isset($_POST["pass"])){$pass = $_POST["pass"];}
if(isset($_POST["delete"])){ $delete = $_POST["delete"];}//削除対象番号
if(isset($_POST["delNO"])){$delNO = $_POST["delNO"];}//削除用パスワードフォーム
if(isset($_POST["edit"])){$edit = $_POST["edit"];}//編集対象番号
if(isset($_POST["hiddenNO"])){$hiddenNO = $_POST["hiddenNO"];}//number3。新規か編集か見極め
if(isset($_POST["editNO"])){$editNO = $_POST["editNO"];}//編集用パスワードフォーム

$date = date("Y/m/d/ H:i:s");
        


//新規投稿
if(isset($name) && isset($comment) && isset($pass))
{echo "aa";
    if(empty($_POST["hiddenNO"])){
    
    
//  INSERT文を変数に格納
            $sql = ("INSERT INTO bbs (name, comment,password,date) 
            VALUES (:name, :comment, :password, :date)");
            //  prepareメソッドでSQLをセット
            $stmt = $pdo->prepare($sql);
            // 編集する値を代入
            $params = array(':name' => $name, ':comment' => $comment, 
            ':password' => $pass, ':date' => $date); 
            // バインドした値の変数をセットしてSQL実行
            $stmt->execute($params); }}//新規投稿ここまで
            
            
// // 編集フォームに投稿番号とパスワードを入力した場合、SELECTでデータを取得する
// // 編集フォームの編集対象番号とパスワードが空きでない場合
if(isset($edit) && isset($editNO)){
// セットする変数の定義
$id = $edit;
$password = $editNO;
 // SQL文を変数に入れておく
$sql = 'SELECT * FROM bbs WHERE id=:id AND password=:password ';
//id=$idにすると、セキュリティ上危険なので、代わりに:idを置く
//下のbindParamで、実際の値をバインドする
                    
 // 2.実行したいSQL文をセットする。prepareメソッドでSQLをセット
 //prepareには、後でexecuteが必要
$stmt = $pdo->prepare($sql); 
                    
// ３．プレースホルダーの値を変数にバインド
$stmt->bindParam(':id', $id, PDO::PARAM_INT); 
//bindParam(プレースホルダ名、実際に入る変数、型を指定)
//PDO::PARAM_INTは数値、STRは文字列を意味する
$stmt->bindParam(':password', $password, PDO::PARAM_INT); 
// ４．executeでクエリを実行
$stmt->execute();      
// ５．結果を全件、配列で取得
$results = $stmt->fetchall(); 
//取得したでーたを＄resultsに代入する
    }
    
// 編集機能
if(!empty($_POST["hiddenNO"]) && $_POST["submit"]=="送信"){
    echo "bb";
// セットする変数の定義
$id = $_POST['hiddenNO'];
$password = $pass;
// SQL文を変数に入れてお
echo "cc";
$sql = 'UPDATE bbs SET name=:name,comment=:comment,
date=:date WHERE id=:id AND password=:password';
//  prepareメソッドでSQLをセット
$stmt = $pdo->prepare($sql);
// プレースホルダーに変数をバインド
echo "dd";
$stmt->bindParam(':name', $newname, PDO::PARAM_STR);
$stmt->bindParam(':comment', $newcomment, PDO::PARAM_STR);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->bindParam(':password', $password, PDO::PARAM_INT);
$stmt->bindParam(':date', $newdate, PDO::PARAM_STR);
// 新たに挿入する値をセット	   
echo "ee";
$newname =  ($_POST["name"]);
    
$newcomment = ($_POST["comment"]);
$newdate = date("Y/m/d/ H:i:s");
echo "ff";
// SQLでクエリを実行
$stmt->execute();
     }



// 削除機能
        if( isset($delete) && isset($delNO) ){
            // セットする変数の定義
            $id = $delete;
            $password =$delNO;
            // SQL文を変数に入れておく
        	$sql = 'delete from bbs where id=:id AND password=:password' ;
            // 	prepareメソッドでSQLをセット
        	$stmt = $pdo->prepare($sql);
            //プレースホルダーの値を変数にバインド
        	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
        	$stmt->bindParam(':password', $password, PDO::PARAM_INT);
             // SQLでクエリを実行	
        	$stmt->execute();    
            
        }
        
?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>mission3-5-6</title>
    </head>
<body>
<form action="" method="post">
    <input type="text" name="name" value="<?php if(!empty($results)){
                     foreach ($results as $row){
		                  echo $row['name'];} } ?>" placeholder="名前">
    <input type="text" name="comment" 
    value="<?php if(!empty($results)){
                     foreach ($results as $row){
		                  echo $row['comment'];} } ?>" placeholder="コメント">
    <input type="text" name="pass"  placeholder="パスワード">
    <input type="number" name="hiddenNO" value="<?php echo $edit;?>">
     <input type="submit" name="submit" value="送信">
    <br>
    <input type="number" name="delete" placeholder="削除するナンバー"><br>
    <input type="text" name="delNO"  placeholder="パスワード">
    <input type="submit" name="submit2" value="削除">
    <br>
    <input type="number" name="edit" placeholder="編集番号"><br>
    <input type="text" name="editNO"  placeholder="パスワード">
    <input type="submit" name="submit3" value="編集">
</form>   
</body>
</html>
<?php
// テーブルの詳細ブラウザ表示
// SQL文を変数に入れておく
$sql = 'SELECT * FROM bbs';
// queryメソッドでSQLをセット
$stmt = $pdo->query($sql);
//結果を全件配列で取得 	
$results = $stmt->fetchAll();
// 取得した配列をループさせて表示
foreach ($results as $row){
echo $row['id'].',';
echo $row['name'].',';
echo $row['comment'].',';
echo $row['password'].',';
echo $row['date'].'<br>';
echo "<hr>";
        }
        

?>