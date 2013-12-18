<div class="center">
    <div><img src="<?= $this->RootUrl . "template/img/success.png" ?>" /></div>
    <div class="bold">
        <?
        if (!$this->AlreadyActivated) {
            ?>
            Thank you! Now your account is activated
            <?
        } else {
            ?>
            Your account is already activated. You can use your login and password
            <?
        }
        ?>
    </div>
</div>
