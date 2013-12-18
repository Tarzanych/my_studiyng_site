<div class="contentBlock">
    <div class="contentTitle">
        <a href="<?= $fullUrl ?>"><?= $cont['contentTitle'] ?></a>
        <? if ($User->CheckPermissions('edit') || ($User->CheckPermissions('editOwn') && $cont['author'] == $User->Id)) { ?><a href="#" onclick="editContent(<?= $cont['id'] ?>);
                    return false;"><img src="<?= $this->RootUrl ?>template/img/edit.png" alt="Edit" title="Edit" /></a><? } ?>
        <? if ($User->CheckPermissions('delete') || ($User->CheckPermissions('deleteOwn') && $cont['author'] == $User->Id)) { ?><a href="#" onclick="if (confirm('Are you sure?')) {
                        deleteContent(<?= $cont['id'] ?>);
                    }
                    return false;"><img src="<?= $this->RootUrl ?>template/img/delete.png" alt="Edit" title="Edit" /></a><? } ?>
    </div>
    <div class="contentText">
        <?= ($cont['contentPreText'] ? $cont['contentPreText'] : (mb_strlen($cont['contentTotalText']) > 150 ? substr($cont['contentTotalText'], 0, 150) . "..." : $cont['contentTotalText'])) ?>
    </div>
    <div class="contentBottom">
        <div class="user"><a href="<?= $this->RootUrl ?>profile/<?= $cont['author'] ?>"><?= $this->getUser($cont['author']) ?></a>, <?= date("d.m.Y H:i", $cont['createTime']) ?></div>
        <div class="more"><a href="<?= $fullUrl ?>"><?= t('Read more...') ?></a></div>

        <div class="clear"></div>
    </div>
</div>