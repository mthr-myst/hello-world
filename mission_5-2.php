<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>mission5</title>
    <style>
        body{
            background-color: gray;
            padding: 1.5rem;
        }
    </style>
</head>

<body>
    <h1>エセ掲示板</h1>

    <?php
    $dsn='データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = "CREATE TABLE IF NOT EXISTS DB"
        . " ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date TEXT,"
        . "pass TEXT"
        . ");";
    $stmt = $pdo->query($sql);

    if (!empty($_POST["submit"])) {
        $comment = $_POST["comment"];
        $name = $_POST["name"];
        $hidden_number = $_POST["number"];
        $pass = $_POST["pass"];
        $date = date("Y/m/d H:i:s");

        if ($hidden_number) {
            $sql = 'UPDATE DB SET name = :name,comment = :comment WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            //穴埋めの状態で文を作る
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $hidden_number, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $sql = $pdo->prepare("INSERT INTO DB (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
            //"INSERT INTO テーブル名(列のタイトル) VALUES(各列の中身)"
            //prepareの場合ユーザからの入力を利用する。queryだとしない。
            //プリペアドステートメントで変更箇所だけ変数のようにする。
            $sql->bindParam(':name', $name, PDO::PARAM_STR);
            $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql->bindParam(':date', $date, PDO::PARAM_STR);
            $sql->bindParam(':pass', $pass, PDO::PARAM_STR);
            $sql->execute();
        }
        header("Location:" . $_SERVER['PHP_SELF']);
    } elseif (!empty($_POST["del_button"])) {
        $del = $_POST["delete"];
        $PASS = $_POST["PASS"];
        $sql = 'SELECT * FROM DB where id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $del, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
        }
        //削除対象のpassをとってくる
        if ($PASS == $row['pass']) {
            $sql = 'delete from DB where id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $del, PDO::PARAM_INT);
            $stmt->execute();
        }
    } elseif (!empty($_POST["edit_button"])) {
        //編集ボタンが押された時の挙動
        $edit = $_POST["edit"];
        $PASS = $_POST["PASS"];
        $sql = 'SELECT * FROM DB where id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $row) {
        }
        //編集対象のpassをとってくる
        if ($PASS == $row['pass']) {
            $sql = 'SELECT * FROM DB where id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            foreach ($results as $row) {
                $value0 = $row[0];
                $value1 = $row[1];
                $value2 = $row[2];
            }
        }
    }
    //表示パート
    $sql = 'SELECT * FROM DB';
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        echo $row['id'] . ' ';
        echo $row['name'] . ' ';
        echo $row['date'] . '<br>';
        echo $row['comment'] . '<br>';
        echo $row['pass'] . '<br>';
        echo "<hr>";    
    }
    
    ?>

    <!--htmlでページに表示するゾーン-->
    <form action="" method="post">
    <fieldset>
        <legend>投稿フォーム</legend>
        名前<br><input type="text" name="name" placeholder="名前" value=<?php if (!empty($value1)) {echo $value1;} ?>> <br>
        コメント<br><input type="text" name="comment" placeholder="本文" value=<?php if (!empty($value2)) {echo $value2;} ?>> <br>
        パスワード<br><input type="password" name="pass" placeholder="パスワード"><br>
        <input type="hidden" name="number" value=<?php if (!empty($value0)) {echo $value0;} ?>><br>
        <input type="submit" name="submit">
    </fieldset>
    </form><br>
    <form action="" method="post">
    <fieldset>
        <legend>削除フォーム</legend>
        削除したい投稿番号<br>
        <input type="number" name="delete" placeholder="削除対象番号"> <br>
        パスワード<br>
        <input type="password" name="PASS" placeholder="パスワード"> <br>
        <input type="submit" name="del_button">
    </fieldset>
    </form><br>
    <form action="" method="post">
    <fieldset>
        <legend>コメント編集フォーム</legend>
        編集したい投稿番号<br>
        <input type="number" name="edit" placeholder="編集対象番号"> <br>
        パスワード<br>
        <input type="password" name="PASS" placeholder="パスワード"> <br>
    <input type="submit" name="edit_button">
    </fieldset>
    </form><br><br>
    <hr>
</body>
</html>