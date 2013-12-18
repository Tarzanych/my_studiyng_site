<? include SNIPPETS_PATH."admin-menu.php"; ?>
<div class="pageTitle center">
	Users list
</div>

<table class="usersTable adminTable">
	<tr>
		<td>
			<a href="<?=$this->RootUrl?>admin/userslist?order_by=<?=(isset($_GET['order_by']) && $_GET['order_by']=='id_asc' ? 'id_desc' : 'id_asc')?>">Id</a>
		<? if (isset($_GET['order_by']) && $_GET['order_by']=="id_asc") { ?>
			<img src="<?=$this->RootUrl?>template/img/up.png" class="arrow" alt="asc" />
		<? } ?>	
		<? if (isset($_GET['order_by']) && $_GET['order_by']=="id_desc") { ?>
			<img src="<?=$this->RootUrl?>template/img/down.png" class="arrow" alt="desc" />
		<? } ?>	
		</td>
		<td>
			<a href="<?=$this->RootUrl?>admin/userslist?order_by=<?=(isset($_GET['order_by']) && $_GET['order_by']=='nickname_asc' ? 'nickname_desc' : 'nickname_asc')?>">Nickname</a>
		<? if (isset($_GET['order_by']) && $_GET['order_by']=="nickname_asc") { ?>
			<img src="<?=$this->RootUrl?>template/img/up.png" class="arrow" alt="asc"  />
		<? } ?>	
		<? if (isset($_GET['order_by']) && $_GET['order_by']=="nickname_desc") { ?>
			<img src="<?=$this->RootUrl?>template/img/down.png" class="arrow" alt="desc"  />
		<? } ?>	
		</td>
		<td>Active</td>
		<td>Registration time</td>
		<td>
			<a href="<?=$this->RootUrl?>admin/userslist?order_by=<?=(isset($_GET['order_by']) && $_GET['order_by']=='login_asc' ? 'login_desc' : 'login_asc')?>">Last login time</a>
		<? if (isset($_GET['order_by']) && $_GET['order_by']=="login_asc") { ?>
			<img src="<?=$this->RootUrl?>template/img/up.png" class="arrow" alt="asc"  />
		<? } ?>	
		<? if (isset($_GET['order_by']) && $_GET['order_by']=="login_desc") { ?>
			<img src="<?=$this->RootUrl?>template/img/down.png" class="arrow" alt="desc"  />
		<? } ?>	
		</td>
		
		<td>Edit profile</td>
		<td>Block profile</td>
		<td>Delete profile</td>
	</tr>
<?
	foreach ($this->UsersList as $user) {
?>
	<tr <?=$user['deleted']==1 || $user['blocked']==1 ? 'class="deleted"' : '' ?>>
		<td class="center"><?=$user['id']?></td>
		<td>
			<a href="<? if ($user['deleted']==0) { ?><?=$this->RootUrl?>profile/<?=$user['id']?><? } else { echo "#"; } ?>"><?=$user['nickname']?></a>
			<?=$user['deleted']==1 ? '<span class="red">(deleted)</span>' : ($user['blocked']==1 ? '<span class="red">(blocked)</span>' : '') ?>
			<?=$user['isAdmin']==1 ? '<span class="green">(admin)</span>' : "" ?>
		</td>
		<td class="center"><?=$user['active']==1 ? '<span class="green">yes</span>' : '<span class="red">no</span>'?></td>
		<td><?=date("d.m.Y H:i",$user['regDate'])?></td>
		<td <?=$user['lastActive'] == 0 ? 'class="center"' : "" ?>><?=$user['lastActive']>0 ? date("d.m.Y H:i",$user['lastActive']) : "-"?></td>
		
		<td class="center">
		<? if ($user['deleted']==0 && ((!$user['isAdmin'] || $User->Permissions['root']==1) || $User->Id==$user['id'])) { ?>
			<a href="<?=$this->RootUrl?>profile/editprofile/<?=$user['id']?>">edit</a>
		<? } ?>
		</td>
		<td class="center">
		<? if ($user['blocked']==0 && $user['deleted']==0 && (!$user['isAdmin'] || $User->Permissions['root']==1) && $user['id'] != $User->Id) { ?>
			<a class="red" href="<?=$this->RootUrl?>admin/userslist/blockprofile/<?=$user['id']?><?=isset($_GET) ? "?".http_build_query($_GET) : ""?>"  onclick="if (confirm('Do you really want to block profile <?=$user['nickname']?>?')) {return true;} else {return false;}">block</a>
		<? } elseif ($user['blocked']==1 && $user['deleted']==0 && (!$user['isAdmin'] || $User->Permissions['root']==1) && $user['id'] != $User->Id) { ?>
			<a class="green" href="<?=$this->RootUrl?>admin/userslist/unblockprofile/<?=$user['id']?><?=isset($_GET) ? "?".http_build_query($_GET) : ""?>">unblock</a>
		<? } ?>
		</td>
		<td class="center">
		<? if ($user['deleted']==0 && (!$user['isAdmin'] || $User->Permissions['root']==1) && $user['id'] != $User->Id ){ ?>
			<a class="red" href="<?=$this->RootUrl?>admin/userslist/delprofile/<?=$user['id']?><?=isset($_GET) ? "?".http_build_query($_GET) : ""?>" onclick="if (confirm('Do you really want to delete profile <?=$user['nickname']?>?')) {return true;} else {return false;}">delete</a>
		<? } ?>
		</td>
	</tr>
<?
	}
?>
</table>