<?php

class Category extends Page {

    public $TemplateName = "category.php";
    public $Category = array();
    public $CategoryIds = array();
    public $Subcategories = array();
    public $ContentArray = array();
    public $CatArray = array();
    public $perPage = 10;
    public $PageNum = 0;

    public function OnBeforeDisplay() {
        global $db, $User;

        if (GetOne("select count(`id`) from `category` where `publish`=1 and `url`=" . $db->quote(end($this->QueryElements))) > 0) {
            $this->Category = $db->query("select * from `category` where `publish`=1 and `url`=" . $db->quote(end($this->QueryElements)))->fetch(PDO::FETCH_ASSOC);
            $this->CategoryIds[] = $this->Category['id'];
            $this->LoadSubCategories($this->Category['id']);
            $this->LoadCategories(0);
            $sql = $db->query("select `id`, `title` from `category` where `parent`=" . $this->Category['id']);
            if ($sql->rowCount() > 0) {
                $subcategories = $sql->fetchAll(PDO::FETCH_ASSOC);
                foreach ($subcategories as $s) {
                    $this->Subcategories[] = $s;
                }
            }
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            if ($page <= 0) {
                $page = 1;
            }
            $page_sql = ($page - 1) * $this->perPage;
            $this->PageNum = $page;

            $sql = $db->query("select  SQL_CALC_FOUND_ROWS c.*, l.`title` as 'contentTitle', l.`preText` as 'contentPreText', l.`totalText` as 'contentTotalText' from `content` c inner join `content_language` l on c.`id`=l.`content_id` and l.`language_id`=" . $db->quote($_SESSION['language']) . " where c.`category_id` in (" . implode(",", $this->CategoryIds) . ") and c.`publish`=1 order by `createTime` desc limit " . $page_sql . ", " . $this->perPage);

            $foundRows = GetOne("SELECT FOUND_ROWS();");
            $content = $sql->fetchAll(PDO::FETCH_ASSOC);
            $this->ContentArray['count'] = $foundRows;
            $this->ContentArray['content'] = $content;
        } else {
            $this->TemplateName = "404.php";
        }
    }

    protected function LoadCategories($parent, $level = 0) {
        global $db;
        $categories = $db->query("select * from `category` where `parent` = '{$parent}' order by `title`");
        foreach ($categories as $cat) {
            $cat['level'] = $level;
            $this->CatArray[] = $cat;
            if (GetOne("select count(`id`) from `category` where `parent`={$cat['id']}")) {
                $this->LoadCategories($cat['id'], $level + 1);
            }
        }
    }

    protected function LoadSubCategories($parent, $level = 0) {
        global $db;
        $categories = $db->query("select * from `category` where `publish`=1 and `parent` = '{$parent}' order by `title`");
        foreach ($categories as $cat) {
            $cat['level'] = $level;
            $this->CategoryIds[] = $cat['id'];

            if (GetOne("select count(`id`) from `category` where `publish`=1 and `parent`={$cat['id']}")) {
                $this->LoadSubCategories($cat['id'], $level + 1);
            }
        }
    }

}

?>