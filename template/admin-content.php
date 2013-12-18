<? include SNIPPETS_PATH."admin-menu.php"; ?>
<div class="pageTitle center">
	Content
</div>
<div class="profileBlock">
	<div class="row">
		<div>Select category</div>
		<div>
			
			<select class="catSelect">
				<option value="all" <?=(isset($this->QueryElements[2]) && $this->QueryElements[2] == "all" ? 'selected="selected"' : "" )?>>All categories</option>
				<option value="without" <?=(isset($this->QueryElements[2]) && $this->QueryElements[2] == "without" ? 'selected="selected"' : "" )?>>Without category</option>
				<option disabled="disabled">-----------</option>
				<?
				foreach ($this->CatArray as $cat) {
					$tab = "";
					for ($i=1; $i<=$cat['level']; $i++) {
						$tab.="&emsp;";
					}
				?>
				<option value="<?=$cat['id']?>"  <?=(isset($this->QueryElements[2]) && $this->QueryElements[2] == $cat['id'] ? 'selected="selected"' : "" )?>><?=$tab.$cat['title']?></option>
				<?
					
				}
				?>
			</select>
		</div>
	</div>
</div>
<?
	if (count($this->ContentArray['content']) < $this->ContentArray['count']) {
		$pager = array(	"link" => $this->RootUrl."admin/content",
				"pages_max" => ceil($this->ContentArray['count']/$this->perPage),
				"active" => $this->PageNum
		);
		include SNIPPETS_PATH."pager.php";
	}
?>
<table class="contentTable adminTable">
	<tr>
		<td>Id</td>
		<td>Title</td>
		<td>Url</td>
		<td>Category</td>
		<td>Published</td>
		<td>On front page</td>
		<td>Creator</td>
		<td>Creation time</td>
		<td></td>
	</tr>
<?
	foreach($this->ContentArray['content'] as $content) {
		$author = $this->getUser($content['author']);
?>
	<tr>
		<td class="center"><?=$content['id']?></td>
		<td>
			<?
			foreach ($content['title'] as $key => $title) {
			?>
			<a href="#" onclick="editContent(<?=$content['id']?>); return false;"><?=$title?></a> (<?=$key?>)
			<? } ?>
		</td>
		<td class="center"><?=$content['url']?></td>
		<td class="center"><?=$this->getCategory($content['category_id'])?></td>
		<td class="center"><?=$content['publish']==1 ? '<span class="green">yes</span>' : '<span class="red">no</span>'?></td>
		<td class="center"><?=$content['onFront']==1 ? '<span class="green">yes</span>' : '<span class="red">no</span>'?></td>
		<td class="center"><?=$author ? '<a href="'.$this->RootUrl.'profile/'.$content['author'].'">'.$author.'</a>' : "-" ?></td>
		<td class="center"><?=($content['createTime'] > 0 ? date("d.m.Y H:i",$content['createTime']) : "-")?></td>
		<td class="center">
			<? if ($User->Permissions['edit']==1 || $User->Permissions['root']==1 || $User->Permissions['admin']==1 || ($User->Permissions['editOwn']==1 && $content['createUser']==$User->Id)) { ?>
				<a href="#" onclick="editContent(<?=$content['id']?>); return false;"><img src="<?=$this->RootUrl?>template/img/edit.png" /></a>
			<? } ?>
			<? if ($User->Permissions['delete']==1 || $User->Permissions['root']==1 || $User->Permissions['admin']==1 || ($User->Permissions['deleteOwn']==1 && $content['createUser']==$User->Id)) { ?>
				<a href="#" onclick="if (confirm('Do you really want to delete content &quot;<?=$content['title']?>&quot;')) { deleteContent(<?=$content['id']?>); } return false;"><img src="<?=$this->RootUrl?>template/img/delete.png" /></a>
			<? } ?>
		</td>
	</tr>
<?
	}
?>
</table>
<?
	if (count($this->ContentArray['content']) < $this->ContentArray['count']) {
		$pager = array(	"link" => $this->RootUrl."admin/content",
				"pages_max" => ceil($this->ContentArray['count']/$this->perPage),
				"active" => $this->PageNum
			);
		include SNIPPETS_PATH."pager.php";
	}
?>
<div class="pageTitle">
	<a href="#" onclick="$('.createContentBlock').show().find('input:first').focus();
	$('.createContentForm input[name=\'language\']:first').click();
	$('.createContentBlock textarea').tinymce({
							script_url : '/template/js/tinymce/js/tinymce/tinymce.min.js',
							theme: 'modern',
							
							plugins: 'image link lists charmap textcolor table visualblocks past media contextmenu'
						});
	return false;">Create content</a>
