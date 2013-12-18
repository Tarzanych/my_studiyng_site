					<div class="accountBlock">
						<? if ($User->IsAnonymous) { ?>
						<div class="login">
							<div><a href="#"><?=t('[ENTER]')?></a></div>
							<div class="loginForm">
								<form action="/login" method="post">
									<table>
										<tr>
											<td><?=t('[NICKNAME]')?>:</td>
											<td><input type="text" name="login" /></td>
										</tr>
										<tr>
											<td><?=t('[PASSWORD]')?></td>
											<td><input type="password" name="password" /></td>
										</tr>
										<tr>
											<td colspan="2">
												<div>
													<input type="hidden" name="Action" value="login" />
													<input type="submit" value="<?=t('[ENTER]')?>" />
												</div>
											</td>
										</tr>
									</table>
								</form>
							</div>
						</div> &bull; 
						<div class="reg">
							<div><a href="/register"><?=t('[REGISTER]')?></a></div>
						</div>
						<? } else { ?>
							<div class="profile">
								<?=t('Welcome')?>, <a href="<?=$Page->RootUrl?>profile/<?=$User->Id?>"><?=$User->Nickname?></a>
							<?
								if ($User->IsBlocked) {
							?>
								<span class="red">(<?=t('blocked')?>)</span>
							<?
								} elseif (!$User->IsActive) {
							?>
								(<?=t('not active')?>)
							<?
								}
							?>
							</div> &bull;
							<? if ((isset($User->Permissions['root']) && $User->Permissions['root'] == 1) || (isset($User->Permissions['admin']) && $User->Permissions['admin'] == 1)) { ?>
							<div class="adminLink">
								<a href="<?=$Page->RootUrl?>admin"><?=t('[ADMIN_PANEL]')?></a>
							</div>
							&bull;
							<? } ?>
							<div class="logout">
								<a href="/logout"><?=t('[LOGOUT]')?></a>
							</div>
						<? } ?>
					</div>
					