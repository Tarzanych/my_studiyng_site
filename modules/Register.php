<?php

class Register extends Page {

    public $TemplateName = "register.php";
    public $ShowErrors = false;
    public $Nickname = "";
    public $Errors = array();
    public $AlreadyActivated = false;

    public function OnBeforeDisplay() {
        global $db, $User;
        if (!$User->IsAnonymous) {
            $this->GoToUrl($this->RootUrl . "profile/" . $User->Id);
        } else {
            if (isset($_REQUEST['Action'])) {
                switch ($_REQUEST['Action']) {
                    case "register":
                        $data = array("success" => false);
                        $nickname = (isset($_POST['nickname']) ? $_POST['nickname'] : "");
                        $password = (isset($_POST['password']) ? $_POST['password'] : "");
                        $passconfirm = (isset($_POST['passconfirm']) ? $_POST['passconfirm'] : "");
                        $email = (isset($_POST['email']) ? $_POST['email'] : "");
                        $firstname = (isset($_POST['firstname']) ? $_POST['firstname'] : "");
                        $secondname = (isset($_POST['secondname']) ? $_POST['secondname'] : "");

                        if (mb_strlen($nickname) < 3 || mb_strlen($nickname) > 25 || !$this->validateNick($nickname)) {
                            $this->Errors[] = "Invalid nickname";
                        }
                        if (!$this->validateName($firstname) || !$this->validateName($secondname)) {
                            $this->Errors[] = "Invalid name";
                        }
                        if (mb_strlen($password) < 3 || mb_strlen($password) > 25 || mb_strlen($password) < 3 || mb_strlen($passconfirm) > 25) {
                            $this->Errors[] = "Invalid passconfirm size";
                        }
                        if ($password != $passconfirm) {
                            $this->Errors[] = "Passwords doesn't match";
                        }
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $this->Errors[] = "Invalid email";
                        }
                        $sql = $db->prepare("select count(`id`) as 'cnt' "
                            . "from `users` where  `nickname` = ? ");
                        $sql->execute(array($nickname));

                        if ($sql->rowCount() > 0 && $sql->fetchObject()->cnt > 0) {
                            $this->Errors[] = 'User <span class="bold">' . $nickname . '</span> already registered';
                        }
                        $sql = $db->prepare("select count(`id`) as 'cnt'  "
                            . "from `users` where `email` = ? ");
                        $sql->execute(array($email));
                        if ($sql->rowCount() > 0 && $sql->fetchObject()->cnt > 0) {
                            $this->Errors[] = 'User with email <span class="bold">' . $email . '</span> is already registered';
                        }
                        if (count($this->Errors) == 0) {
                            $activateCode = $this->genRandomString();
                            $language = GetOne("select ifnull(`id`,1) from "
                                . "`languages` where 1 "
                                . "order by `default` desc limit 1");
                            $sql = $db->prepare("insert into `users` "
                                . "(`nickname`, "
                                . "`password`, "
                                . "`email`, "
                                . "`regDate`, "
                                . "`language`, "
                                . "`name`, "
                                . "`surname`, "
                                . "`activateCode`) "
                                . "values "
                                . "(:nickname ,"
                                . " :password ,"
                                . " :email ,"
                                . " UNIX_TIMESTAMP() ,"
                                . " :language,"
                                . " :firstname ,"
                                . " :secondname ,"
                                . " :activateCode )");
                            if ($sql) {
                                $arr = array(
                                    ":nickname" => $nickname,
                                    ":password" => md5($password),
                                    ":email" => $email,
                                    ":language" => $language,
                                    ":firstname" => $firstname,
                                    ":secondname" => $secondname,
                                    ":activateCode" => $activateCode
                                );
                                $exec = $sql->execute($arr);
                                if ($exec) {
                                    $this->TemplateName = "register-success.php";
                                    $letter = '<div style="font-style:bold; padding-bottom: 20px">Thank you for your registration on our site!</div>
						Please follow <a href="' . $this->RootUrl . 'register?Action=activate&code=' . $activateCode . '">this link</a> to activate your account<br />
						<br />
						Best regards,<br />
						Drupal [nOob] community';

                                    $headers = array();
                                    $headers[] = "MIME-Version: 1.0";
                                    $headers[] = "Content-type: text/plain; charset=UTF-8";
                                    $headers[] = "From: Drupal nOob community <" . $_SERVER['SERVER_ADMIN'] . ">";
                                    $headers[] = "Subject: Activation link";
                                    $headers[] = "X-Mailer: PHP/" . phpversion();

                                    mail($email, "Activation link", $letter, implode("\r\n", $headers));
                                    $db->exec("insert into `permissions` (`user_id`) values (LAST_INSERT_ID())");
                                } else {
                                    $this->Errors[] = "Database query error";
                                }
                            }
                        }
                        break;
                    case 'activate':
                        $success = false;
                        if (isset($_GET['code'])) {
                            $code = $_GET['code'];
                        } else {
                            $code = "";
                        }
                        if (mb_strlen($code) > 0) {
                            $sql = $db->prepare("select * from `users` where `deleted`=0 and `activateCode` = ? ");
                            if ($sql) {
                                $exec = $sql->execute(array($code));
                                if ($exec) {
                                    $user = $sql->fetch(PDO::FETCH_ASSOC);
                                    $success = true;
                                    if (!$user['active']) {
                                        $db->query("update `users` set "
                                            . "`active` = 1 where "
                                            . "`id` = {$user['id']}");
                                    } else {
                                        $this->AlreadyActivated = true;
                                    }
                                }
                            }
                        }
                        if ($success) {
                            $this->TemplateName = 'activation-success.php';
                        } else {
                            $this->TemplateName = 'activation-fail.php';
                        }
                        break;
                }
            }
        }
    }

}

?>