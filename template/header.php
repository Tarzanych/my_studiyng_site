<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title><?= $config['siteTitle'] ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= $config['charset'] ?>" />
        <script type="text/javascript" src="<?= $config['rootUrl'] ?>template/js/languages.php"></script>
        <script type="text/javascript" src="<?= $config['rootUrl'] ?>template/js/jquery.js"></script>
        <script type="text/javascript" src="<?= $config['rootUrl'] ?>template/js/jquery.alerts.js"></script>
        <script type="text/javascript" src="<?= $config['rootUrl'] ?>template/js/jquery.validate.js"></script>
        <script type="text/javascript" src="<?= $config['rootUrl'] ?>template/js/additional-methods.min.js"></script>
        <script type="text/javascript" src="<?= $config['rootUrl'] ?>template/js/jquery-ui.js"></script>
        <script type="text/javascript" src="<?= $config['rootUrl'] ?>template/js/main.js"></script>
        <script type="text/javascript" src="<?= $config['rootUrl'] ?>template/js/tinymce/js/tinymce/jquery.tinymce.min.js"></script>
        <script type="text/javascript">
            var searchText = '<?= t('Search') ?>...';
        </script>
        <link rel="stylesheet" type="text/css" href="<?= $config['rootUrl'] ?>template/css/main.css" />
        <link rel="stylesheet" type="text/css" href="<?= $config['rootUrl'] ?>template/css/jquery.alerts.css" />
        <link rel="stylesheet" type="text/css" href="<?= $config['rootUrl'] ?>template/css/jquery-ui.css" />
        <script type="text/javascript">
            var isRoot = <?= isset($User->Permissions['root']) && $User->Permissions['root'] == 1 ? "true" : "false" ?>;
        </script>
    </head>
    <body>
        <div class="mainDiv">
            <div class="header">
                <div class="logo">
                    <a href="<?= $config['rootUrl'] ?>"><img src="<?= $config['rootUrl'] ?>template/img/logo.png" alt="Drupal noob community" /></a>
                </div>
                <div class="flags">
                    <? foreach ($Page->Languages as $lang) { ?>
                        <span><a href="#" onclick="changeLanguage(<?= $lang['id'] ?>);
                                return false;"><img src="<?= $Page->RootUrl ?>template/img/flags/<?= $lang['flag'] ?>" alt="<?= $lang['title'] ?>" /></a></span>
                        <? } ?>
                </div>
                <div class="searchBlock">
                    <form action="/search" method="get">
                        <div class="searchDiv">
                            <input type="text" name="find" value="<?= t('Search') ?>..." />
                        </div>
                        <div><button type="submit"><img src="<?= $config['rootUrl'] ?>template/img/search.png" alt="Search" /></button></div>
                    </form>
                </div>
                <? include SNIPPETS_PATH . "account.php"; ?>
            </div>
            <? include SNIPPETS_PATH . "menu.php"; ?>
            <div class="content">
