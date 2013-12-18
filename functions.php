<?php

/**
 * @file
 * File consits most of classes and functions
 */

/**
 * Basic menu class
 */
class Menu {

    /**
     * Menu id
     * @var int Menu ID 
     */
    public $Id = 0;
    public $TopId = 0;
    public $Show = false;
    public $Title = '';
    public $Items = array();

    /**
     * Recursive HTML menu generating function
     * 
     * @param array $items
     * @return string 
     */
    public function generateMenu($items = false) {
        $html = "<ul>\n";
        if (!$items)
            $items = $this->Items;
        foreach ($items as $item) {
            $html .= "<li>\n";
            $html .= "\t<a href=\"{$item['link']}\">{$item['title']}</a>\n";
            if (isset($item['items'])) {
                $html .= $this->generateMenu($item['items']);
            }
            $html .= "</li>";
        }
        $html .= "</ul>";
        return $html;
    }

    /**
     * Function loads whole menu
     * @global mixed $db PDO object
     */
    protected function loadMenu() {
        global $db;
        $menu = $db->query("select m.* from `menu` m where "
                . "`id`=" . $this->Id)->fetch(PDO::FETCH_ASSOC);
        $this->Title = $menu['title'];
        $this->Show = ($menu['publish'] ? true : false);
        $this->Items = $this->loadItems();
    }

    /**
     * Recursive function that loads menu items
     * @global mixed $db PDO object
     * @global array $config Site config
     * @param int $parent Parent item id
     * @return array Menu items array
     */
    public function loadItems($parent = 0) {
        global $db, $config;
        $itemsArray = array();

        $sql = $db->query("select * from `menuItems` where "
            . "`menuId`=" . $this->Id . " and "
            . "`parent`={$parent} and "
            . "`publish`=1 "
            . "order by `order_by` asc");
        if ($sql->rowCount() > 0) {
            $items = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach ($items as $item) {
                /**
                 * Switching item type
                 * L - basic link
                 * S - gets category link
                 * C - gets content link
                 */
                switch ($item['linkType']) {
                    default:
                    case 'L':
                        $link = $item['link'];
                        break;
                    case 'S':
                        $urlArray = array_reverse(getFullCategoryUrl($item['link']));
                        $link = $config['rootUrl'] . implode("/", $urlArray);
                        break;
                    case 'C':
                        $urlArray = array_reverse(getFullCategoryUrl(GetOne("select `category_id` "
                                    . "from `content` "
                                    . "where `id`=" . $item['link'])));
                        $link = $config['rootUrl'] . implode("/", $urlArray)
                            . (count($urlArray) > 0 ? "/" : "" )
                            . GetOne("select `url` from `content` where "
                                . "`id`=" . $item['link']);
                        break;
                }
                $addArray = array(
                    "id" => $item['id'],
                    "title" => $item['title'],
                    "link" => $link
                );
                /**
                 * Checking children menu items
                 * If true - loading loadItems($parent)
                 */
                if (GetOne("select count(`id`) from `menuItems` where `publish`=1 and `parent`=" . $item['id'])) {

                    $addArray['items'] = $this->loadItems($item['id']);
                }
                $itemsArray[] = $addArray;
            }
        }
        return $itemsArray;
    }

    /**
     * Menu construct function
     * 
     * @global mixed $db PDO object
     * @param int $id
     */
    public function __construct($id) {
        global $db;

        $sql = $db->prepare("select * from `menu` where `title` = ? ");
        $sql->execute(array($id));

        if (is_numeric($id) && GetOne("select * from `menu` where `id`=" . intval($id))) {
            $this->Id = intval($id);
            $this->loadMenu();
        } elseif ($sql->rowCount() > 0) {
            $this->Id = $sql->fetchObject()->id;
            $this->loadMenu();
        }
    }

}

/**
 * User class
 */
class User {

    /**
     * @var boolean Anonymous if true
     */
    public $IsAnonymous = true;

    /**
     * @var boolean Active if true
     */
    public $IsActive = false;

    /**
     * @var boolean Blocked if true
     */
    public $IsBlocked = false;

    /**
     * User id
     * @var int User's Id 
     */
    public $Id = -1;

    /**
     * User's nick
     * @var string User's nick
     */
    public $Nickname = "";

    /**
     * Check root priveleges
     * @var int Root if true
     */
    public $IsRoot = 0;

    /**
     * User's data array
     * @var array Data array
     */
    public $Data = array();

    /**
     * User's id is getting from the session id
     * 
     * @var array 
     */
    public $Session = array();

    /**
     * User's permissions array
     * @var array Permissions array
     */
    public $Permissions = array();

