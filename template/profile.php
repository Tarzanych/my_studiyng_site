<?
if (isset($_SESSION['updateSuccess']) && $_SESSION['updateSuccess']) {
    ?>
    <div class="green bold padding-bottom">
        Successfully updated
    </div>
    <?
    unset($_SESSION['updateSuccess']);
}
?>
<?
if ($this->Error) {
    ?>
    <div class="center">
        <div><img src="<?= $this->RootUrl . "template/img/error.png" ?>" /></div>
        <div class="bold">
            <?= $this->ErrorMessage ?>
        </div>
    </div>
    <?
} else {
    ?>
    <div class="profileBlock">
        <div class="row">
            <div class="avatar">
                <img src="<?= $this->RootUrl ?>images/avatars/<?= $this->ProfileAvatar ?>"  alt="Avatar"  />
            </div>
            <div>
                <div class="padding-bottom">
                    <span class="bold"><?= $this->Profile['nickname'] ?></span> <?= ($this->MyProfile ? "(my profile)" : "") ?>
                    <?
                    if ($this->Profile['blocked'] == 1) {
                        ?>
                        <div class="small red"><?= t('blocked') ?></div>
                        <?
                    } elseif ($this->Profile['active'] == 0) {
                        ?>
                        <div class="small red"><?= t('not active') ?></div>
                        <?
                    }
                    ?>
                </div>
                <div class="padding-bottom">
                    <? if (mb_strlen($this->Profile['name']) > 0 || mb_strlen($this->Profile['surname'])) { ?>
                        <div>
                            <span class="bold"><?= t('[NAME]') ?>:</span> <?= $this->Profile['name'] ?> <?= $this->Profile['surname'] ?> 
                        </div>
                    <? } ?>
                    <? if (!$User->IsAnonymous && $User->IsActive) { ?>
                        <div>
                            <span class="bold">Email:</span> <?= $this->Profile['email'] ?>
                        </div>
                    <? } ?>
                    <div>
                        <span class="bold">Registration time:</span> <?= date("d.m.Y H:i", $this->Profile['regDate']) ?>
                    </div>
                    <? if (intval($this->Profile['lastActive']) > 0) { ?>
                        <div>
                            <span class="bold">Last login:</span> <?= date("d.m.Y H:i", $this->Profile['lastActive']) ?>
                        </div>
                    <? } ?>
                </div>
                <? if (($this->MyProfile || ((isset($User->Permissions['root']) && $User->Permissions['root'] == 1) || (isset($User->Permissions['admin']) && $User->Permissions['admin'] == 1 && $this->ProfilePermissions['root'] == 0))) && !$User->IsBlocked) { ?>
                    <div class="padding-bottom">
                        <a href="<?= $this->RootUrl ?>profile/editprofile/<?= $this->ProfileId ?>" class="bold">Edit profile</a>&emsp;
                        <a href="<?= $this->RootUrl ?>profile/delprofile/<?= $this->ProfileId ?>" class="bold red" onclick="if (confirm('Are you sure?')) {
                                    return true;
                                } else {
                                    return false;
                                }">Delete profile</a>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
    <?
}
?>