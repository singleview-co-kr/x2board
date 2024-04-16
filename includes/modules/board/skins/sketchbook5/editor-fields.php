<?php if($field['field_type'] == 'content'):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="kboard-content">
			<?php echo $editor_html ?>
		</div>
	</div>
<?php elseif($field['field_type'] == 'nick_name'):?>
	<?php if(!x2b_is_this_accessible()): //if($field['permission'] == 'always_visible' || (!$field['permission'] && $board->viewUsernameField)): ?>
		<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> required">
			<label class="attr-name" for="kboard-input-member-display"><span class="field-name"><?php echo esc_html($field_name)?></span> <span class="attr-required-text">*</span></label>
			<div class="attr-value"><input type="text" id="kboard-input-nick-name" name="nick_name" class="required" value="<?php echo $post->nick_name?esc_attr($post->nick_name):esc_attr($default_value)?>"<?php if($placeholder):?> placeholder="<?php echo esc_attr($placeholder)?>"<?php endif?>></div>
		</div>
		<div class="kboard-attr-row kboard-attr-password">
			<label class="attr-name" for="kboard-input-password"><?php echo __('Password', 'x2board')?> <span class="attr-required-text">*</span></label>
			<div class="attr-value"><input type="password" id="kboard-input-password" name="password" value="" placeholder="<?php echo __('Password', 'x2board')?>..."></div>
		</div>
	<?php endif?>
<?php elseif($field['field_type'] == 'captcha'):?>
	<?php if(false): //$board->useCAPTCHA && !$content->uid):?>
		<?php if(kboard_use_recaptcha()):?>
		<div class="kboard-attr-row <?php echo esc_attr($field['class'])?>">
			<label class="attr-name"></label>
			<div class="attr-value"><div class="g-recaptcha" data-sitekey="<?php echo kboard_recaptcha_site_key()?>"></div>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?></div>
		</div>
		<?php else:?>
		<div class="kboard-attr-row <?php echo esc_attr($field['class'])?>">
			<label class="attr-name" for="kboard-input-captcha"><img src="<?php echo kboard_captcha()?>" alt=""></label>
			<div class="attr-value"><input type="text" id="kboard-input-captcha" name="captcha" value="" placeholder="<?php echo __('CAPTCHA', 'x2board')?>...">
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?></div>
		</div>
		<?php endif?>
	<?php endif?>
<?php elseif($field['field_type'] == 'attach'): ?>
	<input type="file" name="files" id="file_software" class="file-upload" data-maxfilecount='<?php echo $n_file_max_attached_count?>' data-accpet_file_types="<?php echo $s_accept_file_types?>" data-max_each_file_size_mb="<?php echo $n_file_allowed_filesize_mb?>">
	<ul class="file-list list-unstyled mb-0">
		<?php foreach($post->get_uploaded_files() as $file_key=>$file_value):?>
			<li class="file my-1 row">
				<div class="file-name col-md-3">
					<img src='<?=$file_value['thumbnail_abs_url']?>' class='attach_thumbnail'>
					<?=$file_value['file_name']?> 
				</div>
				<div class="del-button col-md-1">
					<button type="button" class="btn btn-sm btn-danger file-embed" data-thumbnail_abs_url="<?=$file_value['thumbnail_abs_url']?>" <?php if( $file_value['file_type'] !== 'image'):?>disabled<?php endif?>><i class="fa fa-plus"></i></button>
					<button type="button" class="btn btn-sm btn-danger file-delete" data-file_uid="<?=$file_value['file_uid']?>"><i class="far fa-trash-alt"></i></button>
				</div>
				<div class="progress col-md-7 my-auto px-0">
					<!-- <div class="progress-bar progress-bar-striped bg-info" role="progressbar" style="width: 100%;"></div> -->
				</div>
			</li>
		<?php endforeach?>
	</ul>
<?php elseif($field['field_type'] == 'wp_editor'):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo isset($field['custom_class']) && $field['custom_class'] ? esc_attr($field['custom_class']) : ''?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<?php 
			// $content->option->{$meta_key}?$content->option->{$meta_key}:$default_value
			wp_editor($default_value, $this->get_option_field_name($meta_key), array('media_buttons'=>x2b_is_this_accessible(), 'editor_height'=>400, 'editor_class'=>$required))?>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>	
