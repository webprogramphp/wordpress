	<?php if( !isset( $metabox ) ):?>
	<p class="description">
		<?php _e('Import videos from Vimeo.', 'codeflavors-vimeo-video-post-lite');?><br />
		<?php _e('Enter your search criteria and submit. All found videos will be displayed and you can selectively import videos into WordPress.', 'codeflavors-vimeo-video-post-lite');?>
	</p>
	<?php endif;?>
	<form method="get" action="" id="cvm_load_feed_form">
		<input type="hidden" name="post_type" value="<?php echo $this->post_type;?>" />
		<input type="hidden" name="page" value="cvm_import" />
		<input type="hidden" name="cvm_source" value="vimeo" />
		<?php if( !isset( $metabox ) ):?>
		<table class="form-table">
			<tr class="cvm_feed">
				<th valign="top" scope="row">
		<?php endif;?>
					<label for="cvm_feed"><?php _e('Feed type', 'codeflavors-vimeo-video-post-lite');?>:</label>
		<?php if( !isset( $metabox ) ):?>
				</th>
				<td>
		<?php endif;?>		
					<?php 
						$args = array(
							'options' => array(
								'search' => array(
									'text' => __('Search videos on Vimeo', 'codeflavors-vimeo-video-post-lite'),
									'title' => __('Enter Vimeo search query', 'codeflavors-vimeo-video-post-lite'),
								),
								'album' => array(
									'text' => __('Load Vimeo album', 'codeflavors-vimeo-video-post-lite'),
									'title' => __('Enter Vimeo album ID', 'codeflavors-vimeo-video-post-lite'),
								), 
								'channel' => array(
									'text' => __('Load Vimeo channel', 'codeflavors-vimeo-video-post-lite'),
									'title' => __('Enter Vimeo channel', 'codeflavors-vimeo-video-post-lite'),
								),
								'user' => array(
									'text' => __('User uploads', 'codeflavors-vimeo-video-post-lite'),
									'title' => __('Enter Vimeo user', 'codeflavors-vimeo-video-post-lite'),
								),
								'group' => array(
									'text' => __('Group videos', 'codeflavors-vimeo-video-post-lite'),
									'title' => __('Enter Vimeo group', 'codeflavors-vimeo-video-post-lite'),
								),
								'category' => array(
									'text' => __('Vimeo category', 'codeflavors-vimeo-video-post-lite'),
									'title' => __('Enter Vimeo category', 'codeflavors-vimeo-video-post-lite'),
								),
							),
							'name' 		=> 'cvm_feed',
							'id' 		=> 'cvm_feed',
							'selected' 	=> isset( $_GET['cvm_feed'] ) ? $_GET['cvm_feed'] : false
						);
						cvm_select($args);
					?>
			<?php if( !isset( $metabox ) ):?>		
					<span class="description"><?php _e('Select the type of feed you want to load.', 'codeflavors-vimeo-video-post-lite');?></span>									
				</td>
			</tr>
			
			<tr class="cvm_query">
				<th valign="top" scope="row">
			<?php endif;?>	
					<label for="cvm_query"><?php _e('Vimeo search query', 'codeflavors-vimeo-video-post-lite');?>:</label>
			<?php if( !isset( $metabox ) ):?>	
				</th>
				<td>
			<?php endif;?>	
					<input type="text" name="cvm_query" id="cvm_query" value="<?php echo  isset( $_GET['cvm_query'] ) ? $_GET['cvm_query'] : '';?>" />
			<?php if( !isset( $metabox ) ):?>		
					<span class="description"><?php _e('Enter search query, user ID, group ID, channel ID or album ID according to Feed Type selection.', 'codeflavors-vimeo-video-post-lite');?></span>
				</td>
			</tr>
			
			<tr class="cvm_order">
				<th valign="top" scope="row">
			<?php endif;?>
			
			<?php if( isset( $metabox ) ):?>
			<span class="cvm_order">
			<?php endif;?>
				
					<label for="cvm_order"><?php _e('Order by', 'codeflavors-vimeo-video-post-lite');?>:</label>
			<?php if( !isset( $metabox ) ):?>		
					</th>
				<td>
			<?php endif;?>	
					<?php 
						$args = array(
							'options' => array(
								'new' => __('Newest videos first', 'codeflavors-vimeo-video-post-lite'),
								'old' => __('Oldest videos first', 'codeflavors-vimeo-video-post-lite'),
								'played' => __('Most played', 'codeflavors-vimeo-video-post-lite'),
								'likes' => __('Most liked', 'codeflavors-vimeo-video-post-lite'),
								'comments' => __('Most commented', 'codeflavors-vimeo-video-post-lite'),
								'relevant' => __('Relevancy', 'codeflavors-vimeo-video-post-lite')
							),
							'name' 		=> 'cvm_order',
							'id'		=> 'cvm_order',
							'selected' 	=> isset( $_GET['cvm_order'] ) ? $_GET['cvm_order'] : false
						);
						cvm_select( $args );
					?>
			
			<?php if( isset( $metabox ) ):?>
			</span>
			<?php endif;?>		
					
			<?php if( !isset( $metabox ) ):?>
                </td>
            </tr>

            <tr class="cvm_search_results">
                <th valign="top" scope="row">
					<?php endif;?>

					<?php if( isset( $metabox ) ):?>
                    <span class="cvm_search_results">
			<?php endif;?>

                        <label for="cvm_search_results"><?php _e( 'Search results','cvm_video' );?> :</label>

						<?php if( !isset( $metabox ) ):?>
                </th>
                <td>
					<?php endif;?>

                    <input type="text" name="cvm_search_results" id="cvm_search_results" value="<?php echo  isset( $_GET['cvm_search_results'] ) ? esc_attr( $_GET['cvm_search_results'] ) : '';?>" placeholder="<?php _e('enter optional search query', 'cvm_video');?>" size="25">
					<?php if( isset( $metabox ) ):?>
                        </span>
					<?php endif;?>

					<?php if( !isset( $metabox ) ):?>
                </td>
            </tr>
			<?php endif;?>
		
			<!-- 
			<tr>
				<td valign="top"><label for=""></label></td>
				<td></td>
			</tr>
			-->	
		
		<?php if( !isset($metabox) ):?>					
		</table>
		<?php endif;?>
		<?php wp_nonce_field('cvm-video-import', 'cvm_search_nonce', false);?>
		<?php
			$type = isset( $metabox ) ? 'secondary' : 'primary'; 
			submit_button( __('Load feed', 'codeflavors-vimeo-video-post-lite'), $type, 'submit', !isset($metabox) );
		?>
	</form>