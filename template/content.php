<script type="text/javascript">
	var content_id = <?=$this->Content['id']?>;
	var rating = <?=round($this->Content['rating'])?>;
	var canVote = <?=(!isset($User->Id) || $User->IsAnonymous || !$User->IsActive || $User->IsBlocked || $User->Id==$this->Content['author'] || GetOne("select count(`vote`) from `votes` where `content_id`={$this->Content['id']} and `user_id`={$User->Id})") > 0 ? "false" : "true")?>;
	var yourVote=<?=intval(GetOne("select `vote` from `votes` where `content_id`={$this->Content['id']} and `user_id`={$User->Id}"))?>;
</script>
<script type="text/javascript" src="<?=$this->RootUrl?>template/js/content.js"></script>
<div class="contentBlock">
	<div class="contentTitle">
		<a href="<?=$this->FullUrl?>"><?=$this->Content['contentTitle']?></a>
		<? if ($User->CheckPermissions('edit') || ($User->CheckPermissions('editOwn') && $this->Content['author']==$User->Id)) { ?><a href="#" onclick="editContent(<?=$this->Content['id']?>); return false;"><img src="<?=$this->RootUrl?>template/img/edit.png" alt="Edit" title="Edit" /></a><? } ?>
		<? if ($User->CheckPermissions('delete') || ($User->CheckPermissions('deleteOwn') && $this->Content['author']==$User->Id)) { ?><a href="#" onclick="if (confirm('Are you sure?')) { deleteContent(<?=$this->Content['id']?>); } return false;"><img src="<?=$this->RootUrl?>template/img/delete.png" alt="Edit" title="Edit" /></a><? } ?>
	</div>
	<div class="contentText">
		<?=$this->Content['contentTotalText']?>
	</div>
	<div class="contentBottom">
		<div class="rating">
			Rating:
			<span class="stars">
				<span class="star" id="star1"><img src="<?=$this->RootUrl?>template/img/starEmpty.png" /></span>
				<span class="star" id="star2"><img src="<?=$this->RootUrl?>template/img/starEmpty.png" /></span>
				<span class="star" id="star3"><img src="<?=$this->RootUrl?>template/img/starEmpty.png" /></span>
				<span class="star" id="star4"><img src="<?=$this->RootUrl?>template/img/starEmpty.png" /></span>
				<span class="star" id="star5"><img src="<?=$this->RootUrl?>template/img/starEmpty.png" /></span>
			</span>
			<span class="stat"><span class="bold"><?=$this->Content['rating']?></span> (<span class="count"><?=GetOne("select count(`vote`) from `votes` where `content_id`={$this->Content['id']} ")?></span> votes)</span>
		</div>
		<div class="user"><a href="<?=$this->RootUrl?>profile/<?=$this->Content['author']?>"><?=$this->getUser($this->Content['author'])?></a>, <?=date("d.m.Y H:i", $this->Content['createTime'])?></div>
		
		
		<div class="clear"></div>
	</div>
</div>
<? include SNIPPETS_PATH."comments.php"; ?>
<? include SNIPPETS_PATH."edit-content.php"; ?>