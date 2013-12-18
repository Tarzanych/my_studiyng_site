<?php

class FrontPage extends Page {

    public $TemplateName = "front-page.php";
    public $perPage = 10;
    public $PageNum = 0;
    public $ContentArray = array();
    public $CatArray = array();

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

    function OnBeforeDisplay() {
        global $db, $User;
        $this->LoadCategories(0);
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($page <= 0) {
            $page = 1;
        }
        $page_sql = ($page - 1) * $this->perPage;
        $this->PageNum = $page;

        $sql = $db->query("select  SQL_CALC_FOUND_ROWS c.*, "
            . "l.`title` as 'contentTitle', "
            . "l.`preText` as 'contentPreText', "
            . "l.`totalText` as 'contentTotalText' "
            . "from `content` c "
            . "inner join `content_language` l "
            . "on c.`id`=l.`content_id` and "
            . "l.`language_id`=" . $db->quote($_SESSION['language'])
            . " where c.`onFront`=1 and "
            . "c.`publish`=1 "
            . "order by c.`createTime` desc "
            . "limit " . $page_sql . ", " . $this->perPage);

        $foundRows = GetOne("SELECT FOUND_ROWS();");
        $content = $sql->fetchAll(PDO::FETCH_ASSOC);
        $this->ContentArray['count'] = $foundRows;
        $this->ContentArray['content'] = $content;
    }

    function OnAfterDisplay() {
        
    }

}

?>