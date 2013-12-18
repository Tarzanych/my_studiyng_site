<? if ((isset($User->Permissions) && !$User->IsAnonymous && ($User->Permissions['create'] > 0 || $User->Permissions['root'] > 0 || $User->Permissions['admin'] > 0 )) && isset($User->IsActive) && $User->IsActive && isset($User->IsBlocked) && !$User->IsBlocked) { ?>
    <span class="createLink"><a href="#" onclick="openCreateForm();
            return false;"><?= t('Create content') ?></a></span>
    <? }
    ?>
<div class="center pageTitle">
    Hi, noobs! Welcome to our site
</div>
<div class="frontContent">
    <?
    if (count($this->ContentArray['content']) < $this->ContentArray['count']) {
        $pager = array("link" => $this->RootUrl,
            "pages_max" => ceil($this->ContentArray['count'] / $this->perPage),
            "active" => $this->PageNum,
        );
        include SNIPPETS_PATH . "pager.php";
    }
    foreach ($this->ContentArray['content'] as $cont) {
        $urlArray = getFullCategoryUrl($cont['category_id']);
        $fullUrl = $this->RootUrl . (($implode = implode("/", array_reverse($urlArray))) ? $implode . "/" : "") . $cont['url'];
        include SNIPPETS_PATH . "content-block.php";
    }
    if (count($this->ContentArray['content']) < $this->ContentArray['count']) {
        $pager = array("link" => $this->RootUrl,
            "pages_max" => ceil($this->ContentArray['count'] / $this->perPage),
            "active" => $this->PageNum,
        );
        include SNIPPETS_PATH . "pager.php";
    }
    ?>
</div>
<? include SNIPPETS_PATH . "create-content.php"; ?>
<? include SNIPPETS_PATH . "edit-content.php"; ?>