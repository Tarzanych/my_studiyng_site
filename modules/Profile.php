<?php

class Profile extends Page {

    public $TemplateName = "profile.php";
    public $ProfileId = 0;
    public $MyProfile = false;
    public $Profile = array();
    public $ProfilePermissions = array();
    public $Error = true;
    public $ErrorMessage = "";
    public $OperationsErrors = "";
    public $Edit = false;
    public $Delete = false;
    public $ProfileAvatar = "no-avatar.png";

    protected function GetProfile($id) {
        global $db, $User;
        if ($id > 0) {
            $sql = $db->prepare("select * from `users` where "
                . "`deleted`=0 and `id` = ? ");
            if ($sql) {
                $sql->execute(array($id));
                if ($sql->rowCount() > 0) {
                    $this->Error = false;
                    $this->Profile = $sql->fetch(PDO::FETCH_ASSOC);
                    $this->ProfileId = $this->Profile['id'];
                    $sql = $db->query("select * from `permissions` where "
                        . "`user_id` = {$this->ProfileId}");
                    $this->ProfilePermissions = ($sql->rowCount() > 0 ? $sql->fetch(PDO::FETCH_ASSOC) : array());
                    if ($this->ProfileId == $User->Id) {
                        $this->MyProfile = true;
                    }
                    if (mb_strlen($this->Profile['avatar']) > 0 && file_exists(ABSOLUTE_PATH . "images/avatars/" . $this->Profile['avatar'])) {
                        $this->ProfileAvatar = $this->Profile['avatar'];
                    }
                } else {
                    $this->ErrorMessage = "No users found with this id";
                }
            } else {
                $this->ErrorMessage = "Database query error";
            }
        } else {
            $this->TemplateName = "404.php";
        }
    }

