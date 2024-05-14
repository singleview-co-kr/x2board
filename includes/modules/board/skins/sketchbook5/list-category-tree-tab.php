<?php
// var_dump($category_recursive);
?>
<div class="cnb_n_list">
	<div>
		<div class="bd_cnb clear css3pie">
			<a class="home" href="<?php echo x2b_get_url()?>" title="글 수 '000'"><i class="home ico_16px"><?php echo __('All', 'kboard')?></i></a>
			<div class="dummy_ie fr"></div>
			<ul class="bubble bg_f_f9 css3pie" style="overflow: visible;">
				<li class="cnbMore" style="visibility: hidden;"><a href="#" class="bubble" title="분류 더보기"><i class="fa fa-caret-down"></i></a></li>
				<?php if(isset($category_recursive['parent'])):?>
					<li class="a1"><a href="<?php echo x2b_get_url('category', $category_recursive['parent']->title)?>" ><?php echo $category_recursive['parent']->title?> (<?php echo $category_recursive['parent']->post_count?>)</a></li>
					<li class="a1">&larr;</li>
				<?php endif?>
				<?php if(isset($category_recursive['cur'])):?>
					<li class="a1 on"><a href="#" ><?php echo $category_recursive['cur']->title?> (<?php echo $category_recursive['cur']->post_count?>)</a> </li>
				<?php endif?>
				
				<?php if(isset($category_recursive['children'])):?>
					<?php if(isset($_GET['category_id'])):?>
					<li class="a1">&rarr;</li>
					<?php endif?>
				<?php endif?>
				<?php foreach($category_recursive['children'] as $item):?>
					<li class="a1"><a href="<?php echo x2b_get_url('category', $item->title)?>" ><?php echo $item->title?> (<?php echo $item->post_count?>)</a></li>
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