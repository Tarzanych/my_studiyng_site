	<div class="center pageTitle">
		<?=t('[REG_NEW]')?>
	</div>
	<? if (count($this->Errors) > 0) { ?>
	<div class="regerrorsBlock">
		<div class="bold"><?=t('[SOME_ERRORS]')?>:</div>
		<ul>
		<?
		foreach ($this->Errors as $error) {
		?>
			<li><?=$error?></li>
		<?
		}
		?>
		</ul>
	</div>
	<? } ?>
	<form action="/register" method="post" class="registerForm">
		<div class="registerBlock">
			<table>
				<tr>
					<td>
						<div><?=t('[NICKNAME]')?>*:</div>
						<div class="valDesc"><?=t('from')?> 3 <?=t('to')?> 25 <?=t('symbols')?></div>
					</td>
					<td>
						<input name="nickname" type="text" value="<?=isset($_POST['nickname']) ? htmlspecialchars($_POST['nickname']) : "" ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<div><?=t('[PASSWORD]')?>*:</div>
						<div class="valDesc"><?=t('from')?> 6 <?=t('to')?> 15 <?=t('symbols')?></div>
					</td>
					<td>
						<input name="password" type="password" />
					</td>
				</tr>
				<tr>
					<td>
						<div><?=t('[PASSCONFIRM]')?>*:</div>
						<div class="valDesc"><?=t('from')?> 6 <?=t('to')?> 15 <?=t('symbols')?></div>
					</td>
					<td>
						<input name="passconfirm" type="password" />
					</td>
				</tr>
				<tr>
					<td>
						Email*:
					</td>
					<td>
						<input name="email" type="text" value="<?=isset($_POST['email']) ? htmlspecialchars($_POST['email']) : "" ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<?=t('[FIRSTNAME]')?>:
					</td>
					<td>
						<input name="firstname" type="text" value="<?=isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : "" ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<?=t('[SECONDNAME]')?>:
					</td>
					<td>
						<input name="secondname" type="text" value="<?=isset($_POST['secondname']) ? htmlspecialchars($_POST['secondname']) : "" ?>" />
					</td>
				</tr>
				
			</table>
			
		</div>
		
		<div class="center">
			<input type="hidden" name="Action" value="register" />
			<input type="submit" class="registerButton" value="<?=t('[REGISTER]')?>" />
		</div>
	</form>