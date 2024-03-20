<div class="cnb_n_list">
	<div>
		<div class="bd_cnb clear css3pie">
			<a class="home" href="<?=get_permalink()?>" title="글 수 '000'"><i class="home ico_16px"><?=__('All', 'kboard')?></i></a>
			<select id='kboard_search_option' onchange="return kboard_tree_category_search('<?=get_permalink()?>')">
				<option value=""><?=__('Category select', 'kboard')?></option>
				<?php foreach($board->tree_category->buildTreeCategoryLinear() as $cat_id=>$option_val):?>
					<option value="<?=$cat_id?>" <?php if($option_val->selected):?> selected<?php endif?>>
					<?=str_repeat("&nbsp;&nbsp;",$option_val->depth)?> <?=$option_val->category_name?> (<?=$option_val->document_count?>)
					</option>
				<?php endforeach?>
			</select>
		</div>
	</div>
	<div class="lst_btn fr">
		<ul>
			<!-- <li class="classic on"><a class="bubble" href="" title="Text Style"><b>List</b></a></li>
			<li class="zine"><a class="bubble" href="" title="Text + Image Style"><b>Zine</b></a></li>
			<li class="gall"><a class="bubble" href="" title="Gallery Style"><b>Gallery</b></a></li> -->
		</ul>
	</div>
</div>