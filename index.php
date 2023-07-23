<?php
$db_host = 'localhost';
$db_name = '数据库名称';
$db_username = '数据库用户名';
$db_password = '数据库密码';
//获取IP
$ip = $_SERVER["REMOTE_ADDR"];
//脏话处理函数
function filter_bad_words($input) {
    $filename = 'bad_words.txt';
    $bad_words = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $filtered_input = $input;
    foreach ($bad_words as $bad_word) {
        $replacement = str_repeat('*', mb_strlen($bad_word, 'UTF-8'));
        $filtered_input = str_ireplace($bad_word, $replacement, $filtered_input);
    }
    return $filtered_input;
}

$id = 0;


try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
} catch (PDOException $e) {
    throw new Exception($e->getMessage());// 抛出异常
} 

//查询数据库
$pdo->exec('set names utf8');
$sql="select * from message";
$smt=$pdo->query($sql);
$rows=$smt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ZH-CN">
<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="xcccx,php,留言,留言墙">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>留言墙</title>
    <link type="text/css" rel="stylesheet" href="css.css" /> 
</head>
<body>
    <div id="content">

        <h1 class="title">欢迎访问留言墙，在这里你可以随便留言但不能发布违规内容哦~(有过滤屏蔽词系统)</h1>

        <form method="post">
            <label for="name">输入名字</label>
            <input type="text" name="name">
            <br>
            <label for="content">输入内容</label>
            <textarea name="content" cols="30" rows="10"></textarea>
            <br>
            <button type="submit">提交</button>
        </form>

        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                //获取表单
                $name = $_POST['name'];
                $content = $_POST['content'];

                // 调用脏话处理函数
                $filtered_name = filter_bad_words($name);
                $filtered_content = filter_bad_words($content);

                if ($name && $content !== null) {
                    //插入数据库
                    $sql = "INSERT INTO message (name, content, ip) VALUES (:name, :content, :ip)";

                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $filtered_name);
                    $stmt->bindParam(':content', $filtered_content);
                    $stmt->bindParam(':ip', $ip);
                    $stmt->execute();

                    header("Refresh:0");
                    exit();

                    $pdo = null;
                } else {
                    echo '不能为空';
                }
            }
        ?>
        <?php
            foreach ($rows as $row) { 
                $msg_content = filter_bad_words($row['content']);
        ?>
        <!--msg <?php echo $id ?> begin-->

                <div id="container<?php echo $id ?>" class="container">
                    <div id="msg<?php echo $id ?>" class="msg">
                        <div class="msgheader"><?php echo $row['name'] ?></div>
                        <p><?php echo htmlspecialchars($msg_content) ?></p>
                    </div>
                </div>

                <script src="logic.js"></script>
                <script>
                    //获取元素
                    dragElement(document.getElementById('msg<?php echo $id ?>'));
                    // 获取屏幕的宽度和高度
                    var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                    var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
                    // 生成随机的左偏移和上偏移值
                    var randomLeft = Math.floor(Math.random() * screenWidth);
                    var randomTop = Math.floor(Math.random() * screenHeight);
                    // 设置元素的位置
                    var element = document.getElementById("msg<?php echo $id ?>"); //获取元素
                    element.style.position = "absolute";
                    element.style.left = randomLeft + "px";
                    element.style.top = randomTop + "px";
                </script>
        <!--msg <?php echo $id ?> end-->
        <?php 
        $id++;        
            }
        ?>
    </div>
    <footer id="rin-footer">
        <div>
          <p>bug/反馈<a href="http://wpa.qq.com/msgrd?v=3&uin=1432777209&site=qq&menu=yes" target="_blank">QQ</a></p>
          <p>Powered by <a href="https://xcccx.top" target="_blank">xcccx</a></p>
          <p>&copy; 2023 <a href="http://lyq.xcccx.top">xcccx的留言墙</a></p>
          <p><?php echo date("当前时间Y年m月d日 H:i:s"); ?></p>
        </div>
    </footer>
</body>
</html>
