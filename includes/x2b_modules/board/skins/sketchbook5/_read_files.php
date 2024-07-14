<?php if ( !defined( 'ABSPATH' ) ) {
    exit;  // Exit if accessed directly.
}?>

<div id="files_<?php echo $post->post_id?>" class="rd_fnt rd_file<?php if($mi->show_files == ' '):?> hide<?php endif ?>">
	<table class="bd_tb">
		<caption class="blind">Atachment</caption>
		<tr>
			<th scope="row" class="ui_font"><strong><?php echo __('lbl_uploaded_file', X2B_DOMAIN)?></strong> <span class="fnt_count">'<b><?php echo $post->get('uploaded_count')?></b>'</span></th>
			<td>
				<?php if($mi->files_type == ' '):?><!-- cond="!$mi->files_type" -->
					<ul>
						<!-- loop="$post->get_uploaded_files()=>$key,$file" -->
						<?php foreach($post->get_uploaded_files() as $key => $file): ?>
							<li><a class="bubble" href="<?php echo $file->download_url?>" title="[File Size:<?php echo esc_attr(number_format($file->file_size/1000))?>KB/Download:<?php echo number_format($file->download_count)?>]"><?php echo $file->source_filename ?></a><span class="comma">,</span></li>
						<?php endforeach ?>
					</ul>
				<?php endif?>
				<?php if($mi->files_type == 'Y'):?><!-- cond="$mi->files_type" -->
					<ul>
						<?php foreach($post->get_uploaded_files() as $key => $file): // <block loop="$post->get_uploaded_files()=>$key,$file"> 
$ext = substr($file->source_filename, -4);
$ext = strtolower($ext);
$ext_img = in_array($ext,array('.jpg','jpeg','.gif','.png'));
$ext_video = in_array($ext,array('.mpg','mpeg','.avi','.wmv','.mp4','.mov','.mkv','.swf','.flv','.ogv','webm'));
$ext_audio = in_array($ext,array('.mp3','.ogg','.wma','.wav','.ape','flac','.mid'));
$s_file_size = esc_attr(number_format($file->file_size/1000));
?>
						<?php if($mi->files_img == ' ' && $ext_img):?><!-- cond="!$mi->files_img && $ext_img" -->
							<li><a class="bubble" href="<?php echo $file->download_url ?>" title="[File Size:<?php echo $s_file_size?>KB/Download:<?php echo number_format($file->download_count)?>]"><?php echo $file->source_filename ?></a><span class="comma">,</span></li>
						<?php endif?>
						<?php if($mi->files_video == ' ' && $ext_video):?><!-- cond="!$mi->files_video && $ext_video" -->
							<li><a class="bubble" href="<?php echo $file->download_url ?>" title="[File Size:<?php echo $s_file_size?>KB/Download:<?php echo number_format($file->download_count)?>]"><?php echo $file->source_filename ?></a><span class="comma">,</span></li>
						<?php endif?>
						<?php if($mi->files_audio == ' ' && $ext_audio):?><!-- cond="!$mi->files_audio && $ext_audio" -->
							<li><a class="bubble" href="<?php echo $file->download_url ?>" title="[File Size:<?php echo $s_file_size?>KB/Download:<?php echo number_format($file->download_count)?>]"><?php echo $file->source_filename ?></a><span class="comma">,</span></li>
						<?php endif?>
						<?php if($mi->files_etc == ' ' && (!$ext_img && !$ext_video && !$ext_audio)):?><!-- cond="!$mi->files_etc && (!$ext_img && !$ext_video && !$ext_audio)" -->
							<li><a class="bubble" href="<?php echo $file->download_url ?>" title="[File Size:<?php echo $s_file_size?>KB/Download:<?php echo number_format($file->download_count)?>]"><?php echo $file->source_filename ?></a><span class="comma">,</span></li>
						<?php endif?>
						<?php endforeach ?><!-- </block> -->
					</ul>
				<?php endif?>
			</td>
		</tr>
	</table>
</div>