<?
	abstract class Page {
		public $RootUrl = '';
		public $TemplatesDir = '';
		public $TemplateName = "";
		public $ReqUrl = "";
		public $Module = "";
		public $Languages = array();
		public $QueryElements = array();
		public function OnGlobalBeforeDisplay () {
			global $config, $db, $User;
			ob_start();
			$this->RootUrl = $config['rootUrl'];
			$this->TemplatesDir = TEMPLATES_PATH;
			if (!isset($_GET['q'])) {
				$query='';
			} else {
				$query = $_GET['q'];
			}
			$this->QueryElements = explode("/",$query);
			$this->Languages = $db->query("select * from `languages` where 1 order by `default` desc")->fetchAll(PDO::FETCH_ASSOC);
			foreach ($this->QueryElements as $id=>$q) {
				if ($q=="") unset($this->QueryElements[$id]);
			}
			
			if (isset($User->Permissions) && isset($_REQUEST['Action']) && !$User->IsBlocked && $User->IsActive) {
				switch ($_REQUEST['Action']) {
					case "checkContent":
							$data = array("success" => false, "errors" => "", "publish" => false);
							if (isset($_POST['contentId']) && GetOne("select count(`id`) from `content` where `id` = ".intval($_POST['contentId'])) > 0) {
								$data['success'] = true;
 								$data['content'] = $db->query("select * from `content` where `id`='".intval($_POST['contentId'])."'")->fetch(PDO::FETCH_ASSOC);
 								if ($User->CheckPermissions('publish') || ($User->CheckPermissions('publishOwn') && $data['content']['author']==$User->Id)) {
									$data['publish'] = true;
 								}
 								unset($data['content']['title']);
 								unset($data['content']['preText']);
 								unset($data['content']['totalText']);
 								$data['content']['preText']=array();
 								$data['content']['totalText']=array();
 								$data['content']['title']=array();
 								$sql = $db->query("select * from `content_language` where `content_id`='{$data['content']['id']}'")->fetchAll(PDO::FETCH_ASSOC);
 								foreach ($sql as $lang) {
									$data['content']['preText'][$lang['language_id']] = $lang['preText'];
									$data['content']['totalText'][$lang['language_id']] = $lang['totalText'];
									$data['content']['title'][$lang['language_id']] = $lang['title'];
 								}
 								
							}
							echo json_encode($data);
// 							if (isset($this->Forbidden)) $this->Forbidden=false;
							exit;
					break;
					case "checkCreateContentForm":
						$data = array("success" => false, "errors" => "");
						if (((isset($User->Permissions['create']) && $User->Permissions['create']==1) || (isset($User->Permissions['root']) && $User->Permissions['root']==1) || (isset($User->Permissions['admin']) && $User->Permissions['admin']==1))) {
							$category = (isset($_POST['category']) ? intval($_POST['category']) : 0 );
							$title = (isset($_POST['title']) ? $_POST['title'] : array() );
							$url = (isset($_POST['url']) ? trim($_POST['url']) : "" );
							$mainText = (isset($_POST['mainText']) ? $_POST['mainText'] : array() );
 							$cCheck = GetOne("select count(`id`) from `category` where `id`='{$category}'");
							$tCount = @GetOne("select count(`id`) from `content_language` where `title`=".$db->quote($_POST['title']));
							$uCount = @GetOne("select count(`id`) from `content` where `url`=".$db->quote($_POST['url']));
							if ($tCount > 0) $data['errors'] .= "Another content with such title exists<br />";
							if ($cCheck == 0 && $category!=0) $data['errors'] .= "Category id error<br />";
							if ($uCount > 0) $data['errors'] .= "Another content with such URL exists<br />";
							if (count($title) == 0 || !checkFilledArray($title)) $data['errors'] .= "Please enter content title<br />";
							if (mb_strlen(trim($url)) == 0) $data['errors'] .= "Please enter content URL<br />";
							if (count($mainText) == 0 || !checkFilledArray($mainText)) $data['errors'] .= "Please enter main text".print_r($mainText,true)."<br />";
							if (mb_strlen(trim($data['errors'])) == 0) {
								$data['success'] = true;
								if (isset($this->Forbidden)) $this->Forbidden=false;
							}
						}
						echo json_encode($data);
						exit;
					break;
					case "checkEditContentForm":
						$data = array("success" => false, "errors" => "");
						$id = (isset($_POST['contentId']) ? intval($_POST['contentId']) : 0 );
						if (((isset($User->Permissions['edit']) && $User->Permissions['edit']==1) || (isset($User->Permissions['editOwn']) && $User->Permissions['editOwn']==1 && GetOne("select count(`id`) from `content` where `id`='{$id}' and `author`={$User->Id}")>0) || (isset($User->Permissions['root']) && $User->Permissions['root']==1) || (isset($User->Permissions['admin']) && $User->Permissions['admin']==1))) {	
							$category = (isset($_POST['category']) ? intval($_POST['category']) : 0 );
							$title = (isset($_POST['title']) ? $_POST['title'] : array() );
							$url = (isset($_POST['url']) ? trim($_POST['url']) : "" );
							$mainText = (isset($_POST['mainText']) ? $_POST['mainText'] : array() );
							$newAuthor = (isset($_POST['newAuthor']) ? trim($_POST['newAuthor']) : "" );
							if (mb_strlen($newAuthor) > 0) {
								if (GetOne("select count(`id`) from `users` where `nickname` = ".$db->quote($newAuthor)) == 0) {
									$data['errors'] .= "User <b>{$newAuthor}</b> not found<br />";
								}
							}
 							$cCheck = GetOne("select count(`id`) from `category` where `id`='{$category}'");
							$tCount = @GetOne("select count(`id`) from `content` where `id`<>'{$id}' and `title`=".$db->quote($_POST['title']));
							$uCount = @GetOne("select count(`id`) from `content` where `id`<>'{$id}' and `url`=".$db->quote($_POST['url']));
							if ($id == 0 || GetOne("select count(`id`) from `content` where `id`='{$id}'")==0) $data['errors'] .= "Content id error<br />";
							if ($tCount > 0) $data['errors'] .= "Another content with such title exists<br />";
							if ($cCheck == 0 && $category!=0) $data['errors'] .= "Category id error<br />";
							if ($uCount > 0) $data['errors'] .= "Another content with such URL exists<br />";
							if (count($title) == 0 || !checkFilledArray($title)) $data['errors'] .= "Please enter content title<br />";
							if (mb_strlen(trim($url)) == 0) $data['errors'] .= "Please enter content URL<br />";
							if (count($mainText) == 0 || !checkFilledArray($mainText)) $data['errors'] .= "Please enter main text<br />";
							if (mb_strlen(trim($data['errors'])) == 0) {
								$data['success'] = true;
								if (isset($this->Forbidden)) $this->Forbidden=false;
							}
						}
						echo json_encode($data);
						exit;
					break;
					case 'createContent':
						if (((isset($User->Permissions['create']) && $User->Permissions['create']==1) || (isset($User->Permissions['root']) && $User->Permissions['root']==1) || (isset($User->Permissions['admin']) && $User->Permissions['admin']==1))) {
							$title = isset($_POST['contentTitle']) ? $_POST['contentTitle'] : array();
							$url = isset($_POST['contentUrl']) ? trim($_POST['contentUrl']) : "";
							$category = isset($_POST['contentCategory']) ? intval($_POST['contentCategory']) : 0;
							$preText = isset($_POST['contentPreText']) ? $_POST['contentPreText'] : array();
							$mainText = isset($_POST['contentMainText']) ? $_POST['contentMainText'] : array();
							$publish = isset($_POST['contentPublish']) ? intval($_POST['contentPublish']) : 0;
							$onFront = isset($_POST['contentFront']) ? intval($_POST['contentFront']) : 0;
							$cCheck = GetOne("select count(`id`) from `category` where `id`='{$category}'");
							$tCount = 0;
							foreach ($title as $t) {
									$tCount += GetOne("select count(`id`) from `content_language` where `content_id`<>'{$id}' and `title`=".($db->quote($t)));
							}
							$uCount = GetOne("select count(`id`) from `content` where `url`=".$db->quote($url));
							if (count($title) > 0 && checkFilledArray($title) && mb_strlen($url) > 0 && count($mainText) > 0 && checkFilledArray($mainText) &&  ($cCheck > 0 || $category == 0) && $tCount == 0 && $uCount == 0) {
								$sql = $db->prepare("insert into `content` (`url`, `category_id`, `createTime`, `publishTime`, `author`, `publish`, `onFront`) values ( :url, :category, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :user, :publish, :onfront)");
								$sql->execute(array(
									
									":url" => $url,
									":category" => $category,
									":user" => $User->Id,
									":publish" => $publish,
									":onfront" => $onFront
								));
								$content_id=GetOne("select LAST_INSERT_ID();");
								foreach ($preText as $id => $text) {
									$db->exec("insert into `content_language` (`title`,`content_id`, `language_id`, `preText`, `totalText`) values (".$db->quote($title[$id]).",{$content_id},{$id},".$db->quote($preText[$id]).",".$db->quote($mainText[$id]).")");
								}
								$_SESSION['CreateSuccess'] = true;
								$get=$_GET;
								unset($get['q']);
								$goto=$this->RootUrl.implode("/",$this->QueryElements).(count($get)>0 ? "?".http_build_query($get) : "" );
								$this->GoToUrl($goto);
							}
						}
					break;
					case 'updateContent':
						$id = isset($_POST['contentId']) ? intval($_POST['contentId']) : 0;
						if (((isset($User->Permissions['edit']) && $User->Permissions['edit']==1) || (isset($User->Permissions['editOwn']) && $User->Permissions['editOwn']==1 && GetOne("select count(`id`) from `content` where `id`='{$id}' and `author`={$User->Id}")>0) || (isset($User->Permissions['root']) && $User->Permissions['root']==1) || (isset($User->Permissions['admin']) && $User->Permissions['admin']==1))) {	
// 							echo 1;
							if (GetOne("select count(`id`) from `content` where `id`='{$id}'") > 0) {
// 								echo 2;
								$title = isset($_POST['contentTitle']) ? $_POST['contentTitle'] : array();
								$url = isset($_POST['contentUrl']) ? trim($_POST['contentUrl']) : "";
								$category = isset($_POST['contentCategory']) ? intval($_POST['contentCategory']) : 0;
								$preText = isset($_POST['contentPreText']) && is_array($_POST['contentPreText']) ? $_POST['contentPreText'] : array();
								$mainText = isset($_POST['contentMainText']) && is_array($_POST['contentMainText']) ? $_POST['contentMainText'] : array();
								$publish = isset($_POST['contentPublish']) ? intval($_POST['contentPublish']) : 0;
								$onFront = isset($_POST['contentFront']) ? intval($_POST['contentFront']) : 0;
								$author = isset($_POST['contentAuthor']) ? trim($_POST['contentAuthor']) : "";
								$authorId = GetOne("select `author` from `content` where `id`='{$id}'");
								if (mb_strlen($author) > 0) {
									if (GetOne("select count(`id`) from `users` where `nickname` = ".$db->quote($author)) > 0) {
										$authorId = GetOne("select `id` from `users` where `nickname`=".$db->quote($author));
									}
								}
								$cCheck = GetOne("select count(`id`) from `category` where `id`='{$category}'");
								$tCount = 0;
								foreach ($title as $t) {
									$tCount += GetOne("select count(`id`) from `content_language` where `content_id`<>'{$id}' and `title`=".($db->quote($t)));
								}
								$uCount = GetOne("select count(`id`) from `content` where `id`<>'{$id}' and `url`=".$db->quote($url));
								if (count($title) > 0 && checkFilledArray($title) && mb_strlen($url) > 0 && count($mainText) > 0 && checkFilledArray($mainText) &&  ($cCheck > 0 || $category == 0) && $tCount == 0 && $uCount == 0) {
// 									echo 3;
									$sql = $db->prepare("update `content` set `url` = :url, `category_id` = :category, `author` = :user, `publish` = :publish, onFront = :onfront where `id` = :id");
									$sql->execute(array(
										":id" => $id,
								
										":url" => $url,
										":category" => $category,

										":user" => $authorId,
										":publish" => $publish,
										":onfront" => $onFront
									));
									foreach ($preText as $key=>$val) {
										$db->exec("update `content_language` set `preText`=".$db->quote($preText[$key])." , `title`=".$db->quote($title[$key]).", `totalText`=".$db->quote($mainText[$key])." where `language_id`=".intval($key)." and `content_id`='{$id}'");
// 									

									}
									$_SESSION['UpdateSuccess'] = true;
									$get=$_GET;
									unset($get['q']);
									$goto=$this->RootUrl.implode("/",$this->QueryElements).(count($get)>0 ? "?".http_build_query($get) : "" );
 									$this->GoToUrl($goto);
								}
							}
						}
					break;
					case 'deleteContent':
						$id = isset($_REQUEST['contentId']) ? intval($_REQUEST['contentId']) : 0;
						$data = array("success" => false, "errors" => "", "url"=>"");
						if ($User->CheckPermissions('delete') || ($User->CheckPermissions('deleteOwn') && GetOne("select count(`id`) from `content` where `id`='{$id}' and `author`={$User->Id}")>0)) {	
							
							if (GetOne("select count(`id`) from `content` where `id`='{$id}'") > 0) {
								$content = $db->query("select * from `content` where `id`='{$id}'")->fetch(PDO::FETCH_ASSOC);
								$urlArray = getFullCategoryUrl($content['category_id']);
								$data['url'] = $this->RootUrl.implode("/",array_reverse($urlArray));
								$db->exec("delete from `content` where `id`='{$content['id']}'");
								$data['errors']="delete from `content` where `id`='{$content['id']}'";
								$data['success'] = true;
							} else {
								$data['errors'] = "No such content";
							}
						} else {
							$data['errors']="No permissions";
						}
						echo json_encode($data);
						exit;
						
					break;
				}
			}
			if (isset($_REQUEST['Action'])) {
				switch ($_REQUEST['Action']) {
						case "ChangeLanguage":
							$data = array("success" => false);
							$language = isset($_POST['language']) ? intval($_POST['language']) : 0;
							if ($language > 0 && GetOne("select count(`id`) from `languages` where `id`={$language}")>0) {
								$language = $db->query("select * from `languages` where `id`={$language}")->fetch(PDO::FETCH_ASSOC);
								$_SESSION['language'] = $language['id'];
								if (!$User->IsAnonymous) {
									$db->exec("update `users` set `language`={$language['id']} where `id`={$User->Id}");
									
								}
								$data['success']=true;
							}
							echo json_encode($data);
							exit;
						break;
				}
			}
		}
		public function OnBeforeDisplay() {
			
		}
		public function OnDisplay() {
			global $config, $User;
			if (file_exists($config['absolutePath'].$this->TemplateName)) {
				$this->TemplateName = "404.php";
			}
			require_once (TEMPLATES_PATH.$this->TemplateName);
		}
		public function OnAfterDisplay() {
		
		}
		public function OnGlobalAfterDisplay () {
		
		}
		protected function GoToUrl($url) {
			while (ob_get_status()) {
				ob_end_clean();
			}
			header( "Location: ".$url );
		}
		public function genRandomString($length = 10) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$string = '';
			for ($p = 0; $p < $length; $p++) {
				$string .= $characters[mt_rand(0, strlen($characters)-1)];
			}
			
			return $string;
		}
		public function validateNick($nick) {
			$rexSafety = "/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/i";
			if (preg_match($rexSafety, $nick)) {
				return false;
			} else {
				return true;
			}
		}
		public function validateName($name) {
			$regEx = "/^[a-zA-Zа-яА-Яіїє.-]*$/u";
			if (preg_match($regEx, $name)) {
				return true;
			} else {
				return false;
			}
		}
		public function validatePasswords($pass1,$pass2) {
			$return = false;
			if ($pass1 == $pass2 && mb_strlen($pass1) >= 6 && mb_strlen($pass1) <= 15 && mb_strlen($pass2) >= 6 && mb_strlen($pass2) <= 15) {
				$return = true;
			}
			return $return;
		}
		public function getUser($id) {
			return GetOne("select `nickname` from `users` where `id`={$id} and `deleted`=0");
		}
		public function getCategory($id) {
			return GetOne("select `title` from `category` where `id`={$id}");
		}
		
	}
?>