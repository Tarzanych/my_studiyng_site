<?

class Admin extends Page {

    public $TemplateName = "admin.php";
    public $UsersList = array();
// 		public $CreateSuccess = false;
    public $CatArray = array();
    public $Forbidden = true;
    public $ContentArray = array("count" => 0, "content" => array());
    public $perPage = 20;
    public $PageNum = 0;
// 		public $Languages = array();
    public $LangConstants = array();

    protected function LoadCategories($parent, $level = 0) {
        global $db;
        $categories = $db->query("select * from `category` where "
            . "`parent` = '{$parent}' order by `title`");
        foreach ($categories as $cat) {
            $cat['level'] = $level;
            $this->CatArray[] = $cat;
            if (GetOne("select count(`id`) from `category` where "
                    . "`parent`={$cat['id']}")) {
                $this->LoadCategories($cat['id'], $level + 1);
            }
        }
    }

    protected function LoadContent($category = 0) {
        global $db;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($page <= 0) {
            $page = 1;
        }
        $page_sql = ($page - 1) * $this->perPage;
        $this->PageNum = $page;
        $sql = $db->prepare("select  SQL_CALC_FOUND_ROWS * from `content` where "
            . ($category >= 0 ? "`category_id`= :cat" : "1")
            . " order by `createTime` desc "
            . "limit " . $page_sql . ", " . $this->perPage);
        if ($category >= 0) {
            $sql->execute(array(":cat" => $category));
        } else {
            $sql->execute(array());
        }
        $foundRows = GetOne("SELECT FOUND_ROWS();");
        $content = $sql->fetchAll(PDO::FETCH_ASSOC);
        unset($content['title']);
        foreach ($content as $id => &$c) {
            $c['title'] = array();
            foreach ($this->Languages as $lang) {
                $c['title'][$lang['abbr']] = GetOne("select `title` from "
                    . "`content_language` where "
                    . "`content_id`={$c['id']} and "
                    . "`language_id`={$lang['id']}");
            }
        }
        $this->ContentArray['count'] = $foundRows;
        $this->ContentArray['content'] = $content;
    }

