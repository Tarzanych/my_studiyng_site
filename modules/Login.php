<?php

class Login extends Page {

    public $TemplateName = "404.php";

    public function OnBeforeDisplay() {
        global $db;
        if (!isset($_REQUEST['Action']))
            $_REQUEST['Action'] = '';
        switch ($_REQUEST['Action']) {
            case 'login':
                $data = array(
                    "success" => false,
                    "errors" => ""
                );
                $login = $_REQUEST['login'];
                $password = md5($_REQUEST['password']);
                $sql = $db->prepare("select * from `users` where `deleted`=0 and `nickname` = ? and `password` = ?");
                $sql->execute(array($login, $password));
                if ($sql->rowCount() > 0) {
                    $data['success'] = true;
                    $tempUser = $sql->fetch(PDO::FETCH_ASSOC);

                    $sql = $db->prepare("select * from `sessions` where `user_id` = ? and `lastActive` > UNIX_TIMESTAMP() - 3*24*3600 limit 1");
                    $sql->execute(array($tempUser['id']));
                    if ($sql->rowCount() > 0) {
                        $sess = $sql->fetch(PDO::FETCH_ASSOC);
                        $sql = $db->prepare("update `sessions` set `sess_id` = ? , `ip` = ? , `lastActive` = UNIX_TIMESTAMP() where `id` = ?");
                        $sql->execute(array(session_id(), $_SERVER['REMOTE_ADDR'], $sess['id']));
                    } else {
                        $sql = $db->prepare("insert into `sessions` (`sess_id`, `lastActive`, `user_id`, `ip`) values ( ? , UNIX_TIMESTAMP() , ? , ? ) ");
                        $sql->execute(array(session_id(), $tempUser['id'], $_SERVER['REMOTE_ADDR']));
                    }
                    $_SESSION['language'] = $tempUser['language'];
                    $sql = $db->prepare("update `users` set  `lastActive` = UNIX_TIMESTAMP() where `id` = ?");
                    $sql->execute(array($tempUser['id']));
                } else {
                    $data['errors'] = "Login error. Please check your nickname and password";
                }
                echo json_encode($data);
                exit;
                break;
        }
        if ($this->ReqUrl == "logout") {
            $db->exec("delete from `sessions` where `sess_id` = '" . session_id() . "'");
            $this->GoToUrl($this->RootUrl);
            exit;
        }
    }

}

?>