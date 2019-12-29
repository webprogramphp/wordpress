<div class="wrap about-wrap">
	<h1>
		<?php _e('Automatic import - PRO feature', 'codeflavors-vimeo-video-post-lite')?>
	</h1>
	<p class="about-text" style="margin-left: 0; margin-right: 0;">
		<?php _e('PRO feature that allows automatic creation of video posts from Vimeo channels, albums, categories, groups or user uploads.', 'codeflavors-vimeo-video-post-lite');?>
	</p>
    <hr />
	<script language="javascript">
        ;(function($){
            $(document).ready(function(){
                $('#cvm-video-preview').Vimeo_VideoPlayer({
                    'video_id' 	: '223879840#t=54',
                    'source'	: 'vimeo',
                    'play'       : false,
                    'volume'    : 50
                }).pause();
            })
        })(jQuery);
	</script>

    <div class="class="feature-section one-col">
        <div class="col">
            <h2><?php _e( 'An easier way to create WordPress posts from your Vimeo videos!', 'codeflavors-vimeo-video-post-lite' ) ;?></h2>
            <div id="cvm-video-preview" style="width: 90%; max-width: 90%; margin: 0 auto;">%nbsp;</div>
        </div>
    </div>

    <div class="feature-section one-col">
        <div class="col">
            <hr />
            <h2><?php _e( 'Main features of Automatic Import', 'codeflavors-vimeo-video-post-lite' ) ;?></h2>
            <ul>
                <li>- <?php _e( 'Customizable time interval for importing that ranges from 1 minute to 24 hours;', 'codeflavors-vimeo-video-post-lite' ) ;?></li>
                <li>- <?php _e( "Customizable import rules that can skip or import as draft/pending the videos that can't be embedded into your website.", 'codeflavors-vimeo-video-post-lite' ) ;?></li>
                <li>- <?php _e( 'No restriction on the number of channels, albums, user uploads or groups that can be imported using Automatic Importing.', 'codeflavors-vimeo-video-post-lite' ) ;?></li>
                <li>- <?php _e( 'Each Automatic Import can be assigned to any WordPress category/taxonomy and tags.', 'codeflavors-vimeo-video-post-lite' ) ;?></li>
                <li>- <?php _e( 'Import as regular post type "post" or as custom post type "vimeo-video" (implemented by the plugin).', 'codeflavors-vimeo-video-post-lite' ) ;?></li>
                <li>- <?php _e( 'Automatically import all video content, including featured image.', 'codeflavors-vimeo-video-post-lite' ) ;?></li>
            </ul>
        </div>
    </div>
    <hr />
    <div class="return-to-dashboard">
        <a href="<?php echo cvm_link(''); ?>">Vimeotheque PRO</a> |
        <a href="<?php echo cvm_docs_link( 'basic-tutorials/automatic-import/' ) ;?>"><?php _e( 'Automatic Import explained', 'codeflavors-vimeo-video-post-lite' );?></a> |
        <a href="<?php echo cvm_docs_link( 'plugin-options/import-options/' ) ;?>"><?php _e( 'Import options', 'codeflavors-vimeo-video-post-lite' );?></a>
    </div>
</div>