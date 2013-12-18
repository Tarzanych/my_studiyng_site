<?
include ("../../config.php");
include ("../../functions.php");
$languages = $db->query("select * from `languages` where 1 order by `default` desc")->fetchAll(PDO::FETCH_ASSOC);
$langArr = array();
foreach ($languages as $lang) {
    $langArr[] = $lang['id'];
}
$languageConstants = $db->query("select * from `languageConstants` where 1")->fetchAll(PDO::FETCH_ASSOC);
?>
var language = <?= $_SESSION['language'] ?>;
var languageDefault = <?= GetOne("select `id` from `languages` where 1 order by `default` desc limit 1") ?>;
var languageConstants = [];
<?
foreach ($languages as $lang) {
    ?>
    languageConstants[<?= $lang['id'] ?>]=[];
    <?
}

foreach ($languageConstants as $lang) {
    ?>
    languageConstants[<?= $lang['language'] ?>]['<?= $lang['var'] ?>'] = <?= json_encode($lang['val']) ?>;
    <?
}
?>