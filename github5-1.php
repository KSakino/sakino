<?php 
        //データベース接続
	    $dsn = 'データベース名';
	    $user = 'ユーザー名';
	    $password = 'パスワード';
	    //データベース操作で発生したエラーを警告として表示する
	    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	    
	    //テーブルを作成
	    //IF NOT EXISTS を入れないと2回目以降に呼び出した時もテーブルを作ろうとしてしまう
	    $sql = "CREATE TABLE IF NOT EXISTS tbsa" 
		." ("
	    //id:自動で登録されているというナンバリング
	    . "id INT AUTO_INCREMENT PRIMARY KEY,"
	    //char(32)半角英数で32文字まで
	    //text:長めの文章も入る
	    ."name char(32),"
	    ."comment TEXT,"
	    ."date char(32),"
	    ."pass char(32)"
	    .");";
	    $stmt = $pdo->query($sql);
	    
        //POSTされたものを受け取り,変数にする
	    $name = $_POST["name"];
	    $comment = $_POST["com"];
	    $date=date("Y-m-d H:i");
	    $pass=$_POST["pass"];
        $num=$_POST["num"];
        $temponum=$_POST["temponum"];
        $dpass=$_POST["dpass"];
        $epass=$_POST["epass"];
        $editnum=$_POST["editnum"];
        
        //①新規投稿フォーム
        if(isset($_POST["submit"])&&$temponum==null&&$name!=null&&$comment!=null&&$pass!=null){
            
            //SQLをセット(insert文)
          	$sql = $pdo -> prepare("INSERT INTO tbsa (name,comment,date,pass) VALUES (:name, :comment,:date,:pass)");
	        //変数にパラメーターを割り当てる
	        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	        //SQL実行する
	        $sql -> execute();
            echo $name."さんコメントありがとうございます！<br>";
        }
    
        
        //②編集番号指定フォーム
        elseif(isset($_POST["edit"])&&$editnum!=null&&$epass!=null){
            //SQLをセット(select文でパスワードを探す)
            $id=$editnum;
            $sql = 'SELECT pass FROM tbsa WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetch(); 
        	//パスワードが正しければ、変数に代入する
        	if($results['pass']==$epass){
        	    //そのidの行の情報をすべて抜き出す
        	    $sql = 'SELECT * FROM tbsa WHERE id=:id ';
                $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
                $stmt->execute();                             // ←SQLを実行する。
                $results = $stmt->fetchAll(); 
                //その行の情報を変数に代入する
                foreach ($results as $row){
		        $editnum=$row['id'];
		        $editname=$row['name'];
	           	$editcom=$row['comment'];
	           	echo "投稿番号".$editnum."の名前/コメントを編集できます。(パスワード不要)<br>";
                }
	    	}
        	else{
        	    echo "パスワードが間違っています。<br>";
        	}
        	
        }
            
        //③編集投稿フォーム
        elseif(isset($_POST["submit"])&&$temponum!=null&&$name!=null&&$comment!=null){
            $id=$_POST["temponum"];
             $date=date("Y-m-d H:i");
            //SQL文を準備
            $sql = 'UPDATE tbsa SET 
            name=:name,comment=:comment,date=:date WHERE id=:id';
            //プレースホルダに新しい変数を与える
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            //実行する
            $stmt->execute();
            echo "<br>編集を受け付けました<br>";
        }

        //④削除フォーム
        elseif(isset($_POST["delate"])&&$num!=null&&$dpass!=null){
            $id=$num;
            //SQLをセット(select文でパスワードを探す)
            $sql = 'SELECT pass FROM tbsa WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetch(); 
            //パスワードが正しければ削除
            if($results['pass']==$dpass){
                $sql = 'delete from tbsa where id=:id';
	            $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	            $stmt->execute();
             }else{
                 echo "投稿が存在しないか、パスワードが間違っています。<br>";
             }
        }
        
        //表示
        $sql = 'SELECT * FROM tbsa';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
	    echo "<hr>";
	    }
       
        ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8" >
        <title>mission_5-1</title>
    </head>
    <body>
        
        <form action="" method="post"> 
           <!--新規投稿/編集フォーム-->
           <input type="text" name="name" placeholder="名前" value="<?php echo $editname?>" >
           <input type="text" name="com" placeholder="コメント" value="<?php echo $editcom?>" >
           <input type="text" name="pass" placeholder="パスワード" >
           <!--編集したい投稿番号を表示させる/最後に隠す-->
           <input type="hidden" name="temponum" value="<?php echo $editnum?>">
           <input type="submit" name="submit" value="送信">
           <br>
           <!--削除フォーム-->
           <input type="number" name="num" placeholder="削除対象番号">
           <input type="text" name="dpass" placeholder="パスワード">
           <input type="submit" name="delate" value="削除">
           <br>
           <!--編集番号指定用フォーム-->
           <input type="number" name="editnum" placeholder="編集対象番号">
           <input type="text" name="epass" placeholder="パスワード">
           <input type="submit" name="edit" placeholder="編集">
           
           
        </form>
 
  
  </body>
  </html>