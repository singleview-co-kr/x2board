<div class="cnb_n_list">
	<div>
		<div class="bd_cnb clear css3pie">
			<a class="home" href="<?=get_permalink()?>" title="글 수 '000'"><i class="home ico_16px"><?=__('All', 'kboard')?></i></a>
			<div class="dummy_ie fr"></div>
			<ul class="bubble bg_f_f9 css3pie" style="overflow: visible;">
			<?php
			$category_nav = $board->tree_category->getCategoryItemList();
			?>
				<li class="cnbMore" style="visibility: hidden;"><a href="#" class="bubble" title="분류 더보기"><i class="fa fa-caret-down"></i></a></li>
				<?php if(isset($category_nav['parent'])):?>
					<li class="a1"><a href="<?=get_permalink()?>?category/<?=$category_nav['parent']->category_id?>" ><?=$category_nav['parent']->category_name?> (<?=$category_nav['parent']->document_count?>)</a></li>
					<li class="a1">&larr;</li>
				<?php endif?>
				<?php if(isset($category_nav['cur'])):?>
					<li class="a1 on"><a href="#" ><?=$category_nav['cur']->category_name?> (<?=$category_nav['cur']->document_count?>)</a> </li>
				<?php endif?>
				
				<?php if(isset($category_nav['children'])):?>
					<?php if(isset($_GET['category_id'])):?>
					<li class="a1">&rarr;</li>
					<?php endif?>
				<?php endif?>
				<?php foreach($category_nav['children'] as $item):?>
					<li class="a1"><a href="<?=get_permalink()?>?category/<?=$item->category_id?>" ><?=$item->category_name?> (<?=$item->document_count?>)</a></li>
				<?php endforeach?>
			</ul>
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