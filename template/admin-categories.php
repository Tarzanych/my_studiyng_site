<? include SNIPPETS_PATH."admin-menu.php"; ?>
<div class="pageTitle center">
	Categories
</div>
<div class="pageTitle">
	<a href="#" onclick="$('.createCatBlock').show().find('input:first').focus();
	$('.createCatBlock textarea').tinymce({
		script_url : '/template/js/tinymce/js/tinymce/tinymce.min.js',
		theme: 'modern',
		menubar: 'edit view format'
	});
	return false;">Create category</a>
</div>
<table class="catTable adminTable">
	<tr>
		<td>Id</td>
		<td>Title</td>
		<td>Url</td>
		<td>Description</td>
		<td>Published</td>
		<td>Creator</td>
		<td>Creation time</td>
		<td></td>
	</tr>
<?
	foreach ($this->CatArray as $cat) {
		$tab = "";
		$author = $this->getUser($cat['createUser']);
		for ($i=1; $i<=$cat['level']; $i++) {
			$tab.="&emsp;";
		}
?>
	<tr>
		<td class="center"><?=$cat['id']?></td>
		<td>
			<? if ($User->Permissions['edit']==1 || $User->Permissions['root']==1 || $User->Permissions['admin']==1 || ($User->Permissions['editOwn']==1 && $cat['createUser']==$User->Id)) { ?>
			<?=$tab?><a href="#" onclick="editCategory(<?=$cat['id']?>); return false;"><?=$cat['title']?></a>
			<? } else { ?>
			<?=$tab.$cat['title']?>
			<? } ?>
		</td>
		<td class="center"><?=$cat['url']?></td>
		<td><?=(mb_strlen($cat['description']) > 150 ? mb_substr($cat['description'],0,150)."..." : $cat['description'])?></td>
		<td class="center"><?=$cat['publish']==1 ? '<span class="green">yes</span>' : '<span class="red">no</span>'?></td>
		<td class="center"><?=$author ? '<a href="'.$this->RootUrl.'profile/'.$cat['createUser'].'">'.$author.'</a>' : "-" ?></td>
		<td class="center"><?=($cat['createTime'] > 0 ? date("d.m.Y H:i",$cat['createTime']) : "-")?></td>
		<td class="center">
			<? if ($User->Permissions['edit']==1 || $User->Permissions['root']==1 || $User->Permissions['admin']==1 || ($User->Permissions['editOwn']==1 && $cat['createUser']==$User->Id)) { ?>
				<a href="#" onclick="editCategory(<?=$cat['id']?>); return false;"><img src="<?=$this->RootUrl?>template/img/edit.png" /></a>
			<? } ?>
			<? if ($User->Permissions['delete']==1 || $User->Permissions['root']==1 || $User->Permissions['admin']==1 || ($User->Permissions['deleteOwn']==1 && $cat['createUser']==$User->Id)) { ?>
				<a href="#" onclick="if (confirm('Do you really want to delete category &quot;<?=$cat['title']?>&quot;')) { deleteCategory(<?=$cat['id']?>); } return false;"><img src="<?=$this->RootUrl?>template/img/delete.png" /></a>
			<? } ?>
		</td>
	</tr>
<?
	}
?>
</table>
<div class="pageTitle">
	<a href="#" onclick="$('.createCatBlock').show().find('input:first').focus();
	$('.createCatBlock textarea').tinymce({
							script_url : '/template/js/tinymce/js/tinymce/tinymce.min.js',
							theme: 'modern',
							menubar: 'edit view format'
						});
	return false;">Create category</a>
</div>
<div class="createCatBlock profileBlock">
	<form action="" method="post" class="createCatForm">
	<div class="row">
		<div>Title *:</div>
		<div><input type="text" name="catTitle" /></div>
	</div>
	<div class="row">
		<div>URL *:</div>
		<div><input type="text" name="catUrl" /></div>
	</div>
	<div class="row">
		<div>Parent:</div>
		<div>
			<select name="catParent">
				<option value="0" selected="selected">------</option>
				<? foreach ($this->CatArray as $cat) { 
					$tab = "";
					for ($i=1; $i<=$cat['level']; $i++) {
						$tab.="&emsp;";
					}
				?>
				<option value="<?=$cat['id']?>"><?=$tab.$cat['title']?></option>
					<? } ?>
			</select>
		</div>
	</div>
	<div class="row">
		<div>Description:</div>
		<div><textarea name="catDescription" class="tinymce"></textarea></div>
	</div>
	<div class="row">
		<div>Publish:</div>
		<div>
			<label><input type="radio" name="catPublish" value="1" checked="checked" /> yes</label>&emsp;
			<label><input type="radio" name="catPublish" value="0" /> no</label>&emsp;
		</div>
	</div>
	<div class="row">
		<div></div>
		<div>
			<input type="hidden" name="Action" value="createCategory" />
			<input type="submit" value="Create category" />
		</div>
	</div>
	</form>
</div>
<div class="editCatBlock profileBlock">
	<form action="" method="post" class="editCatForm">
	<div class="row">
		<div>Title *:</div>
		<div><input type="text" name="catTitle" /></div>
	</div>
	<div class="row">
		<div>URL *:</div>
		<div><input type="text" name="catUrl" /></div>
	</div>
	<div class="row">
		<div>New author:</div>
		<div><input type="text" name="catAuthor" /></div>
	</div>
	<div class="row">
		<div>Parent:</div>
		<div>
			<select name="catParent">
				<option value="0" selected="selected">------</option>
				<? foreach ($this->CatArray as $cat) { 
					$tab = "";
					for ($i=1; $i<=$cat['level']; $i++) {
						$tab.="&emsp;";
					}
				?>
				<option value="<?=$cat['id']?>"><?=$tab.$cat['title']?></option>
					<? } ?>
			</select>
		</div>
	</div>
	<div class="row">
		<div>Description:</div>
		<div><textarea name="catDescription" class="tinymce"></textarea></div>
	</div>
	<div class="row">
		<div>Publish:</div>
		<div>
			<label><input type="radio" name="catPublish" value="1" checked="checked" /> yes</label>&emsp;
			<label><input type="radio" name="catPublish" value="0" /> no</label>&emsp;
		</div>
	</div>
	<div class="row">
		<div></div>
		<div>
			<input type="hidden" name="catId" value="" />
			<input type="hidden" name="Action" value="updateCategory" />
			<input type="submit" value="Update category" />
		</div>
	</div>
	</form>
</div>