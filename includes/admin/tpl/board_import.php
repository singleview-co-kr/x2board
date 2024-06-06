<?php if(!defined('ABSPATH')) exit;?>
<div class="wrap">
    <?php include 'header.php' ?>
	
	<h2 class="nav-tab-wrapper">
		<a href="#tab-x2board-setting-0" class="tab-x2board nav-tab nav-tab-active" onclick="x2board_setting_tab_change(0);"><?php echo __('XE2', 'x2board')?></a>
		<a href="#tab-x2board-setting-1" class="tab-x2board nav-tab" onclick="x2board_setting_tab_change(1);"><?php echo __('KBoard', 'x2board')?></a>
	</h2>
	
	<div class="tab-x2board-setting tab-x2board-setting-active">
		<form action="<?=admin_url('admin-post.php')?>" method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( X2B_CMD_ADMIN_PROC_UPDATE_SEQ );?>
            <input type="hidden" name="action" value="<?php echo X2B_CMD_ADMIN_PROC_UPDATE_SEQ ?>">
            <table class="form-table">
                <tbody>
					<tr valign="top">
                        <th scope="row"><label for=""><?=__('x2b_sequence_max_id', 'x2board')?></label></th>
                        <td><?php echo $n_cur_auto_increment?></td>
                    </tr>
					<tr valign="top">
                        <th scope="row"><label for=""><?=__('update_x2b_sequence_max_id', 'x2board')?></label></th>
                        <td>
                            <input type="text" name="x2b_sequence_max_id">
                            <p class="description"><?=__('x2b_sequence_max_id_desc', 'x2board')?></p>
							<br>
                            <input type="submit" class="button-primary" value="<?=__('update', 'x2board')?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <form action="<?=admin_url('admin-post.php')?>" method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( X2B_CMD_ADMIN_PROC_IMPORT_BOARD );?>
            <input type="hidden" name="action" value="<?php echo X2B_CMD_ADMIN_PROC_IMPORT_BOARD ?>">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><label for=""><?=__('XE 백업 파일 선택', 'x2board')?></label></th>
                        <td>
                            <input type="file" name="xe_export_xml_file" accept=".xml,.zip">
                            <p class="description"><?=__('XE2에서 추출한 xml 파일을 선택하세요. 여러 파일은 zip으로 압축하세요.', 'x2board')?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for=""><?=__('게시판 선택', 'x2board')?></label></th>
                        <td>
                            <select name="board_id">
                                <option value=""><?=__('게시판 선택', 'x2board')?></option>
                            <?php foreach($this->items as $o_board): ?>
                                <option value="<?php echo $o_board->board_id?>"><?php echo $o_board->board_title?></option>
                            <?php endforeach ?>
                            </select>
                            <br>
                            <input type="submit" class="button-primary" value="<?=__('import', 'x2board')?>">
                            <p class="description"><?=__('입력하기 버튼을 누르면 선택 게시판을 초기화하고 XE2에서 추출한 XML 데이터를 추가합니다.', 'x2board')?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
	</div>

	<div class="tab-x2board-setting">
        <table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"></th>
					<td>
						웹호스팅의 하드와 데이터베이스 전체 백업기능이 있다면 먼저 웹호스팅의 백업 기능을 사용해보세요.
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for=""><?php echo __('백업파일 다운로드', 'x2board')?></label></th>
					<td>
						<form action="<?php echo admin_url('admin-post.php')?>" method="post">
							<?php wp_nonce_field('kboard-backup-download', 'kboard-backup-download-nonce');?>
							<input type="hidden" name="action" value="kboard_backup_download">
							<input type="submit" class="button-primary" value="<?php echo __('백업파일 다운로드', 'x2board')?>">
							<p class="description"><?php echo __('백업파일을 다운로드 받습니다. 파일은 xml 파일이며 복구하기를 통해 백업된 상태로 되돌릴 수 있습니다.', 'x2board')?></p>
						</form>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for=""><?php echo __('백업파일 선택', 'x2board')?></label></th>
					<td>
						<form action="<?php echo admin_url('admin-post.php')?>" method="post" enctype="multipart/form-data">
							<?php wp_nonce_field('kboard-restore-execute', 'kboard-restore-execute-nonce');?>
							<input type="hidden" name="action" value="kboard_restore_execute">
							<input type="file" name="kboard_backup_xml_file" accept=".xml">
							<br>
							<input type="submit" class="button-primary" value="<?php echo __('복구하기', 'x2board')?>">
							<p class="description"><?php echo __('xml 파일을 선택하고 복구하기 버튼을 누르세요. 지금까지의 데이터는 삭제되고 복원파일 데이터를 입력합니다.', 'x2board')?></p>
						</form>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for=""><?php echo __('Attachments', 'x2board')?></label></th>
					<td>
						<p class="description"><?php echo __('<code>/wp-content/uploads/kboard_attached</code> 이 경로의 모든 파일을 FTP를 사용해서 다운로드받아 옮겨주세요.', 'x2board')?></p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>	