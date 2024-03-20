<?php while($content = $list->hasNextReply()):?>
<tr <?php if($content->uid == kboard_uid()):?>class='select'<?php endif?> >
	<td class="no"><?php echo $list->index()?></td>
	<td class="cate"><span style="color:"></span></td>
	<td class="title" style="padding-left:<?php echo ($depth+1)*5?>px">
		<img src="<?php echo $skin_path?>/images/icon-reply.png" alt="">	
		<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>" class="hx">
		<?php echo $content->title?></a>
		<a href="<?php echo esc_url($url->getDocumentURLWithUID($content->uid))?>#289220_comment" class="replyNum" title="댓글"><?php echo $content->getCommentsCount()?></a>
		<span class="extraimages">
			<?php if($content->isNew()):?><span class="kboard-default-new-notify">N</span><?php endif?>
			<?php if($content->secret):?><img src="<?php echo $skin_path?>/images/icon-lock.png" alt="<?php echo __('Secret', 'kboard')?>"><?php endif?>
		</span>
	</td>
	<td class="author"><span><?php echo $content->getUserDisplay()?></span></td>
	<td class="time" title="2 시간 전"><?php echo $content->getDate()?></td>
	<td class="m_no"><?php echo $content->vote?></td>
	<td class="m_no"><?php echo $content->view?></td>
	<?php if($board->isAdmin()):?>
		<td class="check m_no"><input type="checkbox" value="<?=$content->uid?>" name='doc_chk' title="Check This Article"/></td>
	<?php endif?>
</tr>
<?php $boardBuilder->builderReply($content->uid, $depth+1)?>
<?php endwhile?>