<?php elseif($field['field_type'] == 'category'):?> 
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span></label>
		<div class="attr-value">
			<div class="kboard-tree-category-wrap">
				<select id="category_id" name="category_id" class="category">
					<option value=""><?=__('Category select', 'kboard')?></option>
					<?php 
					$category_list = x2b_get_post_category_list();
					foreach($category_list as $cat_id=>$option_val):?>
						<option value="<?=$cat_id?>" <?php if($option_val->grant && $option_val->selected || $post->category_id == $cat_id):?> selected="selected" <?php endif?> <?php if(!$option_val->grant):?> disabled="disabled" <?php endif?>  >
						<?=str_repeat("&nbsp;&nbsp;",$option_val->depth)?> <?=$option_val->title?> (<?=$option_val->post_count?>)
						</option>
					<?php endforeach?>
				</select>
			</div>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
<?php elseif($field['field_type'] == 'title'):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> required">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span> <span class="attr-required-text">*</span></label>
		<div class="attr-value">
			<input type="text" id="<?php echo esc_attr($meta_key)?>" name="title" class="required" value="<?php echo $post->title?esc_attr($post->title):esc_attr($default_value)?>"<?php if($placeholder):?> placeholder="<?php echo esc_attr($placeholder)?>"<?php endif?>>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
<?php elseif($field['field_type'] == 'option'):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span></label>
		<div class="attr-value">
			<?php if(x2b_is_this_accessible($field['secret_permission'], $field['secret'])):?>
				<label class="attr-value-option"><input type="checkbox" name="secret" value="true" onchange="kboard_toggle_password_field(this)"<?php if($board->meta->secret_checked_forced && !$x2b_is_this_accessible()):?> checked disabled<?php endif?> <?php if($post->secret):?>checked <?php endif?>> <?php echo __('Secret', 'x2board')?></label>
			<?php endif?>
			<?php if(x2b_is_this_accessible($field['notice_permission'], $field['notice'])):?>
				<label class="attr-value-option"><input type="checkbox" name="notice" value="true"<?php if($post->notice):?> checked<?php endif?>> <?php echo __('Notice', 'x2board')?></label>
			<?php endif?>
			<?php if(x2b_is_this_accessible($field['allow_comment_permission'], $field['allow_comment'])):?>
				<label class="attr-value-option"><input type="checkbox" name="allow_comment" value="true"<?php if(strlen($post->title) == 0 || $post->allow_comment):?> checked<?php endif?>> <?php echo __('Comment', 'x2board')?></label>
			<?php endif?>
			<?php //do_action('kboard_skin_editor_option', $content, $board, $boardBuilder)?>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
	<?php if(!x2b_is_this_accessible()):  //$board->viewUsernameField):?>
	<div style="overflow:hidden;width:0;height:0;">
		<input style="width:0;height:0;background:transparent;color:transparent;border:none;" type="text" name="fake-autofill-fields">
		<input style="width:0;height:0;background:transparent;color:transparent;border:none;" type="password" name="fake-autofill-fields">
	</div>
	<!-- 비밀글 비밀번호 필드 시작 -->
	<div class="kboard-attr-row kboard-attr-password secret-password-row"<?php if(!$post->is_secret):?> style="display:none"<?php endif?>>
		<label class="attr-name" for="kboard-input-password"><?php echo __('Password', 'x2board')?> <span class="attr-required-text">*</span></label>
		<div class="attr-value"><input type="password" id="kboard-input-password" name="password" value="" placeholder="<?php echo __('Password', 'x2board')?>..."></div>
	</div>
	<!-- 비밀글 비밀번호 필드 끝 -->
	<?php endif?>
<?php elseif($field['field_type'] == 'search'):?>
	<?php if(isset($field['hidden']) && $field['hidden'] == '1'):?>
		<input type="hidden" name="allow_search" value="<?php echo esc_attr($default_value)?>">
	<?php else:?>
		<div class="kboard-attr-row <?php echo esc_attr($field['class'])?>">
			<label class="attr-name" for="kboard-select-wordpress-search"><span class="field-name"><?php echo esc_html($field_name)?></span></label>
			<div class="attr-value">
				<select id="kboard-select-wordpress-search" name="allow_search">
					<option value="1"<?php if($post->allow_search == '1'):?> selected<?php endif?>><?php echo __('Public', 'x2board')?></option>
					<option value="2"<?php if($post->allow_search == '2'):?> selected<?php endif?>><?php echo __('Only title (secret post)', 'x2board')?></option>
					<option value="3"<?php if($post->allow_search == '3'):?> selected<?php endif?>><?php echo __('Exclusion', 'x2board')?></option>
				</select>
				<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
			</div>
		</div>
	<?php endif?>
