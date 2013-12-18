<? if ($User->CheckPermissions('create') || $User->CheckPermissions('admin') || $User->CheckPermissions('root')) { ?>
<div class="createContentBlock">
	<form action="" method="post" class="createContentForm profileBlock">
	
	<div class="row">
		<div>Languages:</div>
		<div>
			<? foreach ($this->Languages as $lang) { ?>
			<label><input type="radio" name="language" value="<?=$lang['id']?>" /> <?=$lang['title']?></label>&emsp;
			<? } ?>
		</div>
	</div>
	<? foreach ($this->Languages as $lang) { ?>
	<div class="languageText" id="create_language<?=$lang['id']?>">
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
				<option value="<?=$cat['id']?>" <? if (isset($this->Category) && $this->Category['id']==$cat['id']) {?> selected="selected" <?}  ?>><?=$tab.$cat['title']?></option>
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
	<? if ($User->CheckPermissions('publish') || $User->CheckPermissions('publishOwn')) { ?>
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
	<? } else { ?>
	<input type="hidden" name="contentPublish" value="0" />
	<input type="hidden" name="contentFront" value="0" />
	<? } ?>
	<div class="row">
		<div></div>
		<div>
			<input type="hidden" name="Action" value="createContent" />
			<input type="submit" value="Create content" />
		</div>
	</div>
	</form>
</div>

<? } ?>
