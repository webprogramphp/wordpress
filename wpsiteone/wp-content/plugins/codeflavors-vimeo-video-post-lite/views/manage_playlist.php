<div class="wrap">
	<div class="icon32 icon32-posts-video" id="icon-edit"><br></div>
	<h2>
		<?php echo $title;?>
		<a class="add-new-h2" href="<?php menu_page_url('cvm_auto_import');?>"><?php _e('Cancel', 'codeflavors-vimeo-video-post-lite');?></a>	
	</h2>
		
	<form method="post" action="<?php echo $form_action;?>">
		<?php if( isset($error) ):?>
		<?php echo $error;?>
		<div id="message" class="error">
			<p><?php echo $error;?></p>
		</div>
		<?php endif;?>
		<?php wp_nonce_field('cvm-save-playlist', 'cvm_wp_nonce');?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="post_title">*<?php _e('Playlist name', 'cvm_playlist');?>:</label></th>
					<td>
						<input type="text" name="post_title" id="post_title" value="<?php echo $options['post_title'];?>" />
						<span class="description"><?php _e('A name for your internal reference.', 'codeflavors-vimeo-video-post-lite');?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="playlist_type">*<?php _e('Feed type')?>:</label></th>
					<td>
						<?php 
							$args = array(
								'options' => array(
									'album' => __('Vimeo album', 'codeflavors-vimeo-video-post-lite'),
									'channel' => __('Vimeo channel', 'codeflavors-vimeo-video-post-lite'),
									'user' => __('Vimeo user uploads', 'codeflavors-vimeo-video-post-lite'),
									'group' => __('Vimeo group', 'codeflavors-vimeo-video-post-lite'),
									'category' => __('Vimeo category', 'codeflavors-vimeo-video-post-lite')
								),
								'name' => 'playlist_type',
								'selected' => $options['playlist_type']
							);						
							cvm_select($args);
						?>
						<span class="description"><?php _e('Choose the kind of playlist you want to import.', 'codeflavors-vimeo-video-post-lite');?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="playlist_id">*<?php _e('Playlist ID', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
					<td>
						<input type="text" name="playlist_id" id="playlist_id" value="<?php echo $options['playlist_id'];?>" />
						<a href="#" id="cvm_verify_playlist" class="button"><?php _e('Check playlist', 'codeflavors-vimeo-video-post-lite');?></a>
						<div id="cvm_check_playlist" class="description"><?php _e('Enter playlist ID or user ID according to Feed Type selection.', 'codeflavors-vimeo-video-post-lite');?></div>
						
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="playlist_live"><?php _e('Add to import queue?', 'codeflavors-vimeo-video-post-lite');?></label></th>
					<td>
						<input type="checkbox" name="playlist_live" id="playlist_live" value="1"<?php cvm_check( $options['playlist_live'] );?> />
						<span class="description"><?php _e('If checked, playlist will be added to importing queue and will import when its turn comes.', 'codeflavors-vimeo-video-post-lite');?></span>
					</td>
				</tr>
				
				<?php 
					global $CVM_POST_TYPE;
					$args = array(
						'show_count' 		=> 1,
			    		'hide_empty'		=> 0,
						'taxonomy' 			=> $CVM_POST_TYPE->get_post_tax(),
						'name'				=> 'native_tax',
						'id'				=> 'native_tax',
						'selected'			=> $options['native_tax'],
			    		'hide_if_empty' 	=> true,
			    		'echo'				=> false,
						'show_option_all'	=> __('Select category (optional)', 'codeflavors-vimeo-video-post-lite')
					);
					$plugin_categories = wp_dropdown_categories($args);
					if( $plugin_categories ):
				?>
				<tr valign="top" id="native_tax_row"<?php cvm_hide( $options['theme_import'], true );?>>
					<th scope="row"><label for="native_tax"><?php _e('Import in category', 'codeflavors-vimeo-video-post-lite');?>:</label></th>
					<td>
						<?php echo $plugin_categories;?>
						<span class="description"><?php _e('Select category for all videos imported from this playlist.', 'codeflavors-vimeo-video-post-lite');?></span>
					</td>
				</tr>
				<?php endif;?>
				
				
				<?php
				$theme_support =  cvm_check_theme_support();
				if( $theme_support ):
				?>
				<tr>
					<td valign="top">
						<label for="theme_import"><?php printf( __('Import in <strong>%s</strong>? (PRO feature)', 'codeflavors-vimeo-video-post-lite'), $theme_support['theme_name']);?></label>
					</td>
					<td>
						<input type="checkbox" disabled="disabled" />
						<span class="description">
							<?php printf( __('PRO version can import videos directly as posts into your theme.', 'codeflavors-vimeo-video-post-lite'), $theme_support['theme_name']);?>
						</span>
					</td>
				</tr>				
				<?php 
					endif
				?>
				<!-- 
				<tr valign="top">
					<th scope="row"><label for=""></label></th>
					<td>
					</td>
				</tr>
				-->				
			</tbody>
		</table>
		<?php submit_button( __('Save', 'codeflavors-vimeo-video-post-lite'));?>	
	</form>	
		
</div>