    public function OnBeforeDisplay() {
        global $db, $User;
        if (isset($_GET['q'])) {
            unset($_GET['q']);
        }

        if (isset($User->Permissions) && ((isset($User->Permissions['root']) && $User->Permissions['root'] == 1) || (isset($User->Permissions['admin']) && $User->Permissions['admin'] == 1)) && !$User->IsBlocked && $User->IsActive) {
            $this->Forbidden = false;
            if (isset($_REQUEST['Action']) && $_REQUEST['Action'] != "") {
                switch ($_REQUEST['Action']) {
                    case "checkCategory":
                        $data = array("success" => false, "errors" => "");
                        if (isset($_POST['catId']) && GetOne("select count(`id`) from `category` where `id` = " . intval($_POST['catId'])) > 0) {
                            $data['success'] = true;
                            $data['category'] = $db->query("select * from `category` where `id`='" . intval($_POST['catId']) . "'")->fetch(PDO::FETCH_ASSOC);
                        }
                        echo json_encode($data);
                        exit;
                        break;
                    case "createConstant":
                        if (mb_strlen(trim($_POST['constantTitle'])) > 0 && count($_POST['constantVal']) == count($this->Languages) && GetOne("select count(`id`) from `languageConstants` where `var`=" . $db->quote($_POST['constantTitle'])) == 0) {
                            foreach ($_POST['constantVal'] as $key => $val) {
                                $db->exec("insert into `languageConstants` "
                                    . "(`language`,`var`,`val`) "
                                    . "values "
                                    . "(" . intval($key) . ", "
                                    . $db->quote(trim($_POST['constantTitle']))
                                    . ", " . $db->quote($val) . ")");
                            }
                            $this->GoToUrl($this->RootUrl . "admin/language");
                        }

                        break;
                    case "updateConstant":

                        if (mb_strlen(trim($_POST['constantTitle'])) > 0 && count($_POST['constantVal']) == count($this->Languages) && GetOne("select count(`id`) from `languageConstants` where `var`=" . $db->quote($_POST['constantTitle'])) > 0) {
                            foreach ($_POST['constantVal'] as $key => $val) {
                                $db->exec("update `languageConstants` "
                                    . "set `val`=" . $db->quote($val) . " where "
                                    . "`language`=" . intval($key) . " and "
                                    . "`var`=" . $db->quote(trim($_POST['constantTitle'])));
                            }
                            $this->GoToUrl($this->RootUrl . "admin/language");
                        }

                        break;
                    case "checkLangConstant":
                        $data = array("success" => false);
                        $var = isset($_POST['langVar']) ? trim($_POST['langVar']) : "";

                        if (mb_strlen($var) > 0 && GetOne("select count(`id`) from "
                                . "`languageConstants` where "
                                . "`var`=" . $db->quote($var)) > 0) {
                            $data['success'] = true;
                            $data['langVar'] = $var;
                            $data['langVals'] = array();
                            $sql = $db->query("select * from `languageConstants` where "
                                    . "`var`=" . $db->quote($var))->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($sql as $lang) {
                                $data['langVals'][$lang['language']] = $lang['val'];
                            }
                        }
                        echo json_encode($data);
                        exit;
                        break;
                    case "checkCreateCatForm":
                        $data = array("success" => false, "errors" => "");
                        $parent = (isset($_POST['catParent']) ? intval($_POST['catParent']) : 0 );
                        $pCheck = GetOne("select count(`id`) from `category` where "
                            . "`id`='{$parent}'");
                        $tCount = GetOne("select count(`id`) from `category` where "
                            . "`title`=" . $db->quote($_POST['title']));
                        $uCount = GetOne("select count(`id`) from `category` where "
                            . "`url`=" . $db->quote($_POST['url']));
                        if ($tCount > 0)
                            $data['errors'] .= "Another category with such title exists<br />";
                        if ($pCheck == 0 && $parent != 0)
                            $data['errors'] .= "Parent id error<br />";
                        if ($uCount > 0)
                            $data['errors'] .= "Another category with such URL exists<br />";
                        if (mb_strlen(trim($_POST['title'])) == 0)
                            $data['errors'] .= "Please enter category title<br />";
                        if (mb_strlen(trim($_POST['url'])) == 0)
                            $data['errors'] .= "Please enter category URL<br />";
                        if (mb_strlen(trim($data['errors'])) == 0)
                            $data['success'] = true;
                        echo json_encode($data);
                        exit;
                        break;

                    case "checkEditCatForm":
                        $data = array("success" => false, "errors" => "");
                        $id = (isset($_POST['catId']) ? intval($_POST['catId']) : 0 );
                        $parent = (isset($_POST['catParent']) ? intval($_POST['catParent']) : 0 );
                        $pCheck = GetOne("select count(`id`) from `category` "
                            . "where `id`='{$parent}'");
                        $tCount = GetOne("select count(`id`) from `category` "
                            . "where "
                            . "`id`<>'{$id}' and "
                            . "`title`=" . $db->quote($_POST['title']));
                        $uCount = GetOne("select count(`id`) from `category` "
                            . "where `id`<>'{$id}' and "
                            . "`url`=" . $db->quote($_POST['url']));
                        $newAuthor = (isset($_POST['catAuthor']) ? trim($_POST['catAuthor']) : "" );
                        if (mb_strlen($newAuthor) > 0) {
                            if (GetOne("select count(`id`) from `users` where "
                                    . "`nickname` = " . $db->quote($newAuthor)) == 0) {
                                $data['errors'] .= "User <b>{$newAuthor}</b> not found<br />";
                            }
                        }
                        if ($id == 0 || GetOne("select count(`id`) "
                                . "from `category` where `id`='{$id}'") == 0)
                            $data['errors'] .= "Category id error<br />";
                        if ($pCheck == 0 && $parent != 0)
                            $data['errors'] .= "Parent id error<br />";
                        if ($tCount > 0)
                            $data['errors'] .= "Another category with such title exists<br />";
                        if ($uCount > 0)
                            $data['errors'] .= "Another category with such URL exists<br />";
                        if (isset($_POST['title']) && mb_strlen(trim($_POST['title'])) == 0)
                            $data['errors'] .= "Please enter category title<br />";
                        if (isset($_POST['url']) && mb_strlen(trim($_POST['url'])) == 0)
                            $data['errors'] .= "Please enter category URL<br />";
                        if (mb_strlen($data['errors']) == 0)
                            $data['success'] = true;
                        echo json_encode($data);
                        exit;
                        break;

                    case "changePermission":
                        $data = array("success" => false);
                        $userId = isset($_REQUEST['userId']) ? intval($_REQUEST['userId']) : 0;
                        $permission = isset($_REQUEST['permission']) ? trim($_REQUEST['permission']) : "";
                        $val = isset($_REQUEST['val']) ? intval($_REQUEST['val']) : 0;
                        $sql = $db->prepare('select * from `users` where `id` = ? and `deleted`=0');
                        if ($sql) {
                            $exec = $sql->execute(array($userId));
                            if ($exec) {
                                $user = $sql->fetch(PDO::FETCH_ASSOC);
                                $permissions = $db->query("select * from `permissions` "
                                        . "where `user_id` = '{$user['id']}'")->fetch(PDO::FETCH_ASSOC);
                                switch ($permission) {
                                    case 'Administrator':
                                        if ($User->Permissions['root']) {
                                            $data['success'] = true;
                                            $data['permissions'] = array(
                                                array("permission" => "admin", "permValue" => 1),
                                                array("permission" => "create", "permValue" => 1),
                                                array("permission" => "edit", "permValue" => 1),
                                                array("permission" => "editOwn", "permValue" => 1),
                                                array("permission" => "delete", "permValue" => 1),
                                                array("permission" => "deleteOwn", "permValue" => 1),
                                                array("permission" => "publish", "permValue" => 1),
                                                array("permission" => "publishOwn", "permValue" => 1)
                                            );
                                            $db->exec("update `permissions` set "
                                                . "`admin`='1', "
                                                . "`create`='1', "
                                                . "`edit`='1', "
                                                . "`editOwn`=1, "
                                                . "`delete`='1', "
                                                . "`deleteOwn`='1', "
                                                . "`publish`='1', "
                                                . "`publishOwn`='1' "
                                                . "where "
                                                . "`user_id`='{$user['id']}'");
                                        }
                                        break;
                                    case 'User':
                                        $data['success'] = true;
                                        $data['permissions'] = array(
                                            array("permission" => "admin", "permValue" => 0),
                                            array("permission" => "create", "permValue" => 0),
                                            array("permission" => "edit", "permValue" => 0),
                                            array("permission" => "editOwn", "permValue" => 0),
                                            array("permission" => "delete", "permValue" => 0),
                                            array("permission" => "deleteOwn", "permValue" => 0),
                                            array("permission" => "publish", "permValue" => 0),
                                            array("permission" => "publishOwn", "permValue" => 0)
                                        );
                                        $db->exec("update `permissions` set "
                                            . "`admin`='0', "
                                            . "`create`='0', "
                                            . "`edit`='0', "
                                            . "`editOwn`=0, "
                                            . "`delete`='0', "
                                            . "`deleteOwn`='0', "
                                            . "`publish`='0', "
                                            . "`publishOwn`='0' "
                                            . "where "
                                            . "`user_id`='{$user['id']}'");


                                        break;
                                    case 'Editor':

                                        $data['success'] = true;
                                        $data['permissions'] = array(
                                            array("permission" => "admin", "permValue" => 0),
                                            array("permission" => "create", "permValue" => 1),
                                            array("permission" => "edit", "permValue" => 0),
                                            array("permission" => "editOwn", "permValue" => 1),
                                            array("permission" => "delete", "permValue" => 0),
                                            array("permission" => "deleteOwn", "permValue" => 1),
                                            array("permission" => "publish", "permValue" => 0),
                                            array("permission" => "publishOwn", "permValue" => 1)
                                        );
                                        $db->exec("update `permissions` set "
                                            . "`admin`='0', "
                                            . "`create`='1', "
                                            . "`edit`='0', "
                                            . "`editOwn`=1, "
                                            . "`delete`='0', "
                                            . "`deleteOwn`='1', "
                                            . "`publish`='0', "
                                            . "`publishOwn`='1' "
                                            . "where "
                                            . "`user_id`='{$user['id']}'");


                                        break;
                                    case 'admin':
                                        if ($User->Permissions['root'] == 1) {
                                            $data['success'] = true;
                                            if ($val == 0) {
                                                $data['permissions'] = array(
                                                    array("permission" => "admin", "permValue" => 0)
                                                );
                                                $db->exec("update `permissions` set `admin`='0' where `user_id`='{$user['id']}'");
                                            } else {
                                                $data['permissions'] = array(
                                                    array("permission" => "admin", "permValue" => 1),
                                                    array("permission" => "create", "permValue" => 1),
                                                    array("permission" => "edit", "permValue" => 1),
                                                    array("permission" => "editOwn", "permValue" => 1),
                                                    array("permission" => "delete", "permValue" => 1),
                                                    array("permission" => "deleteOwn", "permValue" => 1),
                                                    array("permission" => "publish", "permValue" => 1),
                                                    array("permission" => "publishOwn", "permValue" => 1)
                                                );
                                                $db->exec("update `permissions` "
                                                    . "set `admin`='1', "
                                                    . "`create`='1', "
                                                    . "`edit`='1', "
                                                    . "`editOwn`=1, "
                                                    . "`delete`='1', "
                                                    . "`deleteOwn`='1', "
                                                    . "`publish`='1', "
                                                    . "`publishOwn`='1' "
                                                    . "where "
                                                    . "`user_id`='{$user['id']}'");
                                            }
                                        }
                                        break;
                                    case 'create':
                                    case 'editOwn':
                                    case 'publishOwn':
                                    case 'deleteOwn':
                                        $data['success'] = true;
                                        $data['permissions'] = array(
                                            array("permission" => $permission, "permValue" => $val)
                                        );
                                        $db->exec("update `permissions` set "
                                            . "`{$permission}`='{$val}' "
                                            . "where `user_id`='{$user['id']}'");
                                        break;
                                    case "edit":
                                        $data['success'] = true;
                                        if ($val == 1) {
                                            $data['permissions'] = array(
                                                array("permission" => "edit", "permValue" => 1),
                                                array("permission" => "editOwn", "permValue" => 1),
                                            );
                                            $db->exec("update `permissions` set "
                                                . "`editOwn`='1', "
                                                . "`edit`='1' where "
                                                . "`user_id`='{$user['id']}'");
                                        } else {
                                            $data['permissions'] = array(
                                                array("permission" => "edit", "permValue" => 0)
                                            );
                                            $db->exec("update `permissions` set "
                                                . "`edit`='0' where "
                                                . "`user_id`='{$user['id']}'");
                                        }
                                        break;
                                    case "delete":
                                        $data['success'] = true;
                                        if ($val == 1) {
                                            $data['permissions'] = array(
                                                array("permission" => "delete", "permValue" => 1),
                                                array("permission" => "deleteOwn", "permValue" => 1),
                                            );
                                            $db->exec("update `permissions` set "
                                                . "`deleteOwn`='1', "
                                                . "`delete`='1' "
                                                . "where "
                                                . "`user_id`='{$user['id']}'");
                                        } else {
                                            $data['permissions'] = array(
                                                array("permission" => "delete", "permValue" => 0)
                                            );
                                            $db->exec("update `permissions` set "
                                                . "`delete`='0' "
                                                . "where `user_id`='{$user['id']}'");
                                        }
                                        break;
                                    case "publish":
                                        $data['success'] = true;
                                        if ($val == 1) {
                                            $data['permissions'] = array(
                                                array("permission" => "publish", "permValue" => 1),
                                                array("permission" => "publishOwn", "permValue" => 1),
                                            );
                                            $db->exec("update `permissions` set "
                                                . "`publishOwn`='1', "
                                                . "`publish`='1' "
                                                . "where `user_id`='{$user['id']}'");
                                        } else {
                                            $data['permissions'] = array(
                                                array("permission" => "publish", "permValue" => 0)
                                            );
                                            $db->exec("update `permissions` set "
                                                . "`publish`='0' where "
                                                . "`user_id`='{$user['id']}'");
                                        }
                                        break;
                                }
                            }
                        }
                        echo json_encode($data);
                        exit;
                        break;
                    case 'createCategory':
                        $title = isset($_POST['catTitle']) ? trim($_POST['catTitle']) : "";
                        $url = isset($_POST['catUrl']) ? trim($_POST['catUrl']) : "";
                        $parent = isset($_POST['catParent']) ? $_POST['catParent'] : 0;

                        $pCheck = GetOne("select count(`id`) from `category` "
                            . "where `id`='{$parent}'");
                        $tCount = GetOne("select count(`id`) from `category` "
                            . "where `title`=" . $db->quote($title));
                        $uCount = GetOne("select count(`id`) from `category` "
                            . "where `url`=" . $db->quote($url));
                        $description = $_POST['catDescription'] ? trim($_POST['catDescription']) : "";
                        $publish = isset($_POST['catPublish']) ? $_POST['catPublish'] : 0;
                        if (mb_strlen($title) > 0 && mb_strlen($url) > 0 && ($pCheck > 0 || $parent == 0) && $tCount == 0 && $uCount == 0) {
                            $sql = $db->prepare("insert into `category` "
                                . "(`title`, `url`, `parent`, `description`, "
                                . "`createTime`, `createUser`, `publish`) "
                                . "values "
                                . "(:title, :url, :parent, :description, "
                                . "UNIX_TIMESTAMP(), :user, :publish)");
                            $sql->execute(array(
                                ":title" => $title,
                                ":url" => $url,
                                ":parent" => $parent,
                                ":description" => $description,
                                ":user" => $User->Id,
                                ":publish" => $publish
                            ));
                            $_SESSION['CreateSuccess'] = true;
                            $this->GoToUrl($this->RootUrl . "admin/categories");
                        }
                        break;

                    case 'updateCategory':
                        $id = isset($_POST['catId']) ? intval($_POST['catId']) : 0;
                        if (GetOne("select count(`id`) from `category` where `id`='{$id}'") > 0) {
                            $title = isset($_POST['catTitle']) ? trim($_POST['catTitle']) : "";
                            $url = isset($_POST['catUrl']) ? trim($_POST['catUrl']) : "";
                            $parent = isset($_POST['catParent']) ? $_POST['catParent'] : 0;
                            $pCheck = GetOne("select count(`id`) from `category` "
                                . "where `id`='{$parent}'");
                            $tCount = GetOne("select count(`id`) from `category` "
                                . "where `id`<>'{$id}' and `title`=" . $db->quote($title));
                            $uCount = GetOne("select count(`id`) from `category` "
                                . "where `id`<>'{$id}' and `url`=" . $db->quote($url));
                            $author = isset($_POST['catAuthor']) ? trim($_POST['catAuthor']) : "";
                            $authorId = GetOne("select `createUser` from `category` where `id`='{$id}'");
                            if (mb_strlen($author) > 0) {
                                if (GetOne("select count(`id`) from `users` where "
                                    . "`nickname` = " . $db->quote($author)) > 0) {
                                    $authorId = GetOne("select `id` from `users` where "
                                        . "`nickname`=" . $db->quote($author));
                                }
                            }
                            $description = $_POST['catDescription'] ? trim($_POST['catDescription']) : "";
                            $publish = isset($_POST['catPublish']) ? $_POST['catPublish'] : 0;
                            if (mb_strlen($title) > 0 && mb_strlen($url) > 0 && $tCount == 0 && $uCount == 0 && ($pCheck > 0 || $parent == 0)) {
                                $sql = $db->prepare("update `category` set "
                                    . "`title` = :title, "
                                    . "`url` = :url, "
                                    . "`parent` = :parent, "
                                    . "`description` = :description, "
                                    . "`createUser` = :user, "
                                    . "`publish` = :publish where `id`= :id");
                                $sql->execute(array(
                                    ":id" => $id,
                                    ":title" => $title,
                                    ":url" => $url,
                                    ":parent" => $parent,
                                    ":description" => $description,
                                    ":user" => $authorId,
                                    ":publish" => $publish
                                ));
                                $_SESSION['UpdateSuccess'] = true;
                                $this->GoToUrl($this->RootUrl . "admin/categories");
                            }
                        }
                        break;
                    case 'deleteCategory':
                        $data = array("success" => false, "errors" => "");
                        $id = isset($_POST['catId']) ? intval($_POST['catId']) : 0;
                        if (GetOne("select count(`id`) from `category` where `id`='{$id}'") > 0) {
                            $category = $db->query("select * from `category` where "
                                . "`id`='{$id}'")->fetch(PDO::FETCH_ASSOC);
                            $db->exec("update `category` set "
                                . "`parent`='{$category['parent']}' "
                                . "where `parent`='{$category['id']}'");
                            $db->exec("update `content` set "
                                . "`category_id`='{$category['parent']}' where "
                                . "`category_id`='{$category['id']}'");
                            $db->exec("delete from `category` "
                                . "where `id`='{$category['id']}'");
                            $data['success'] = true;
                        } else {
                            $data['errors'] = "No such category";
                        }
                        echo json_encode($data);
                        exit;
                        break;
                    case 'deleteLanguageVar':
                        $data = array("success" => false, "errors" => "");
                        $langVar = isset($_POST['langVar']) ? trim($_POST['langVar']) : "";
                        if (GetOne("select count(`id`) "
                            . "from `languageConstants` where "
                            . "`var`=" . $db->quote($langVar)) > 0) {
                            $db->exec("delete from `languageConstants` where "
                                . "`var`=" . $db->quote($langVar));
                            $data['success'] = true;
                        } else {
                            $data['errors'] = "No such category";
                        }
                        echo json_encode($data);
                        exit;
                        break;
                }
            }
            if (!isset($this->QueryElements[1]) || $this->QueryElements[1] == "") {
                $this->GoToUrl($this->RootUrl . "admin/userslist");
            } else {
                switch ($this->QueryElements[1]) {
                    case 'userslist':
                        if (isset($this->QueryElements[2]) && $this->QueryElements[2] != "") {
                            switch ($this->QueryElements[2]) {
                                case 'delprofile':
                                    if (isset($this->QueryElements[3]) && is_numeric($this->QueryElements[3]) && GetOne("select count(`id`) from `users` where `id`=" . intval($this->QueryElements[3]))) {
                                        $sql = $db->query("select * from `users` "
                                            . "where `id`=" . intval($this->QueryElements[3]));
                                        if ($sql->rowCount() > 0) {
                                            $user = $sql->fetch(PDO::FETCH_ASSOC);
                                            $isAdmin = (GetOne("select count(`id`) "
                                                . "from `permissions` where "
                                                . "`user_id`={$user['id']} "
                                                . "and (`root`=1 or `admin`=1)") > 0 ? true : false );
                                            if ($user['deleted'] == 0 && !$isAdmin) {
                                                $db->exec("update `users` set "
                                                    . "`deleted`=1 where "
                                                    . "`id`={$user['id']}");
                                            }
                                        }
                                    }
                                    $this->GoToUrl($this->RootUrl . "admin/userslist" . (isset($_GET) ? "?" . http_build_query($_GET) : ""));

                                    break;
                                case 'blockprofile':
                                    if (isset($this->QueryElements[3]) && is_numeric($this->QueryElements[3]) && GetOne("select count(`id`) from `users` where `id`=" . intval($this->QueryElements[3]))) {
                                        $sql = $db->query("select * from `users` "
                                            . "where `id`=" . intval($this->QueryElements[3]));
                                        if ($sql->rowCount() > 0) {
                                            $user = $sql->fetch(PDO::FETCH_ASSOC);
                                            $isAdmin = (GetOne("select count(`id`) "
                                                . "from `permissions` where "
                                                . "`user_id`={$user['id']} and "
                                                . "(`root`=1 or `admin`=1)") > 0 ? true : false );
                                            if ($user['deleted'] == 0 && $user['blocked'] == 0 && !$isAdmin) {
                                                $db->exec("update `users` set "
                                                    . "`blocked`=1 where "
                                                    . "`id`={$user['id']}");
                                            }
                                        }
                                    }
                                    $this->GoToUrl($this->RootUrl . "admin/userslist" . (isset($_GET) ? "?" . http_build_query($_GET) : ""));
                                    break;
                                case 'unblockprofile':
                                    if (isset($this->QueryElements[3]) && is_numeric($this->QueryElements[3]) && GetOne("select count(`id`) from `users` where `id`=" . intval($this->QueryElements[3]))) {
                                        $sql = $db->query("select * from `users` "
                                            . "where `id`=" . intval($this->QueryElements[3]));
                                        if ($sql->rowCount() > 0) {
                                            $user = $sql->fetch(PDO::FETCH_ASSOC);
                                            $isAdmin = (GetOne("select count(`id`) "
                                                . "from `permissions` where "
                                                . "`user_id`={$user['id']} and "
                                                . "(`root`=1 or `admin`=1)") > 0 ? true : false );
                                            if ($user['deleted'] == 0 && $user['blocked'] == 1 && !$isAdmin) {
                                                $db->exec("update `users` set "
                                                    . "`blocked`=0 where "
                                                    . "`id`={$user['id']}");
                                            }
                                        }
                                    }
                                    $this->GoToUrl($this->RootUrl . "admin/userslist" . (isset($_GET) ? "?" . http_build_query($_GET) : ""));
                                    break;
                                default:
                                    $this->GoToUrl($this->RootUrl . "admin/userslist" . (isset($_GET) ? "?" . http_build_query($_GET) : ""));
                                    break;
                            }
                        }
                        $where = array();
                        $order_by = "";
                        if (!isset($_GET['order_by'])) {
                            $_GET['order_by'] = 'id_asc';
                        }
                        switch ($_GET['order_by']) {
                            case 'id_asc':
                                $order_by = "`id` asc";
                                break;
                            case 'id_desc':
                                $order_by = "`id` desc";
                                break;
                            case 'nickname_asc':
                                $order_by = "`nickname` asc";
                                break;
                            case 'nickname_desc':
                                $order_by = "`nickname` desc";
                                break;
                            case 'login_asc':
                                $order_by = "`lastActive` asc";
                                break;
                            case 'login_desc':

                                $order_by = "`lastActive` desc";
                                break;
                        }

                        $sql = $db->query("select * from `users` where 1 "
                            . "order by `deleted` asc , `blocked` asc, {$order_by}");
                        if ($sql->rowCount() > 0) {
                            $this->UsersList = $sql->FetchAll(PDO::FETCH_ASSOC);
                        }
                        foreach ($this->UsersList as &$user) {
                            $isAdmin = (GetOne("select count(`id`) "
                                . "from `permissions` where "
                                . "`user_id`={$user['id']} and "
                                . "(`root`=1 or `admin`=1)") > 0 ? true : false );
                            $user['isAdmin'] = $isAdmin;
                        }
                        $this->TemplateName = "admin-userslist.php";
                        break;
                    case 'categories':
                        $this->LoadCategories(0);
                        $this->TemplateName = "admin-categories.php";
                        break;
                    case 'content':
                        if (isset($this->QueryElements[2])) {
                            if ($this->QueryElements[2] == "all") {
                                $catId = -1;
                            } elseif ($this->QueryElements[2] == "without") {
                                $catId = 0;
                            } elseif (intval($this->QueryElements[2]) >= 0) {
                                $catId = intval($this->QueryElements[2]);
                            } else {
                                $catId = -1;
                            }
                        } else {
                            $catId = -1;
                        }
                        $this->LoadCategories(0);
                        $this->LoadContent($catId);
                        $this->TemplateName = "admin-content.php";
                        break;
                    case 'language':
// 							$languages = $db->query("select * from `languages` where 1 order by `id` desc")->fetchAll(PDO::FETCH_ASSOC);
// 							foreach ($languages as $lang) {
// 								$this->Languages[$lang['id']] = $lang['title'];
// 							}
                        $languageConstants = $db->query("select * "
                            . "from `languageConstants` "
                            . "where 1 "
                            . "order by `var` asc, `language` asc")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($languageConstants as $lang) {
                            $this->LangConstants[$lang['var']][$lang['language']] = $lang['val'];
                        }

                        $this->TemplateName = "admin-language.php";
                        break;
                    default:
                        $this->GoToUrl($this->RootUrl . "admin/userslist");
                        break;
                }
            }
        }

        if ($this->Forbidden) {
            $this->TemplateName = "admin-forbidden.php";
        }
    }

}

?>