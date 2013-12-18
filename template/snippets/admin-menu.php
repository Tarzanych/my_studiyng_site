<div class="adminMenu">
	<div <?=$this->QueryElements[1]=="userslist" ? 'class="active"' : "" ?>><a href="<?=$this->RootUrl?>admin/userslist">Users list</a></div>
	<div <?=$this->QueryElements[1]=="categories" ? 'class="active"' : "" ?>><a href="<?=$this->RootUrl?>admin/categories">Categories</a></div>
	<div <?=$this->QueryElements[1]=="content" ? 'class="active"' : "" ?>><a href="<?=$this->RootUrl?>admin/content">Content</a></div>
	<div <?=$this->QueryElements[1]=="language" ? 'class="active"' : "" ?>><a href="<?=$this->RootUrl?>admin/language">Language vars</a></div>
</div>