</div>
<div class="createContentBlock profileBlock">
	<form action="" method="post" class="createContentForm">
	<div class="row">
		<div>Languages:</div>
		<div>
			<? foreach ($this->Languages as $lang) { ?>
			<label><input type="radio" name="language" value="<?=$lang['id']?>" /> <?=$lang['title']?></label>&emsp;
			<? } ?>
		</div>
	</div>
	<? foreach ($this->Languages as $lang) { ?>
	<div class="languageText" id="create_title<?=$lang['id']?>">
		<div class="row">
			<div>Title *:</div>
			<div><input type="text" name="contentTitle[<?=$lang['id']?>]" /></div>
		</div>
	</div>
	<? } ?>
	<div class="row">
		<div>URL *:</div>
		<div><input type="text" name="contentUrl" /></div>
	</div>
	<div class="row">
		<div>Category *:</div>
		<div>
			<select name="contentCategory">
				
				<option value="without" <?=((isset($this->QueryElements[2]) && $this->QueryElements[2] == "without") || !isset($this->QueryElements[2]) ? 'selected="selected"' : "" )?>>Without category</option>
				<option disabled="disabled">-----------</option>
				<?
				foreach ($this->CatArray as $cat) {
					$tab = "";
					for ($i=1; $i<=$cat['level']; $i++) {
						$tab.="&emsp;";
					}
				?>
				<option value="<?=$cat['id']?>"  <?=(isset($this->QueryElements[2]) && $this->QueryElements[2] == $cat['id'] ? 'selected="selected"' : "" )?>><?=$tab.$cat['title']?></option>
				<?
					
				}
				?>
			</select>
		</div>
	</div>
	
	<? foreach ($this->Languages as $lang) { ?>
	<div class="languageText" id="create_language<?=$lang['id']?>">
		<div class="row">
			<div>Pre-text:</div>
			<div>
				<textarea class="tinymce" name="contentPreText[<?=$lang['id']?>]" class="tinymce"></textarea>
			</div>
		</div>
		
		<div class="row">
			<div>Main text *:</div>
			<div>
				<textarea class="tinymce" name="contentMainText[<?=$lang['id']?>]" class="tinymce"></textarea>
			</div>
		</div>
	</div>
	<? } ?>
	<div class="row">
		<div>Publish:</div>
		<div>
			<label><input type="radio" name="contentPublish" value="1" checked="checked" /> yes</label>&emsp;
			<label><input type="radio" name="contentPublish" value="0" /> no</label>&emsp;
		</div>
	</div>
	<div class="row">
		<div>On front page:</div>
		<div>
			<label><input type="radio" name="contentFront" value="1" checked="checked" /> yes</label>&emsp;
			<label><input type="radio" name="contentFront" value="0" /> no</label>&emsp;
		</div>
	</div>
	<div class="row">
		<div></div>
		<div>
			<input type="hidden" name="Action" value="createContent" />
			<input type="submit" value="Create content" />
		</div>
	</div>
	</form>
</div>
<div class="editContentBlock profileBlock">
	<form action="" method="post" class="editContentForm">
	<div class="row">
		<div>Languages:</div>
		<div>
			<? foreach ($this->Languages as $lang) { ?>
			<label><input type="radio" name="language" value="<?=$lang['id']?>" /> <?=$lang['title']?></label>&emsp;
			<? } ?>
		</div>
	</div>
	<? foreach ($this->Languages as $lang) { ?>
	<div class="languageText" id="edit_title<?=$lang['id']?>">
		<div class="row">
			<div>Title *:</div>
			<div><input type="text" name="contentTitle[<?=$lang['id']?>]" /></div>
		</div>
	</div>
	<? } ?>
	<div class="row">
		<div>URL *:</div>
		<div><input type="text" name="contentUrl" /></div>
	</div>
	<div class="row">
		<div>New author:</div>
		<div><input type="text" name="contentAuthor" /></div>
	</div>
	<div class="row">
		<div>Category *:</div>
		<div>
			<select name="contentCategory">
				
				<option value="without">Without category</option>
				<option disabled="disabled">-----------</option>
				<?
				foreach ($this->CatArray as $cat) {
					$tab = "";
					for ($i=1; $i<=$cat['level']; $i++) {
						$tab.="&emsp;";
					}
				?>
				<option value="<?=$cat['id']?>"><?=$tab.$cat['title']?></option>
				<?
					
				}
				?>
			</select>
		</div>
	</div>
	
	<? foreach ($this->Languages as $lang) { ?>
	<div class="languageText" id="edit_language<?=$lang['id']?>">
		<div class="row">
			<div>Pre-text:</div>
			<div>
				<textarea class="tinymce" name="contentPreText[<?=$lang['id']?>]" class="tinymce"></textarea>
			</div>
		</div>
		<div class="row">
			<div>Main text *:</div>
			<div>
				<textarea class="tinymce" name="contentMainText[<?=$lang['id']?>]" class="tinymce"></textarea>
			</div>
		</div>
	</div>
	<? } ?>
	<div class="row">
		<div>Publish:</div>
		<div>
			<label><input type="radio" name="contentPublish" value="1" checked="checked" /> yes</label>&emsp;
			<label><input type="radio" name="contentPublish" value="0" /> no</label>&emsp;
		</div>
	</div>
	<div class="row">
		<div>On front page:</div>
		<div>
			<label><input type="radio" name="contentFront" value="1" checked="checked" /> yes</label>&emsp;
			<label><input type="radio" name="contentFront" value="0" /> no</label>&emsp;
		</div>
	</div>
	<div class="row">
		<div></div>
		<div>
			<input type="hidden" name="contentId" value="" />
			<input type="hidden" name="Action" value="updateContent" />
			<input type="submit" value="Update content" />
		</div>
	</div>
	</form>
</div>