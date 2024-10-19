<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}?>

<form action="<?php echo x2b_get_url()?>" method="get" onsubmit="return procFilter(this, search)" no-error-return-url="true" style="margin-bottom: 0px;">
	<input type="hidden" name="board_id" value="<?php echo $board_id?>" />
	<input type="hidden" name="category" value="<?php echo $category?>" />
	<table class="bd_tb">
		<tr>
			<td>
				<span class="select itx">
					<select name="search_target" id='select_box_search_target'>
						<!-- <option loop="$search_option=>$key,$val" value="{$key}" selected="selected"|cond="$search_target==$key">{$val}</option> -->
						<?php foreach($search_option as $key => $val): ?>
							<option value="<?php echo $key?>" <?php if($search_target==$key): ?>selected="selected" <?php endif?>><?php echo $val?></option>
						<?php endforeach ?>
					</select>
				</span>
			</td>
			<td class="itx_wrp">
				<input type="text" name="search_keyword" value="<?php echo htmlspecialchars((string)$search_keyword)?>" class="itx srch_itx" />
			</td>
			<td>
				<button type="submit" onclick="jQuery(this).parents('form').submit();return false" class="bd_btn"><?php echo __('cmd_search', X2B_DOMAIN)?></button>
				<?php if(isset($last_division)): ?>  <!-- cond="$last_division"  -->
				<a class="bd_btn" href="<?php echo x2b_get_url('page',1,'post_id','','division',$last_division,'last_division','')?>"><?php echo __('cmd_search_next', X2B_DOMAIN)?></a>
				<?php endif?>
			</td>
		</tr>
	</table>
</form>

