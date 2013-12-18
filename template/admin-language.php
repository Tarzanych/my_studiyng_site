<? include SNIPPETS_PATH."admin-menu.php"; ?>
<div class="pageTitle center">
	Language variables
</div>
<div class="pageTitle">
	<a href="#" onclick="createLangConstant(); return false;">Create constant</a>
</div>

<table class="languageTable adminTable">
	<tr>
		<td>Language var</td>
		<? foreach ($this->Languages as $lang) { ?>
		<td><?=$lang['title']?></td>
		<? } ?>
		<td></td>
	</tr>
	<? foreach ($this->LangConstants as $key => $langVal) { ?>
	<tr>
		<td><?=$key?></td>
		<? foreach ($this->Languages as $lang) { ?>
		<td><?=t('['.$key.']', $lang['id'])?></td>
		<? } ?>
		<td>
			<a href="#" onclick="editLanguageVar('<?=$key?>'); return false;"><img src="<?=$this->RootUrl?>template/img/edit.png" /></a>
			<a href="#" onclick="if (confirm('Do you really want to delete variable &quot;<?=$key?>&quot;')) { deleteLanguageVar('<?=$key?>'); } return false;"><img src="<?=$this->RootUrl?>template/img/delete.png" /></a>
		</td>
	</tr>
	<? } ?>
</table>
<div class="createLangConstantBlock profileBlock">
	
	<form action="" method="post" class="createLangConstantForm">
	<div class="row">
		<div><?=t("[VARIABLE]")?>:</div>
		<div><input type="text" name="constantTitle" /></div>
	</div>
	<? foreach ($this->Languages as $lang) { ?>
	<div class="row">
		<div><?=$lang['title']?>:</div>
		<div><input type="text" name="constantVal[<?=$lang['id']?>]" /></div>
	</div>
	<? } ?>
	<div class="row">
		<div></div>
		<div>
			<input type="hidden" name="Action" value="createConstant" />
			<input type="submit" value="Create constant" />
		</div>
	</div>
	</form>
</div>
<div class="editLangConstantBlock profileBlock">
	
	<form action="" method="post" class="editLangConstantForm">
	<div class="row">
		<div><?=t("[VARIABLE]")?>:</div>
		<div><input type="text" name="constantTitle" /></div>
	</div>
	<? foreach ($this->Languages as $lang) { ?>
	<div class="row">
		<div><?=$lang['title']?>:</div>
		<div><input type="text" name="constantVal[<?=$lang['id']?>]" /></div>
	</div>
	<? } ?>
	<div class="row">
		<div></div>
		<div>
			<input type="hidden" name="langVal" value="" />
			<input type="hidden" name="Action" value="updateConstant" />
			<input type="submit" value="Update constant" />
		</div>
	</div>
	</form>
</div>