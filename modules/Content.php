<?php

class Content extends Page {

    public $TemplateName = "content.php";
    public $Content = array();
    public $FullUrl = "";
    public $CatArray = array();
    public $comments = 10;

    public function OnBeforeDisplay() {
        global $db, $User;
        if (isset($_REQUEST['Action'])) {
            switch ($_REQUEST['Action']) {
                case "SetVote":
                    $data = array("success" => false);
                    $vote = intval($_POST['vote']);
                    if (GetOne("select count(`id`) from `content` where "
                            . "`id`=" . intval($_POST['content']))) {
                        $content_exists = true;
                    } else {
                        $content_exists = false;
                    }
                    if (GetOne("select count(`id`) from `votes` where "
                            . "`content_id`=" . intval($_POST['content']) . " and "
                            . "`user_id`={$User->Id}")) {
                        $has_voted = true;
                    } else {
                        $has_voted = false;
                    }
                    if ($vote > 0 && $vote < 6 && isset($User) && $User->IsActive && !$User->IsBlocked && $content_exists && !$has_voted) {
                        $db->exec("insert into `votes` "
                            . "(`vote`,`content_id`,`user_id`) "
                            . "values "
                            . "({$vote}," . intval($_POST['content'])
                            . ",'{$User->Id}')");
                        $db->exec("update `content` set `rating`="
                            . "("
                            . "select (SUM(`vote`)/GREATEST(1,COUNT(`vote`))) "
                            . "from `votes` where "
                            . "`content_id`=" . intval($_POST['content'])
                            . ") "
                            . "where `id`=" . intval($_POST['content']));
                        $data['cnt'] = GetOne("select (COUNT(`vote`)) "
                            . "from `votes` where "
                            . "`content_id`=" . intval($_POST['content']));
                        $data['rating'] = GetOne("select "
                            . "(SUM(`vote`)/GREATEST(1,COUNT(`vote`))) "
                            . "from `votes` where "
                            . "`content_id`=" . intval($_POST['content']));
                        $data['success'] = true;
                    }
                    echo json_encode($data);
                    exit;
                    break;
                case "LoadComments":
                    $out = array();
                    $out['action'] = $_POST['Action'];
                    $out['content'] = intval($_POST['content']);
                    $out['comments'] = array();
                    $pagex = intval($_POST['page']) - 1;
                    if ($pagex < 0)
                        $pagex = 0;
                    $sql = "select * from `comments` where "
                        . "`content_id`={$out['content']} "
                        . "order by `time` desc "
                        . "limit " . ($pagex * $this->comments) . ", {$this->comments}";
                    $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = 0, $count = sizeof($row); $i < $count; $i++) {
                        $out['comments'][] = array(
                            "id" => $row[$i]['id'],
                            "name" => $this->getUser($row[$i]['user_id']),
                            "time" => date("d.m.Y H:i:s", $row[$i]['time']),
                            "text" => nl2br($row[$i]['text']),
                            "canDelete" => ($User->Permissions['root'] > 0 || $User->Permissions['admin'] > 0 ? true : false),
                            "ip" => ($User->Permissions['root'] > 0 || $User->Permissions['admin'] > 0 ? $row[$i]['ip'] : "")
                        );
                    }
                    $out['commentsCount'] = GetOne("select count(`id`) "
                        . "from `comments` where "
                        . "`content_id`={$out['content']}");
                    $out['pages'] = ceil($out['commentsCount'] / $this->comments);
                    echo json_encode($out);
                    exit;
                    break;
                case "AddComment":
                    $data = array("success" => false);
                    if (isset($_POST['text']) && isset($_POST['content']) && !$User->IsAnonymous && $User->IsActive && !$User->IsBlocked && mb_strlen(trim($_POST['text'])) > 0 && GetOne("select `id` from `content` where `publish`=1 and `id`=" . intval($_POST['content'])) > 0) {
                        $sql = "INSERT INTO `comments` 
					SET 
					`text` = " . $db->quote(trim($_POST['text'])) . ",
					`content_id` = '" . intval($_POST['content']) . "',
					`time` = UNIX_TIMESTAMP(),
					`user_id` = {$User->Id},
					`ip` = " . $db->quote($_SERVER['REMOTE_ADDR']) . "
			";
                        $db->exec($sql);
                        $data['success'] = true;
                    }
                    echo json_encode($data);
                    exit;
                    break;
                case "DeleteComment":
                    $data = array("success" => false);
                    if (isset($_POST['comment']) && ($User->Permissions['root'] > 0 || $User->Permissions['admin'] > 0) && $User->IsActive && !$User->IsBlocked && GetOne("select `id` from `comments` where `id`=" . intval($_POST['comment'])) > 0) {
                        $sql = "delete from `comments` where "
                            . "`id`=" . intval($_POST['comment']);
                        $db->exec($sql);
                        $data['success'] = true;
                    }
                    echo json_encode($data);
                    exit;
                    break;
            }
        }
        if (GetOne("select count(`id`) from `content` where "
            . "`publish`=1 and  "
            . "`url`=" . $db->quote(end($this->QueryElements))) > 0) {
            $this->Content = $db->query("select c.*, "
                . "l.`title` as 'contentTitle', "
                . "l.`preText` as 'contentPreText', "
                . "l.`totalText` as 'contentTotalText' from "
                . "`content` c  "
                . "inner join `content_language` l "
                . "on c.`id`=l.`content_id` and "
                . "l.`language_id`=" . $db->quote($_SESSION['language'])
                . " where c.`publish`=1 and  "
                . "c.`url`=" . $db->quote(end($this->QueryElements)))->
                fetch(PDO::FETCH_ASSOC);
            $this->LoadCategories(0);
            $urlArray = getFullCategoryUrl($this->Content['category_id']);
            $this->FullUrl = $this->RootUrl . implode("/", array_reverse($urlArray)) . "/" . $this->Content['url'];
        } else {
            $this->TemplateName = "404.php";
        }
    }

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

}

?>