    /**
     * Check user's permissions function
     * @param string $perm Permission machine name
     * @return boolean
     */
    public function CheckPermissions($perm) {
        $return = false;
        switch ($perm) {
            case 'create':
                if ((isset($this->Permissions) && !$this->IsAnonymous && ($this->Permissions['create'] > 0 || $this->Permissions['root'] > 0 || $this->Permissions['admin'] > 0 )) && isset($this->IsActive) && $this->IsActive && isset($this->IsBlocked) && !$this->IsBlocked)
                    $return = true;
                break;
            case 'publish':
                if ((isset($this->Permissions) && !$this->IsAnonymous && ($this->Permissions['publish'] > 0 || $this->Permissions['root'] > 0 || $this->Permissions['admin'] > 0 )) && isset($this->IsActive) && $this->IsActive && isset($this->IsBlocked) && !$this->IsBlocked)
                    $return = true;
                break;
            case 'edit':
                if ((isset($this->Permissions) && !$this->IsAnonymous && ($this->Permissions['edit'] > 0 || $this->Permissions['root'] > 0 || $this->Permissions['admin'] > 0 )) && isset($this->IsActive) && $this->IsActive && isset($this->IsBlocked) && !$this->IsBlocked)
                    $return = true;
                break;
            case 'delete':
                if ((isset($this->Permissions) && !$this->IsAnonymous && ($this->Permissions['delete'] > 0 || $this->Permissions['root'] > 0 || $this->Permissions['admin'] > 0 )) && isset($this->IsActive) && $this->IsActive && isset($this->IsBlocked) && !$this->IsBlocked)
                    $return = true;
                break;
            case 'publishOwn':
                if ((isset($this->Permissions) && !$this->IsAnonymous && ($this->Permissions['publishOwn'] > 0 || $this->Permissions['root'] > 0 || $this->Permissions['admin'] > 0 )) && isset($this->IsActive) && $this->IsActive && isset($this->IsBlocked) && !$this->IsBlocked)
                    $return = true;
                break;
            case 'editOwn':
                if ((isset($this->Permissions) && !$this->IsAnonymous && ($this->Permissions['editOwn'] > 0 || $this->Permissions['root'] > 0 || $this->Permissions['admin'] > 0 )) && isset($this->IsActive) && $this->IsActive && isset($this->IsBlocked) && !$this->IsBlocked)
                    $return = true;
                break;
            case 'deleteOwn':
                if ((isset($this->Permissions) && !$this->IsAnonymous && ($this->Permissions['deleteOwn'] > 0 || $this->Permissions['root'] > 0 || $this->Permissions['admin'] > 0 )) && isset($this->IsActive) && $this->IsActive && isset($this->IsBlocked) && !$this->IsBlocked)
                    $return = true;
                break;
        }
        return $return;
    }

    /**
     * User's type construct function
     * @global mixed $db
     */
    public function __construct() {
        global $db;
        $sessId = session_id();

        $sql = $db->prepare("select * from `sessions` where "
            . "`sess_id` = ? and "
            . "`lastActive` > UNIX_TIMESTAMP() - 3*24*3600 limit 1");
        $sql->execute(array($sessId));
        if ($sql->rowCount() > 0) {

            $this->Session = $sql->fetch(PDO::FETCH_ASSOC);
            /**
             * If user is deleted, he cannot login.
             * User row is keeped to protect from the errors
             */
            $user = $db->query("select * from `users` where "
                . "`deleted`=0 and "
                . "`id`=" . $this->Session['user_id']);
            if ($user->rowCount() > 0) {
                $this->Id = $this->Session['user_id'];
                $this->IsAnonymous = false;

                $this->Data = $user->fetch(PDO::FETCH_ASSOC);
                $this->IsBlocked = ($this->Data['blocked'] == 1 ? true : false);
                $this->Nickname = $this->Data['nickname'];
                $this->IsActive = ($this->Data['active'] > 0 ? true : false);
                $sql = $db->query("select * from `permissions` where "
                    . "`user_id` = {$this->Id}");
                $this->Permissions = ($sql->rowCount() > 0 ? $sql->fetch(PDO::FETCH_ASSOC) : array());

                $sql = $db->prepare("update `sessions` set "
                    . "`ip` = ? , "
                    . "`lastActive` = UNIX_TIMESTAMP() where "
                    . "`id` = ? ");
                $sql->execute(array($_SERVER['REMOTE_ADDR'], $this->Session['id']));
                $this->Session['ip'] = $_SERVER['REMOTE_ADDR'];
            }
        }
    }

}

/**
 * Used if only one variable is needed from database
 * 
 * @global mixed $db PDO object
 * @param string $query SQL string
 * @return mixed
 */
function GetOne($query) {
    global $db;
    $result = array("");
    if ($sql = $db->query($query)) {
        $result = $sql->fetch();
    }
    $result = $result[0];
    return $result;
}