    public function OnBeforeDisplay() {
        global $db, $User;
        if (isset($this->QueryElements[1]) && is_numeric($this->QueryElements[1])) {
            $id = intval($this->QueryElements[1]);
            $this->GetProfile($id);
        } elseif ($this->QueryElements[1] == "editprofile" && isset($this->QueryElements[2]) && is_numeric($this->QueryElements[2])) {
            $id = intval($this->QueryElements[2]);
            $this->GetProfile($id);
            if (!$this->Error && isset($User->Permissions) && !$User->IsBlocked && (($this->MyProfile && $this->Profile['active'] == 1) || $User->Permissions['root'] == 1 || ($User->Permissions['admin'] == 1 && $this->ProfilePermissions['root'] == 0))) {
                if (isset($_REQUEST['Action'])) {
                    switch ($_REQUEST['Action']) {
                        case 'Update':

                            if (isset($_POST['firstname'])) {
                                $firstname = trim($_POST['firstname']);
                                if (!$this->validateName($firstname)) {
                                    $this->OperationsErrors .= "Invalid first name<br />";
                                }
                            }
                            if (isset($_POST['secondname'])) {
                                $secondname = trim($_POST['secondname']);
                                if (!$this->validateName($secondname)) {
                                    $this->OperationsErrors .= "Invalid first name<br />";
                                }
                            }
                            if (isset($_POST['email'])) {
                                $email = trim($_POST['email']);
                                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $this->OperationsErrors .= "Invalid email<br />";
                                }
                            }
                            $passchange = false;
                            if (isset($_POST['password']) && isset($_POST['passconfirm']) && (mb_strlen($_POST['password']) > 0 || mb_strlen($_POST['passconfirm'] > 0))) {
                                $password = $_POST['password'];
                                $passconfirm = $_POST['passconfirm'];
                                if (!$this->validatePasswords($password, $passconfirm)) {
                                    $this->OperationsErrors .= "Invalid passwords or passwords doesn't match<br />";
                                } else {
                                    $passchange = true;
                                }
                            }
                            $avatarUpload = false;
                            if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {

                                $allowedExts = array("gif", "jpeg", "jpg", "png");
                                $temp = explode(".", $_FILES['avatar']['name']);
                                $extension = end($temp);
                                if ((($_FILES['avatar']['type'] == "image/gif") || ($_FILES['avatar']['type'] == "image/jpeg") || ($_FILES['avatar']['type'] == "image/jpg") || ($_FILES['avatar']['type'] == "image/pjpeg") || ($_FILES['avatar']['type'] == "image/x-png") || ($_FILES['avatar']['type'] == "image/png")) && ($_FILES['avatar']['size'] < 700 * 1024) && in_array(mb_strtolower($extension), $allowedExts)) {
                                    if ($_FILES['avatar']['error'] > 0) {
                                        $this->OperationsErrors .= "Avatar upload failed<br />";
                                    } else {
                                        $newName = $this->genRandomString() . "." . $extension;
                                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], ABSOLUTE_PATH . "images/upload/" . $newName)) {
                                            include_once(ABSOLUTE_PATH . 'libraries/phpThumb/phpthumb.class.php');
                                            $phpThumb = new phpThumb();
                                            $phpThumb->setSourceFilename($this->RootUrl . "images/upload/" . $newName);
                                            $phpThumb->setParameter('w', 150);
                                            $phpThumb->setParameter('h', 150);
                                            $phpThumb->setParameter('q', 90);
                                            $phpThumb->setParameter('far', 1);
                                            $phpThumb->setParameter('bg', 'FFF');
                                            if ($phpThumb->GenerateThumbnail()) {
                                                if ($phpThumb->RenderToFile(ABSOLUTE_PATH . "images/avatars/" . $newName)) {
                                                    $avatarUpload = true;
                                                } else {
                                                    $this->OperationsErrors .= "Avatar generation failed<br />";
                                                }
                                            } else {
                                                $this->OperationsErrors .= "Avatar generation failed<br />";
                                            }
                                            @unlink(ABSOLUTE_PATH . "images/upload/" . $newName);
                                        } else {
                                            $this->OperationsErrors .= "Permissions error<br />";
                                        }
                                    }
                                } else {
                                    $this->OperationsErrors .= "Invalid file size or type<br />";
                                }
                            }
                            if (mb_strlen($this->OperationsErrors) == 0) {
                                $sql = $db->prepare("update `users` set "
                                    . "`name` = :name, "
                                    . "`surname` = :surname, "
                                    . "`email` = :email "
                                    . ($passchange ? ", `password` = :password " : "")
                                    . ($avatarUpload ? ", `avatar` = :avatar " : "")
                                    . " where `id` = :id");
                                if ($sql) {
                                    $execArr = array(
                                        ":name" => $firstname,
                                        ":surname" => $secondname,
                                        ":email" => $email,
                                        ":id" => $this->ProfileId
                                    );
                                    if ($passchange) {
                                        $execArr[":password"] = md5($password);
                                    }
                                    if ($avatarUpload) {
                                        $execArr[":avatar"] = $newName;
                                    }
                                    $exec = $sql->execute($execArr);
                                    if ($exec) {
                                        $_SESSION['updateSuccess'] = true;
                                        $this->GoToUrl($this->RootUrl . "profile/" . $this->ProfileId);
                                    } else {
                                        $this->OperationsErrors .= "Database query error<br />";
                                    }
                                } else {
                                    $this->OperationsErrors .= "Database query error<br />";
                                }
                            }
                            break;
                    }
                }
                $this->TemplateName = "profile-edit.php";
            } elseif (!$this->Error) {
                $this->Error = true;
                $this->ErrorMessage = "Sorry, you have no permissions for this operation";
            }
        } elseif ($this->QueryElements[1] == "delprofile" && isset($this->QueryElements[2]) && is_numeric($this->QueryElements[2])) {
            $id = intval($this->QueryElements[2]);
            $this->GetProfile($id);
            if (!$this->Error && !$User->IsBlocked && (($this->MyProfile && $this->Profile['active'] == 1) || $User->Permissions['root'] == 1 || ($User->Permissions['admin'] == 1 && $this->ProfilePermissions['root'] == 0))) {
                $sql = $db->prepare("update `users` set "
                    . "`deleted`=1 where `id` = ?");
                if ($sql) {
                    $exec = $sql->execute(array($this->ProfileId));
                    if ($exec) {
                        $db->exec("delete from `sessions` where "
                            . "`user_id`=" . $this->ProfileId);
                        $this->TemplateName = "profile-delete-success.php";
                    } else {
                        $this->OperationsErrors .= "Database query error<br />";
                    }
                } else {
                    $this->OperationsErrors .= "Database query error<br />";
                }
            } elseif (!$this->Error) {
                $this->Error = true;
                $this->ErrorMessage = "Sorry, you have no permissions for this operation";
            }
        } else {
            $this->TemplateName = "404.php";
        }
    }

}

?>