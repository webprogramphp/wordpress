<div class="wrap">
	<div class="icon32" id="icon-options-general"><br></div>
	<h2><?php _e('Videos - Plugin settings', 'codeflavors-vimeo-video-post-lite');?></h2>
	<?php if( isset( $authorize_error ) ):?>
	<div class="error">
		<p><?php echo $authorize_error;?></p>
	</div>
	<?php endif;?>
	<div id="cvm_tabs">	
		<form method="post" action="">
			<?php wp_nonce_field('cvm-save-plugin-settings', 'cvm_wp_nonce');?>
			<ul class="cvm-tab-labels">
				<li><a href="#cvm-settings-post-options"><i class="dashicons dashicons-arrow-right"></i> <?php _e('Post options', 'codeflavors-vimeo-video-post-lite') ?></a></li>
				<li><a href="#cvm-settings-content-options"><i class="dashicons dashicons-arrow-right"></i> <?php _e('Content options', 'codeflavors-vimeo-video-post-lite') ?></a></li>
                <li class="cvm-pro-option hide-if-js"><a href="#cvm-settings-image-options"><i class="dashicons dashicons-arrow-right"></i> <?php _e( 'Image options', 'codeflavors-vimeo-video-post-lite' ) ?>
                <li><a href="#cvm-settings-import-options"><i class="dashicons dashicons-arrow-right"></i> <?php _e( 'Import options', 'codeflavors-vimeo-video-post-lite' ) ?>
				<li><a href="#cvm-settings-embed-options"><i class="dashicons dashicons-arrow-right"></i> <?php _e('Embed options', 'codeflavors-vimeo-video-post-lite') ?></a></li>
				<li><a href="#cvm-settings-auth-options"><i class="dashicons dashicons-arrow-right"></i> <?php _e('API access', 'codeflavors-vimeo-video-post-lite') ?></a></li>
			</ul>
			
			<!-- Post options tab -->
			<div id="cvm-settings-post-options">
				<table class="form-table">
				<tbody>
					<tr>
                        <th colspan="2">
                            <h4>
                                <i class="dashicons dashicons-admin-tools"></i> <?php _e('General settings', 'codeflavors-vimeo-video-post-lite');?>
                                <a class="button cvm-pro-options-trigger" href="#"
                                   class="button"
                                   data-visible="0"
                                   data-text_on="<?php esc_attr_e( 'Hide PRO options', 'codeflavors-vimeo-video-post-lite' ) ;?>"
                                   data-text_off="<?php esc_attr_e( 'Show PRO options', 'codeflavors-vimeo-video-post-lite' );?>"
                                   data-selector=".cvm-pro-option"><?php _e( 'Show PRO options', 'codeflavors-vimeo-video-post-lite' );?></a>
                            </h4>
                        </th>
                    </tr>

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label
                                    for="post_type_post"><?php _e( 'Import as regular post type (aka post)', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="checkbox" name="" value="1" id="" disabled="disabled" />
                            <span class="description">
								<?php _e( 'Videos will be imported as <strong>regular posts</strong> instead of custom post type video. Posts having attached videos will display having the same player options as video post types.', 'codeflavors-vimeo-video-post-lite' ); ?>
								</span>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    <tr valign="top">
                        <th scope="row"><label
                                    for="archives"><?php _e( 'Embed videos in archive pages', 'codeflavors-vimeo-video-post-lite' ) ?>:</label>
                        </th>
                        <td>
                            <input type="checkbox" name="archives" value="1"
                                   id="archives"<?php cvm_check( $options['archives'] ); ?> />
                            <span class="description">
									<?php _e( 'When checked, videos will be visible on all pages displaying lists of video posts.', 'codeflavors-vimeo-video-post-lite' ); ?>
								</span>
                        </td>
                    </tr>

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label
                                    for="use_microdata"><?php _e( 'Include microdata on video pages', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="checkbox" name="" value="1" id="" disabled="disabled" />
                            <span class="description">
									<?php _e( 'When checked, all pages displaying videos will also include microdata for SEO purposes ( more on <a href="http://schema.org" target="_blank">http://schema.org</a> ).', 'codeflavors-vimeo-video-post-lite' ); ?>
								</span>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    <!-- Visibility -->
                    <tr>
                        <th colspan="2">
                            <h4>
                                <i class="dashicons dashicons-video-alt3"></i> <?php _e( 'Video post type options', 'codeflavors-vimeo-video-post-lite' ); ?>
                                <a class="button cvm-pro-options-trigger" href="#"
                                   class="button"
                                   data-visible="0"
                                   data-text_on="<?php esc_attr_e( 'Hide PRO options', 'codeflavors-vimeo-video-post-lite' ) ;?>"
                                   data-text_off="<?php esc_attr_e( 'Show PRO options', 'codeflavors-vimeo-video-post-lite' );?>"
                                   data-selector=".cvm-pro-option"><?php _e( 'Show PRO options', 'codeflavors-vimeo-video-post-lite' );?></a>
                            </h4>
                        </th>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="public"><?php _e( 'Video post type is public', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="checkbox" name="public" value="1"
                                   id="public"<?php cvm_check( $options['public'] ); ?> />
                            <span class="description">
								<?php if ( ! $options['public'] ): ?>
                                    <span style="color:red;"><?php _e( 'Videos cannot be displayed in front-end. You can only incorporate them in playlists or display them in regular posts using shortcodes.', 'codeflavors-vimeo-video-post-lite' ); ?></span>
								<?php else: ?>
									<?php _e( 'Videos will display in front-end as post type video are and can also be incorporated in playlists or displayed in regular posts.', 'codeflavors-vimeo-video-post-lite' ); ?>
								<?php endif; ?>
								</span>
                        </td>
                    </tr>

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label
                                    for="homepage"><?php _e( 'Include videos post type on homepage', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="checkbox" name="" value="1" id="" disabled="disabled" />
                            <span class="description">
									<?php _e( 'When checked, if your homepage displays a list of regular posts, videos will be included among them.', 'codeflavors-vimeo-video-post-lite' ); ?>
								</span>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label
                                    for="main_rss"><?php _e( 'Include videos post type in main RSS feed', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="checkbox" name="" value="1" id="" disabled="disabled" />
                            <span class="description">
									<?php _e( 'When checked, custom post type will be included in your main RSS feed.', 'codeflavors-vimeo-video-post-lite' ); ?>
								</span>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    <!-- PRO option -->
                    <tr class="cvm-pro-option hide-if-js">
                        <th colspan="2">
                            <h4>
                                <i class="dashicons dashicons-admin-links"></i> <?php _e( 'Video post type rewrite (pretty links)', 'codeflavors-vimeo-video-post-lite' ); ?>
                            </h4>
                            <p class="description">
								<?php _e( "Please make sure that you don't insert the same slug twice.", 'codeflavors-vimeo-video-post-lite' ); ?>
                            </p>
                        </th>
                    </tr>
                    <!-- /PRO option -->
                    <?php global $CVM_POST_TYPE;?>
                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label for="post_slug"><?php _e( 'Post slug', 'codeflavors-vimeo-video-post-lite' ) ?>:</label></th>
                        <td>
                            <input type="text" id="" name="" readonly="readonly"
                                   value="<?php echo $CVM_POST_TYPE->get_post_type() ;?>"/>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label for="taxonomy_slug"><?php _e( 'Taxonomy slug', 'codeflavors-vimeo-video-post-lite' ) ?> :</label>
                        </th>
                        <td>
                            <input type="text" id="" name="" readonly="readonly"
                                   value="<?php echo $CVM_POST_TYPE->get_post_tax(); ?>"/>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label for="tag_slug"><?php _e( 'Tags slug', 'codeflavors-vimeo-video-post-lite' ) ?> :</label></th>
                        <td>
                            <input type="text" id="" name="" readonly="readonly"
                                   value="<?php echo $CVM_POST_TYPE->get_post_tax(); ?>"/>
                        </td>
                    </tr>
                    <!-- PRO option -->

				</tbody>
				</table>
				<?php submit_button(__('Save settings', 'codeflavors-vimeo-video-post-lite'));?>
			</div>
			<!-- /Post options tab -->
			
			<!-- Content options tab -->
			<div id="cvm-settings-content-options" class="hide-if-js">
				<table class="form-table">
				<tbody>
					<tr>
                        <th colspan="2">
                            <h4>
                                <i class="dashicons dashicons-admin-post"></i> <?php _e('Post content settings', 'codeflavors-vimeo-video-post-lite');?>
                                <a class="button cvm-pro-options-trigger" href="#"
                                   class="button"
                                   data-visible="0"
                                   data-text_on="<?php esc_attr_e( 'Hide PRO options', 'codeflavors-vimeo-video-post-lite' ) ;?>"
                                   data-text_off="<?php esc_attr_e( 'Show PRO options', 'codeflavors-vimeo-video-post-lite' );?>"
                                   data-selector=".cvm-pro-option"><?php _e( 'Show PRO options', 'codeflavors-vimeo-video-post-lite' );?></a>
                            </h4>
                        </th>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="import_date"><?php _e( 'Import date', 'codeflavors-vimeo-video-post-lite' ) ?>:</label></th>
                        <td>
                            <input type="checkbox" value="1" name="import_date"
                                   id="import_date"<?php cvm_check( $options['import_date'] ); ?> />
                            <span class="description"><?php _e( "Imports will have Vimeo's publishing date.", 'codeflavors-vimeo-video-post-lite' ); ?></span>
                        </td>
                    </tr>

                    <tr valign="top">
						<th scope="row"><label for="import_title"><?php _e('Import titles', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
						<td>
							<input type="checkbox" value="1" id="import_title" name="import_title"<?php cvm_check($options['import_title']);?> />
							<span class="description"><?php _e('Automatically import video titles from feeds as post title.', 'codeflavors-vimeo-video-post-lite');?></span>
						</td>
					</tr>

                    <tr valign="top">
                        <th scope="row"><label for="import_tags"><?php _e( 'Import tag', 'codeflavors-vimeo-video-post-lite' ) ?>:</label></th>
                        <td>
                            <input type="checkbox" value="1" id="import_tags"
                                   name="import_tags"<?php cvm_check( $options['import_tags'] ); ?> />
                            <span class="description">
                                <?php _e( 'Automatically import first video tag as post tag from feeds.', 'codeflavors-vimeo-video-post-lite' ); ?>
                                <?php printf(
                                        __( 'Please note that multiple tags importing is a %sPRO%s feature.', 'codeflavors-vimeo-video-post-lite' ),
                                        '<a href="' . cvm_link('') . '" target="_blank">',
                                        '</a>');
                                ?>
                            </span>
                        </td>
                    </tr>

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label for="max_tags"><?php _e( 'Number of tags', 'codeflavors-vimeo-video-post-lite' ) ?>:</label></th>
                        <td>
                            <input type="text" value="5" id="" name="" size="1" readonly="readonly" />
                            <span class="description"><?php _e( 'Maximum number of tags that will be imported. PRO version allows importing of multiple tags.', 'codeflavors-vimeo-video-post-lite' ); ?></span>
                        </td>
                    </tr>
                    <!-- /PRO option -->

					<tr valign="top">
						<th scope="row"><label for="import_description"><?php _e('Import descriptions as', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
						<td>
							<?php 
								$args = array(
									'options' => array(
										'content' 			=> __('post content', 'codeflavors-vimeo-video-post-lite'),
										'excerpt' 			=> __('post excerpt', 'codeflavors-vimeo-video-post-lite'),
										'content_excerpt' 	=> __('post content and excerpt', 'codeflavors-vimeo-video-post-lite'),
										'none'				=> __('do not import', 'codeflavors-vimeo-video-post-lite')
									),
									'name' => 'import_description',
									'selected' => $options['import_description']								
								);
								cvm_select($args);
							?>
							<p class="description"><?php _e('Import video description from feeds as post description, excerpt or none.')?></p>
						</td>
					</tr>

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label
                                    for="prevent_autoembed"><?php _e( 'Prevent auto embed on video content', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="checkbox" value="1" name="" id="" disabled="disabled" />
                            <span class="description">
									<?php _e( 'If content retrieved from Vimeo has links to other videos, checking this option will prevent auto embedding of videos in your post content.', 'codeflavors-vimeo-video-post-lite' ); ?>
								</span>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label
                                    for="make_clickable"><?php _e( "Make URL's in video content clickable", 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="checkbox" value="1" name="" id="" disabled="disabled" />
                            <span class="description">
									<?php _e( "Automatically make all valid URL's from content retrieved from Vimeo clickable.", 'codeflavors-vimeo-video-post-lite' ); ?>
								</span>
                        </td>
                    </tr>
                    <!-- /PRO option -->

				</tbody>
				</table>
				<?php submit_button(__('Save settings', 'codeflavors-vimeo-video-post-lite'));?>
			</div>
			<!-- /Content options tab -->

            <!-- Tab image options -->
            <div id="cvm-settings-image-options" class="hide-if-js">
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th colspan="2"><h4><i class="dashicons dashicons-format-image"></i> <?php _e( 'Image settings', 'codeflavors-vimeo-video-post-lite' ); ?>
                            </h4></th>
                    </tr>

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label for="featured_image"><?php _e( 'Import images', 'codeflavors-vimeo-video-post-lite' ) ?>:</label>
                        </th>
                        <td>
                            <input type="checkbox" value="1" name="" id="" disabled="disabled" />
                            <span class="description"><?php _e( "Vimeo video thumbnail will be set as post featured image.", 'codeflavors-vimeo-video-post-lite' ); ?></span>
                        </td>
                    </tr>
                    <!-- PRO option -->

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label
                                    for="image_on_demand"><?php _e( 'Import featured image on request', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="checkbox" value="1" name="" id="" disabled="disabled" />
                            <span class="description"><?php _e( "Vimeo video thumbnail will be imported only when featured images needs to be displayed (ie. a post created by the plugin is displayed).", 'codeflavors-vimeo-video-post-lite' ); ?></span>
                        </td>
                    </tr>
                    <!-- PRO option -->

                    </tbody>
                </table>
				<?php submit_button( __( 'Save settings', 'codeflavors-vimeo-video-post-lite' ) ); ?>
            </div>
            <!-- /Tab image options -->

            <!-- Tab import options -->
            <div id="cvm-settings-import-options">
                <table class="form-table">
                    <tbody>
                    <!-- Manual Import settings -->
                    <tr>
                        <th colspan="2">
                            <h4>
                                <i class="dashicons dashicons-download"></i> <?php _e( 'Bulk Import settings', 'codeflavors-vimeo-video-post-lite' ); ?>
                                <a class="button cvm-pro-options-trigger" href="#"
                                   class="button"
                                   data-visible="0"
                                   data-text_on="<?php esc_attr_e( 'Hide PRO options', 'codeflavors-vimeo-video-post-lite' ) ;?>"
                                   data-text_off="<?php esc_attr_e( 'Show PRO options', 'codeflavors-vimeo-video-post-lite' );?>"
                                   data-selector=".cvm-pro-option"><?php _e( 'Show PRO options', 'codeflavors-vimeo-video-post-lite' );?></a>
                            </h4>
                        </th>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="import_status"><?php _e('Import status', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
                        <td>
		                    <?php
		                    $args = array(
			                    'options' => array(
				                    'publish' 	=> __('Published', 'codeflavors-vimeo-video-post-lite'),
				                    'draft' 	=> __('Draft', 'codeflavors-vimeo-video-post-lite'),
				                    'pending'	=> __('Pending', 'codeflavors-vimeo-video-post-lite')
			                    ),
			                    'name' 		=> 'import_status',
			                    'selected' 	=> $options['import_status']
		                    );
		                    cvm_select($args);
		                    ?>
                            <p class="description"><?php _e('Bulk imported videos will have this status.', 'codeflavors-vimeo-video-post-lite');?></p>
                        </td>
                    </tr>

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label
                                    for="import_privacy"><?php _e( 'Videos not public will be', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
		                    <?php
		                    $args = array(
			                    'options'  => array(
				                    'pending' => __( 'imported as posts pending review', 'codeflavors-vimeo-video-post-lite' ),
			                        'import'  => __( 'imported following the import rules', 'codeflavors-vimeo-video-post-lite' ),
				                    'skip'    => __( 'skipped from importing', 'codeflavors-vimeo-video-post-lite' )
			                    ),
			                    'name'     => '',
			                    'selected' => false,
                                'disabled' => true
		                    );
		                    cvm_select( $args );
		                    ?>
                            <p class="description"><?php _e( 'If a video is not set as public by its owner (password protected videos for example), it will obey this rule.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label for="import_frequency"><?php _e( 'Automatic import', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
		                    <?php _e( 'Import ', 'codeflavors-vimeo-video-post-lite' ); ?>
		                    <?php printf( __( '%d videos', 'codeflavors-vimeo-video-post-lite' ), 20 ); ?>
		                    <?php _e( 'every', 'codeflavors-vimeo-video-post-lite' ); ?>
		                    <?php
		                    $args = array(
			                    'options'  => array(
			                            '5' => __( '5 minutes', 'codeflavors-vimeo-video-post-lite' )
                                ),
			                    'name'     => '',
			                    'selected' => '',
                                'disabled' => true
		                    );
		                    cvm_select( $args );
		                    ?>
                            <p class="description"><?php _e( 'How often should Vimeo be queried for playlist updates.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    <!-- PRO option -->
                    <tr valign="top" class="cvm-pro-option hide-if-js">
                        <th scope="row"><label
                                    for="automatic_import_uses"><?php _e( 'Automatic import runs by', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="radio" name="" id=""
                                   value="" checked="checked" disabled="disabled" />
                            <label for="wp_cron"><?php _e( 'WordPress internal CRON JOB system. Videos will be imported at the given interval everytime a user visits your website.', 'codeflavors-vimeo-video-post-lite' ); ?></label><br/>
                            <input type="radio" name="" id=""
                                   value="" disabled="disabled" />
                            <label for="server_cron"><?php _e( 'My own SERVER CRON job (you need to set up a cron job on your server to open a specific address. ', 'codeflavors-vimeo-video-post-lite' ); ?></label>
                            <p class="description"><?php _e( 'If you select to make automatic imports by SERVER CRON JOB, the same delay as the one set under Automatic import will apply. <br />The difference is that if your website has low traffic, imports will still be made as oposed to WP cron job.<br /> SERVER CRON JOB frequency should be set having the same time delay as the one entered under Automatic Import.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
                        </td>
                    </tr>
                    <!-- /PRO option -->

                    </tbody>
                </table>
				<?php submit_button( __( 'Save settings', 'codeflavors-vimeo-video-post-lite' ) ); ?>
            </div>
            <!-- /Tab import options -->

			<!-- Embed options tab -->
			<div id="cvm-settings-embed-options" class="hide-if-js">
				<table class="form-table cvm-player-settings-options">
				<tbody>
                    <tr>
                        <th colspan="2">
                            <h4>
                                <i class="dashicons dashicons-leftright"></i> <?php _e( 'Player size and position', 'codeflavors-vimeo-video-post-lite' ); ?>
                            </h4>
                            <p class="description"><?php _e( 'General player size settings. These settings will be applied to any new video by default and can be changed individually for every imported video.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
                        </th>
                    </tr>

                    <tr>
                        <th><label for="cvm_js_embed"><?php _e( 'Embed by JavaScript', 'codeflavors-vimeo-video-post-lite' ); ?>:</label></th>
                        <td>
                            <input type="checkbox" value="1" id="cvm_js_embed"
                                   name="js_embed"<?php cvm_check( (bool ) $player_opt['js_embed'] ); ?> />
                            <span class="description"><?php _e( 'When checked, single video embedding will be handled by plugin JavaScript. <br />If unchecked, the video iframe will be placed directly into the page.', 'codeflavors-vimeo-video-post-lite' ); ?></span>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="cvm_aspect_ratio"><?php _e( 'Player size', 'codeflavors-vimeo-video-post-lite' ); ?>:</label></th>
                        <td>
                            <label for="cvm_aspect_ratio"><?php _e( 'Aspect ratio' ); ?> :</label>
                            <?php
                            $args = array(
                                'name'     => 'aspect_ratio',
                                'id'       => 'cvm_aspect_ratio',
                                'class'    => 'cvm_aspect_ratio',
                                'selected' => $player_opt['aspect_ratio']
                            );
                            cvm_aspect_ratio_select( $args );
                            ?>
                            <label for="cvm_width"><?php _e( 'Width', 'codeflavors-vimeo-video-post-lite' ); ?> :</label>
                            <input type="number" name="width" id="cvm_width" class="cvm_width"
                                   value="<?php echo $player_opt['width']; ?>" size="2" placeholder="<?php esc_attr_e( 'Enter the optimal width for your videos.', 'codeflavors-vimeo-video-post-lite' ); ?>" />px
                            | <?php _e( 'Height', 'codeflavors-vimeo-video-post-lite' ); ?> : <span class="cvm_height"
                                                                            id="cvm_calc_height"><?php echo cvm_player_height( $player_opt['aspect_ratio'], $player_opt['width'] ); ?></span>px
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label
                                    for="aspect_override"><?php _e( 'Allow videos to override player size', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="checkbox" value="1" id="aspect_override"
                                   name="aspect_override"<?php cvm_check( (bool ) $player_opt['aspect_override'] ); ?> />
                            <span class="description"><?php _e( 'When checked (recommended), player will have the exact aspect ratio as retrieved from Vimeo. Player size option will be ignored.<br />Applies only to videos imported starting with plugin version 1.2.6.', 'codeflavors-vimeo-video-post-lite' ); ?></span>
                        </td>
                    </tr>
					
					<tr>
						<th><label for="cvm_video_position"><?php _e('Display video in custom post','codeflavors-vimeo-video-post-lite');?>:</label></th>
						<td>
							<?php 
								$args = array(
									'options' => array(
										'above-content' => __('Above post content', 'codeflavors-vimeo-video-post-lite'),
										'below-content' => __('Below post content', 'codeflavors-vimeo-video-post-lite')
									),
									'name' 		=> 'video_position',
									'id'		=> 'cvm_video_position',
									'selected' 	=> $player_opt['video_position']
								);
								cvm_select($args);
							?>
						</td>
					</tr>

                    <tr>
                        <th colspan="2">
                            <h4>
                                <i class="dashicons dashicons-video-alt3"></i> <?php _e( 'Embed settings', 'codeflavors-vimeo-video-post-lite' ); ?>
                            </h4>
                            <p class="description"><?php _e( 'General Vimeo player settings. These settings will be applied to any new video by default and can be changed individually for every imported video.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
                        </th>
                    </tr>

					<tr>
						<th><label for="cvm_volume"><?php _e('Volume', 'codeflavors-vimeo-video-post-lite');?></label>:</th>
						<td>
							<input type="number" name="volume" id="cvm_volume" value="<?php echo $player_opt['volume'];?>" size="1" maxlength="3" min="0" max="100" style="width: 4em;" placeholder="<?php esc_attr_e( 'Video volume', 'codeflavors-vimeo-video-post-lite' );?>" />
							<label for="cvm_volume"><span class="description">( <?php _e('number between 0 (mute) and 100 (max)', 'codeflavors-vimeo-video-post-lite');?> )</span></label>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><label for="title"><?php _e('Show video title', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
						<td><input type="checkbox" value="1" id="title" name="title"<?php cvm_check( (bool )$player_opt['title'] );?> /></td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><label for="byline"><?php _e('Show video uploader', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
						<td><input type="checkbox" value="1" id="byline" name="byline"<?php cvm_check( (bool )$player_opt['byline'] );?> /></td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><label for="portrait"><?php _e('Show video uploader image', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
						<td><input type="checkbox" value="1" id="portrait" name="portrait"<?php cvm_check( (bool )$player_opt['portrait'] );?> /></td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><label for="autoplay"><?php _e('Autoplay', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
						<td><input type="checkbox" value="1" id="autoplay" name="autoplay"<?php cvm_check( (bool )$player_opt['autoplay'] );?> /></td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><label for="fullscreen"><?php _e('Allow fullscreen', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
						<td><input type="checkbox" name="fullscreen" id="fullscreen" value="1"<?php cvm_check( (bool)$player_opt['fullscreen'] );?> /></td>
					</tr>
					
					<tr valign="top">
						<th scope="row"><label for="color"><?php _e('Player color', 'codeflavors-vimeo-video-post-lite')?>:</label></th>
						<td>
							#<input type="text" name="color" id="color" value="<?php echo $player_opt['color'];?>" />
						</td>
					</tr>
				</tbody>
				</table>
				<?php submit_button(__('Save settings', 'codeflavors-vimeo-video-post-lite'));?>
			</div>
			<!-- /Embed options tab -->
			
			<!-- API auth tab -->
			<div id="cvm-settings-auth-options" class="hide-if-js">
				<table class="form-table">
				<tbody>
                <tr>
                    <th colspan="2">
                        <h4>
                            <i class="dashicons dashicons-admin-network"></i> <?php _e( 'Vimeo oAuth keys', 'codeflavors-vimeo-video-post-lite' ); ?>
                        </h4>
	                    <?php if( !$show_auth_message ):?>
                        <p class="description">
							<?php _e( 'In order to be able to make requests to Vimeo API, you must first have a Vimeo account and create the credentials.', 'codeflavors-vimeo-video-post-lite' ); ?>
                            <br/>
							<?php _e( 'To register your App, please visit <a target="_blank" href="https://developer.vimeo.com/apps/new">Vimeo App registration page</a> (requires login to Vimeo).', 'codeflavors-vimeo-video-post-lite' ) ?>
                            <br/>
							<?php printf( __( 'A step by step tutorial on how to create an app on Vimeo can be found %shere%s.', 'codeflavors-vimeo-video-post-lite' ), '<a href="' . cvm_docs_link( 'getting-started/vimeo-oauth-new-interface/' ) . '" target="_blank">', '</a>' ); ?>
                        </p>
                        <hr />
                        <p class="description" style="color: #ff4269;">
                            <?php
                                _e( 'Please remember to add the following URL into your Vimeo App option "App Callback URLs"', 'codeflavors-vimeo-video-post-lite' );
                            ?><br />
                            <input type="text" readonly="readonly" value="<?php echo $vimeo->get_redirect_url();?>" size="100%" onclick="this.select();" />
                        </p>
                        <?php endif;?>
                    </th>
                </tr>
                <?php if ( empty( $options['vimeo_consumer_key'] ) || empty( $options['vimeo_secret_key'] ) ): ?>
                    <tr valign="top">
                        <th scope="row"><label
                                    for="vimeo_consumer_key"><?php _e( 'Enter Vimeo Client Identifier', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="text" name="vimeo_consumer_key" id="vimeo_consumer_key"
                                   value="<?php echo $options['vimeo_consumer_key']; ?>" size="60"/>
                            <p class="description"><?php _e( 'You first need to create a Vimeo Account.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label
                                    for="vimeo_secret_key"><?php _e( 'Enter Vimeo Client Secrets', 'codeflavors-vimeo-video-post-lite' ) ?>
                                :</label></th>
                        <td>
                            <input type="text" name="vimeo_secret_key" id="vimeo_secret_key"
                                   value="<?php echo $options['vimeo_secret_key']; ?>" size="60"/>
                            <p class="description"><?php _e( 'You first need to create a Vimeo Account.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr valign="top">
                        <th scope="row"><label><?php _e( 'Plugin access to Vimeo account', 'codeflavors-vimeo-video-post-lite' ); ?>
                                :</label></th>
                        <td>
                            <p>
				                <?php _e( 'Your Vimeo keys are successfully installed.', 'codeflavors-vimeo-video-post-lite' ); ?>
				                <?php $this->clear_oauth_credentials_link( __( 'Reset credentials', 'codeflavors-vimeo-video-post-lite' ), 'button cvm-danger' ); ?>
                            </p>
                            <p class="description">
				                <?php _e( 'You can now query public videos on Vimeo and import them as WordPress posts.', 'codeflavors-vimeo-video-post-lite' ); ?>
                            </p>
                            <hr/>

			                <?php if ( $show_auth_message ): ?>
                                <p class="description">
					                <?php printf(
					                        __( 'If you want to query and import <strong>private Vimeo videos</strong> owned by you, please note that this feature is available only in %sPRO version%s of the plugin.', 'codeflavors-vimeo-video-post-lite'),
                                            '<a href="' . cvm_link( '' ) . '" target="_blank">',
                                            '</a>'
                                        ); ?>
                                </p>
			                <?php endif; ?>
                        </td>
                    </tr>
	                <?php
	                /**
	                 * Action that allows display of additional OAuth settings
	                 */
	                do_action( 'cvm_additional_oauth_settings_display' ); ?>
                    <?php endif; ?>
				</tbody>
				</table>
				<?php submit_button(__('Save settings', 'codeflavors-vimeo-video-post-lite'));?>
			</div>
			<!-- /API auth tab -->
		</form>
	</div>	
</div>