/**
 * Before load events
 * @global Menu $MainMenu
 * @global User $User
 */
function onBeforeLoad() {
    global $MainMenu, $User;
    $MainMenu = new Menu(1);
    if (!isset($_SESSION['language']) || $_SESSION['language'] == 0) {
        $_SESSION['language'] = GetOne("select `id` from `languages` where 1 "
            . "order by `default` desc limit 1");
    }
    $User = new User;
}

/**
 * onLoad page events
 * Page is an abstract array
 * Function transforms query into the action
 * 
 * @global Page $Page
 * @global User $User
 * @global mixed $db
 */
function onLoad() {
    global $Page, $User, $db;
    if (!isset($_GET['q'])) {
        $query = '';
    } else {
        $query = $_GET['q'];
    }
    /**
     * Getting query elements and cleaning empty of them
     */
    $queryElements = explode("/", $query);
    foreach ($queryElements as $id => $q) {
        if ($q == "")
            unset($queryElements[$id]);
    }
    /**
     * Loading abstract class
     */
    require_once(MODULES_PATH . "Page.php");
    /**
     * Getting class names from url
     */
    if (!isset($queryElements[0])) {
        $queryElements[0] = "main";
        $className = "FrontPage";
    } else {
        switch (mb_strtolower($queryElements[0])) {
            case 'admin':
                $className = "Admin";
                break;
            case 'main':
                $className = "FrontPage";
                break;
            case 'category':
                $className = "Category";
                break;
            case 'content':
                $className = "Content";
                break;
            case 'login':
            case 'logout':
                $className = "Login";
                break;
            case 'register':
                $className = "Register";
                break;
            case 'profile':
                $className = "Profile";
                break;
        }
        if (!isset($className)) {

            $url = end($queryElements);
// 				echo $url;
            if (GetOne("select count(`id`) from `content` where "
                    . "`url`=" . $db->quote($url)) > 0) {
                $className = "Content";
            } elseif (GetOne("select count(`id`) from `category` where "
                    . "`url`=" . $db->quote($url)) > 0) {
                $className = "Category";
            } else {
                $className = "Page404";
            }
        }
    }
    /**
     * Eval code
     * file names are equal to module names
     */
    $eval = "require_once(MODULES_PATH.\"" . $className . ".php\");
		\$Page = new " . $className . ";";
    eval($eval);
    $Page->ReqUrl = mb_strtolower($queryElements[0]);
}

/**
 * After load functions
 */
function onAfterLoad() {
    
}

/**
 * Recursive function gets url chain
 * 
 * @global mixed $db PDO object
 * @param int $id Category ID
 * @return array Array of url
 */
function getFullCategoryUrl($id) {
    global $db;
    $urlArray = array();
    $sql = $db->query("select * from `category` where `id`={$id}");
    if ($sql->rowCount() > 0) {
        $category = $sql->fetch(PDO::FETCH_ASSOC);
        $urlArray[] = $category['url'];
        if ($category['parent'] > 0) {
            $array = getFullCategoryUrl($category['parent']);
            foreach ($array as $a) {
                $urlArray[] = $a;
            }
        }
    }

    return $urlArray;
}

/**
 * Translation function
 * If argument is wrapped in [ ] scopes, then getting translation
 * by machine name, else searching the argument by default language value
 * 
 * @global mixed $db PDO Object
 * @param string $argument Function argument
 * @param int $lang Language ID
 * @return string
 */
function t($argument, $lang = 0) {
    global $db;
    if ($lang == 0)
        $lang = intval($_SESSION['language']);
    if ($argument[0] == '[' && $argument[mb_strlen($argument) - 1] == ']') {
        $return = GetOne("select `val` from `languageConstants` where "
            . "`var`=" . $db->quote(mb_substr($argument, 1, mb_strlen($argument) - 2))
            . " and "
            . "`language`=" . intval($lang));
    } else {
        $var = GetOne("select `var` from `languageConstants` "
            . "where "
            . "`val`=" . $db->quote($argument) . " and "
            . "`language` = (select `id` from `languages` "
            . "where 1 order by `default` desc limit 1)");
        if (mb_strlen($var) > 0) {
            $return = GetOne("select `val` from `languageConstants` where "
                . "`var`=" . $db->quote($var) . " and `language`=" . $lang);
        }
    }
    if (!isset($return) || $return == "") {
        $return = $argument;
    }
    return $return;
}

/**
 * Function checks required values by every language
 * 
 * @param type $a
 * @return boolean
 */
function checkFilledArray($a) {
    $return = true;
    foreach ($a as $key => $val) {
        if (GetOne("select count(`id`) from `languages` where "
                . "`id`=" . $key) && trim($val) == "")
            $return = false;
    }
    return $return;
}

?>