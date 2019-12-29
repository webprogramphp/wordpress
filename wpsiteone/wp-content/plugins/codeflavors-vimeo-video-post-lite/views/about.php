<div class="wrap about-wrap">
    <h1><?php printf( __( 'Welcome to Vimeotheque Lite %s', 'codeflavors-vimeo-video-post-lite' ), CVM_VERSION ); ?></h1>
    <p class="about-text"><?php printf( __( 'Thank you for installing Vimeotheque Lite %s, the plugin that gives you the possibility to create WordPress posts from Vimeo searches, channels, user uploads or albums.', 'codeflavors-vimeo-video-post-lite' ), CVM_VERSION ); ?></p>

    <div class="changelog point-releases">
        <h3><?php _e( 'Maintenance Release' ); ?></h3>
        <p>
			<?php
			/* translators: %s: Codex URL */
			printf( __( 'For more information, see <a href="%s" target="_blank">the changelog</a>.', 'codeflavors-vimeo-video-post-lite' ), 'https://wordpress.org/plugins/codeflavors-vimeo-video-post-lite/#developers' );
			?>
        </p>
    </div>

    <div class="feature-section one-col">
        <div class="col">
            <h2><?php _e( 'Initial plugin configuration' ); ?></h2>
            <p class="lead-description"><?php _e( 'See how to set up Vimeotheque Lite and start importing your Vimeo videos!', 'codeflavors-vimeo-video-post-lite' ); ?></p>
            <p>
                <?php printf(
                        __( 'First step in setting up the plugin is to create a Vimeo App and set the OAuth credentials in plugin %sSettings%s page.', 'codeflavors-vimeo-video-post-lite' ),
                        '<a href="' . menu_page_url( 'cvm_settings', false ) . '">',
                        '</a>'
                );?>
                <?php printf(
                        __( 'To make the process easier we have prepared a detailed tutorial on how to %sset OAuth credentials%s and start importing Vimeo videos.', 'codeflavors-vimeo-video-post-lite' ),
                        '<a href="' . cvm_docs_link( 'getting-started/vimeo-oauth-new-interface/' ) . '" target="_blank">',
                        '</a>'
                );?>
            </p>

        </div>
    </div>

    <hr />

    <div class="changelog">
        <h2><?php _e( "What's new?", 'codeflavors-vimeo-video-post-lite' );?></h2>

        <div class="under-the-hood two-col">
            <div class="col">
                <h3><?php _e( 'Additional video details available for bulk imports', 'codeflavors-vimeo-video-post-lite' ); ?></h3>
                <p><?php
					_e( 'In addition to video title, description and video embed, you now have the option to import video publish date and one video tag.', 'codeflavors-vimeo-video-post-lite' );
                    ?></p>
            </div>
            <div class="col">
                <h3><?php _e( 'Extra embedding options', 'codeflavors-vimeo-video-post-lite' ); ?></h3>
                <p><?php _e( 'We&#8217;ve introduced two more video embedding options: one that allow embedding directly by iframe, without involving the video embedding script that comes with the plugin and the option to override the player size and use the actual video size returned by Vimeo API.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
            </div>
            <div class="col">
                <h3><?php _e( 'Video image import', 'codeflavors-vimeo-video-post-lite' ); ?></h3>
                <p><?php _e( 'For each video post created by the plugin you now have the option to import the video image as post featured image.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
            </div>
            <div class="col">
                <h3><?php _e( 'New embed size ratio', 'codeflavors-vimeo-video-post-lite' ); ?></h3>
                <p><?php _e( 'In addition to 16:9 and 4:3 size ratios we added 23.5:1 as an option so that your wide videos can be displayed properly without any black bars.', 'codeflavors-vimeo-video-post-lite' ); ?></p>
            </div>
        </div>
    </div>

    <hr />

    <div class="return-to-dashboard">
        <a href="<?php menu_page_url( 'cvm_settings' ); ?>#cvm-settings-auth-options"><?php _e( 'Go to plugin Settings', 'codeflavors-vimeo-video-post-lite' ); ?></a>
        |
        <a href="<?php echo cvm_link( 'documents/getting-started' ) ?>"
           target="_blank"><?php _e( 'Online documentation', 'codeflavors-vimeo-video-post-lite' ); ?></a>
    </div>
</div>