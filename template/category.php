<? if ((isset($User->Permissions) && !$User->IsAnonymous && ($User->Permissions['create'] > 0 || $User->Permissions['root'] > 0 || $User->Permissions['admin'] > 0 )) && isset($User->IsActive) && $User->IsActive && isset($User->IsBlocked) && !$User->IsBlocked) { ?>
    <span class="createLink"><a href="#" onclick="openCreateForm();
            return false;"><?= t('Create content') ?></a></span>
    <? } ?>
<div class="pageTitle center">
    <?= $this->Category['title'] ?>
</div>
<?
if (count($this->Subcategories) > 0) {
    ?>
    <div>
        <b>Subcategories:</b>
        <ul>
            <?
            foreach ($this->Subcategories as $sub) {
                ?>
                <li><a href="<?= $this->RootUrl . implode("/", array_reverse(getFullCategoryUrl($sub['id']))) ?>"><?= $sub['title'] ?></a></li>
                <?
            }
            ?>
        </ul>

    </div>
    <?
}
?>
<?
if (count($this->ContentArray['content']) < $this->ContentArray['count']) {
    $urlArray = getFullCategoryUrl($this->Category['id']);
    $pager = array("link" => $this->RootUrl . implode("/", array_reverse($urlArray)) . "/",
        "pages_max" => ceil($this->ContentArray['count'] / $this->perPage),
        "active" => $this->PageNum
    );
    include SNIPPETS_PATH . "pager.php";
}
foreach ($this->ContentArray['content'] as $cont) {
    $urlArray = getFullCategoryUrl($cont['category_id']);
    $fullUrl = $this->RootUrl . implode("/", array_reverse($urlArray)) . "/" . $cont['url'];
    include SNIPPETS_PATH . "content-block.php";
}
if (count($this->ContentArray['content']) < $this->ContentArray['count']) {
    include SNIPPETS_PATH . "pager.php";
}
?>
<? include SNIPPETS_PATH . "create-content.php"; ?>
<? include SNIPPETS_PATH . "edit-content.php"; ?>