<?php elseif($field['field_type'] == 'text'):?>
	<?php if(isset($field['hidden']) && $field['hidden']):?>
		<input type="hidden" id="<?php echo esc_attr($meta_key)?>" class="<?php echo esc_attr($required)?>" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>" value="<?php echo $post->option->{$meta_key}?esc_attr($post->option->{$meta_key}):esc_attr($default_value)?>">
	<?php else:?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo isset($field['custom_class']) && $field['custom_class'] ? esc_attr($field['custom_class']) : ''?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<input type="text" id="<?php echo esc_attr($meta_key)?>" class="<?php echo esc_attr($required)?>" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>" value="<?php echo $post->option->{$meta_key}?esc_attr($post->option->{$meta_key}):esc_attr($default_value)?>"<?php if($placeholder):?> placeholder="<?php echo esc_attr($placeholder)?>"<?php endif?>>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
	<?php endif?>
<?php elseif($field['field_type'] == 'select' && $has_default_values):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo isset($field['custom_class']) && $field['custom_class'] ? esc_attr($field['custom_class']) : ''?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<select id="<?php echo esc_attr($meta_key)?>" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>"class="<?php echo esc_attr($required)?>">
				<option value=""><?php echo __('Select', 'x2board')?></option>
				<?php foreach($field['row'] as $option_key=>$option_value):?>
					<?php if(isset($option_value['label']) && $option_value['label']):?>
						<?php if(false): //$post->option->{$meta_key}):?>
							<option value="<?php echo esc_attr($option_value['label'])?>"<?php if($this->is_saved_option($post->option->{$meta_key}, $option_value['label'])):?> selected<?php endif?>><?php echo esc_html($option_value['label'])?></option>
						<?php else:?>
							<option value="<?php echo esc_attr($option_value['label'])?>"<?php if($default_value && $default_value==$option_key):?> selected<?php endif?>><?php echo esc_html($option_value['label'])?></option>
						<?php endif?>
					<?php endif?>
				<?php endforeach?>
			</select>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
<?php elseif($field['field_type'] == 'radio' && $has_default_values):?>
	<?php if(isset($field['row']) && $field['row']):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo isset($field['custom_class']) && $field['custom_class'] ? esc_attr($field['custom_class']) : ''?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<input type="hidden" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>" value="">
			<?php foreach($field['row'] as $option_key=>$option_value):?>
				<?php if(isset($option_value['label']) && $option_value['label']):?>
					<?php if(false)://$post->option->{$meta_key}):?>
						<label class="attr-value-label"><input type="radio" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>"class="<?php echo esc_attr($required)?>"<?php if($fields->isSavedOption($post->option->{$meta_key}, $option_value['label'])):?> checked<?php endif?> value="<?php echo esc_attr($option_value['label'])?>"> <?php echo esc_html($option_value['label'])?></label>
					<?php else:?>
						<label class="attr-value-label"><input type="radio" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>"class="<?php echo esc_attr($required)?>"<?php if($default_value && $default_value==$option_key):?> checked<?php endif?> value="<?php echo esc_attr($option_value['label'])?>"> <?php echo esc_html($option_value['label'])?></label>
					<?php endif?>
				<?php endif?>
			<?php endforeach?>
			<label class="attr-reset-button" style="cursor:pointer" onclick="x2board_radio_reset(this)"><?php echo __('Reset', 'x2board')?></label>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
	<?php endif?>
<?php elseif($field['field_type'] == 'checkbox' && $has_default_values):?>
	<?php if(isset($field['row']) && $field['row']):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo isset($field['custom_class']) && $field['custom_class'] ? esc_attr($field['custom_class']) : ''?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<input type="hidden" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>" value="">
			<?php foreach($field['row'] as $option_key=>$option_value):?>
				<?php if(isset($option_value['label']) && $option_value['label']):?>
					<?php if(false): //$post->option->{$meta_key}):?>
						<label class="attr-value-label"><input type="checkbox" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>[]"class="<?php echo esc_attr($required)?>"<?php if($fields->isSavedOption($post->option->{$meta_key}, $option_value['label'])):?> checked<?php endif?> value="<?php echo esc_attr($option_value['label'])?>"> <?php echo esc_html($option_value['label'])?></label>
					<?php else:?>
						<label class="attr-value-label"><input type="checkbox" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>[]"class="<?php echo esc_attr($required)?>"<?php if($default_value && in_array($option_value['label'], $default_value)):?> checked<?php endif?> value="<?php echo esc_attr($option_value['label'])?>"> <?php echo esc_html($option_value['label'])?></label>
					<?php endif?>
				<?php endif?>
			<?php endforeach?>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
	<?php endif?>
