<?php

include "config.php";
include "functions.php";

onBeforeLoad();
onLoad();

require_once $config['absolutePath'] . "template.php";
onAfterLoad();
?>