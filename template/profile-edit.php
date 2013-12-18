<div class="column1">
    <?
    if ($this->OperationsErrors) {
        ?>
        <div class="errorsBlock">
            <div class="bold">Sorry, there are some errors:</div>
            <div>
                <?= $this->OperationsErrors ?>
            </div>
        </div>
        <?
    }
    ?>
    <form method="post" action="<?= $this->RootUrl ?>profile/editprofile/<?= $this->Profile['id'] ?>" 
          enctype="multipart/form-data">
        <div class="profileBlock">
            <div class="row">
                <div class="avatar">
                    <img src="<?= $this->RootUrl ?>images/avatars/<?= $this->ProfileAvatar ?>" alt="Avatar" />
                </div>
                <div>
                    <div class="padding-bottom">
                        <span class="bold"><?= $this->Profile['nickname'] ?></span> <?= ($this->MyProfile ? "(my profile)" : "") ?>
                        <?
                        if ($this->Profile['active'] == 0) {
                            ?>
                            <div class="small red">not active</div>
                            <?
                        }
                        ?>
                    </div>
                    <div class="padding-bottom">
                        <div class="bold">Change avatar:</div>
                        <div>
                            <input name="avatar" type="file" />
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div>First name:</div>
                <div><input type="text" name="firstname" value="<?= htmlspecialchars((isset($_POST['firstname']) ? $_POST['firstname'] : $this->Profile['name']), ENT_QUOTES) ?>" /></div>
            </div>
            <div class="row">
                <div>Second name:</div>
                <div><input type="text" name="secondname" value="<?= htmlspecialchars((isset($_POST['secondname']) ? $_POST['secondname'] : $this->Profile['surname']), ENT_QUOTES) ?>" /></div>
            </div>
            <div class="row padding-bottom">
                <div>Email:</div>
                <div><input type="text" name="email" value="<?= htmlspecialchars((isset($_POST['email']) ? $_POST['email'] : $this->Profile['email']), ENT_QUOTES) ?>" /></div>
            </div>
            <div class="row bold padding-bottom">
                Password will be changed only if the fields below are filled
            </div>
            <div class="row">
                <div>Password:</div>
                <div><input type="password" name="password" /></div>
            </div>
            <div class="row">
                <div>Password confirm:</div>
                <div><input type="password" name="passconfirm" /></div>
            </div>
            <div class="row padding-bottom">
                <div></div>
                <div>
                    <input type="hidden" name="Action" value="Update" />
                    <input type="submit" value="Update profile" />
                </div>
            </div>
        </div>
    </form>
</div>
<div class="column2">
    <?
    if (($User->Permissions['root'] || $User->Permissions['admin']) && !$this->Profile['deleted'] == 1 && !$this->Profile['blocked'] == 1) {
        ?>
        <div class="pageTitle center">
            Profile permissions
        </div>
        <div class="profileBlock" id="profile<?= $this->ProfileId ?>">
            <? if ($this->ProfilePermissions['root']) { ?>
                <div class="row">
                    <div>Root:</div>
                    <div><span class="green">Yes</span></div>
                </div>
            <? } else { ?>
                <div class="row">
                    <div>Rule:</div>
                    <div id="rule"><select></select></div>
                </div>
                <hr />
                <div class="row">
                    <div>Admin:</div>
                    <div id="admin" data-value="<?= $this->ProfilePermissions['admin'] ?>" <? if ($User->Permissions['root']) { ?> class="permission"<? } ?>><?= $this->ProfilePermissions['admin'] == 1 ? '<span class="green">Yes</span>' : '<span class="red">No</span>' ?></div>
                </div>
                <div class="row">
                    <div>Create publications:</div>
                    <div id="create" data-value="<?= $this->ProfilePermissions['create'] ?>" class="permission"><?= $this->ProfilePermissions['create'] == 1 ? '<span class="green">Yes</span>' : '<span class="red">No</span>' ?></div>
                </div>
                <div class="row">
                    <div>Edit own publications:</div>
                    <div id="editOwn" data-value="<?= $this->ProfilePermissions['editOwn'] ?>" class="permission"><?= $this->ProfilePermissions['editOwn'] == 1 ? '<span class="green">Yes</span>' : '<span class="red">No</span>' ?></div>
                </div>
                <div class="row">
                    <div>Edit all publications:</div>
                    <div id="edit" data-value="<?= $this->ProfilePermissions['edit'] ?>" class="permission"><?= $this->ProfilePermissions['edit'] == 1 ? '<span class="green">Yes</span>' : '<span class="red">No</span>' ?></div>
                </div>
                <div class="row">
                    <div>Delete own publications:</div>
                    <div id="deleteOwn" data-value="<?= $this->ProfilePermissions['deleteOwn'] ?>" class="permission"><?= $this->ProfilePermissions['deleteOwn'] == 1 ? '<span class="green">Yes</span>' : '<span class="red">No</span>' ?></div>
                </div>
                <div class="row">
                    <div>Delete all publications:</div>
                    <div id="delete" data-value="<?= $this->ProfilePermissions['delete'] ?>" class="permission"><?= $this->ProfilePermissions['delete'] == 1 ? '<span class="green">Yes</span>' : '<span class="red">No</span>' ?></div>
                </div>
                <div class="row">
                    <div>Publish own publications:</div>
                    <div id="publishOwn" data-value="<?= $this->ProfilePermissions['publishOwn'] ?>" class="permission"><?= $this->ProfilePermissions['publishOwn'] == 1 ? '<span class="green">Yes</span>' : '<span class="red">No</span>' ?></div>
                </div>
                <div class="row">
                    <div>Publish all publications:</div>
                    <div id="publish" data-value="<?= $this->ProfilePermissions['publish'] ?>" class="permission"><?= $this->ProfilePermissions['publish'] == 1 ? '<span class="green">Yes</span>' : '<span class="red">No</span>' ?></div>
                </div>
            <? } ?>
        </div>
        <?
    }
    ?>
</div>
<div class="clear"></div>