<?php elseif($field['field_type'] == 'textarea'):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo isset($field['custom_class']) && $field['custom_class'] ? esc_attr($field['custom_class']) : ''?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<textarea id="<?php echo esc_attr($meta_key)?>" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>"class="editor-textarea <?php echo esc_attr($required)?>"<?php if($placeholder):?> placeholder="<?php echo esc_attr($placeholder)?>"<?php endif?>><?php echo $post->option->{$meta_key}?esc_textarea($post->option->{$meta_key}):esc_textarea($default_value)?></textarea>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
<?php elseif($field['field_type'] == 'html'):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?>">
		<?php echo $html?>
	</div>
<?php elseif($field['field_type'] == 'shortcode'):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?>">
		<?php echo do_shortcode($shortcode)?>
	</div>
<?php elseif($field['field_type'] == 'date'):?>
	<?php
	wp_enqueue_style('kboard-jquery-flick-style');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('kboard-field-date');
	?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo isset($field['custom_class']) && $field['custom_class'] ? esc_attr($field['custom_class']) : ''?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<input type="text" id="<?php echo esc_attr($meta_key)?>" class="<?php echo esc_attr($required)?> datepicker" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>" value="<?php echo $post->option->{$meta_key}?esc_attr($post->option->{$meta_key}):esc_attr($default_value)?>"<?php if($placeholder):?> placeholder="<?php echo esc_attr($placeholder)?>"<?php endif?>>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
<?php elseif($field['field_type'] == 'time'):?>
	<?php
	wp_enqueue_style('jquery-timepicker');
	wp_enqueue_script('jquery-timepicker');
	wp_enqueue_script('kboard-field-time');
	?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<input type="text" id="<?php echo esc_attr($meta_key)?>" class="<?php echo esc_attr($required)?> timepicker" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>" value="<?php echo $post->option->{$meta_key}?esc_attr($post->option->{$meta_key}):esc_attr($default_value)?>"<?php if($placeholder):?> placeholder="<?php echo esc_attr($placeholder)?>"<?php endif?>>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
<?php elseif($field['field_type'] == 'email'):?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<input type="email" id="<?php echo esc_attr($meta_key)?>" class="<?php echo esc_attr($required)?>" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>" value="<?php echo $post->option->{$meta_key}?esc_attr($post->option->{$meta_key}):esc_attr($default_value)?>"<?php if($placeholder):?> placeholder="<?php echo esc_attr($placeholder)?>"<?php endif?>>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
<?php elseif($field['field_type'] == 'address'):?>
	<?php
	wp_enqueue_script('kboard-field-address');
	?>
	<div class="kboard-attr-row <?php echo esc_attr($field['class'])?> meta-key-<?php echo esc_attr($meta_key)?> <?php echo esc_attr($required)?>">
		<label class="attr-name" for="<?php echo esc_attr($meta_key)?>"><span class="field-name"><?php echo esc_html($field_name)?></span><?php if($required):?> <span class="attr-required-text">*</span><?php endif?></label>
		<div class="attr-value">
			<div class="kboard-row-postcode">
				<input type="text" id="<?php echo esc_attr($meta_key)?>_postcode" class="kboard-postcode" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>_postcode" value="<?php echo esc_attr($post->option->{$meta_key.'_postcode'})?>" placeholder="<?php echo __('Zip Code', 'x2board')?>" style="width:160px;"> <button type="button" class="kboard-default-button-small kboard-postcode-address-search-button" onclick="kboard_postcode_address_search('<?php echo esc_attr($meta_key)?>_postcode', '<?php echo esc_attr($meta_key)?>_address_1', '<?php echo esc_attr($meta_key)?>_address_2')"><?php echo __('Search', 'x2board')?></button>
			</div>
			<div class="kboard-row-address-1">
				<input type="text" id="<?php echo esc_attr($meta_key)?>_address_1" class="kboard-address-1" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>_address_1" value="<?php echo esc_attr($post->option->{$meta_key.'_address_1'})?>" placeholder="<?php echo __('Address', 'x2board')?>">
			</div>
			<div class="kboard-row-address-2">
				<input type="text" id="<?php echo esc_attr($meta_key)?>_address_2" class="kboard-address-2" name="<?php echo esc_attr($this->get_option_field_name($meta_key))?>_address_2" value="<?php echo esc_attr($post->option->{$meta_key.'_address_2'})?>" placeholder="<?php echo __('Address 2', 'x2board')?>">
			</div>
			<?php if(isset($field['description']) && $field['description']):?><div class="description"><?php echo esc_html($field['description'])?></div><?php endif?>
		</div>
	</div>
<?php endif?>