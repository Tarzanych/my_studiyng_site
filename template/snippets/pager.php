<div class="pager">
    Pages: 
    <?
    for ($i = 1; $i <= $pager['pages_max']; $i++) {
        $link = $pager['link'];
        $get = $_GET;
        unset($get['q']);
        $get['page'] = $i;
        $link .= "?" . http_build_query($get);
        ?>
        <span <?= ($i == $pager['active'] ? 'class="active"' : "") ?>><a href="<?= $link ?>"><?= $i ?></a></span>
        <?
    }
    ?>
</div>