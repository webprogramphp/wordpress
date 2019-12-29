<?php
/**
 * Footer For a1 Theme.
 */
global $a1_options; $widget_flag=false;?>
<div class="clearfix"></div>
<!--footer start-->
<footer>
    <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3') || is_active_sidebar('footer-4')) { $widget_flag=true;?>
        <div class="col-md-12 footer-top no-padding-lr">
            <div class="container a1-container a1-main-sidebar">
                <div class="row">
                    <div class="col-md-3 col-sm-6  footer-column">
                        <?php
                        if (is_active_sidebar('footer-1')) {
                            dynamic_sidebar('footer-1');
                        } ?>
                    </div>
                    <div class="col-md-3 col-sm-6  footer-column">
                        <?php
                        if (is_active_sidebar('footer-2')) {
                            dynamic_sidebar('footer-2');
                        } ?>
                    </div>
                    <div class="col-md-3 col-sm-6  footer-column">
                        <?php
                        if (is_active_sidebar('footer-3')) {
                            dynamic_sidebar('footer-3');
                        } ?>
                    </div>
                    <div class="col-md-3 col-sm-6  footer-column">
                        <?php
                        if (is_active_sidebar('footer-4')) {
                            dynamic_sidebar('footer-4');
                        } ?>
                    </div>
                </div>
            </div>
        </div>
<?php } ?>
    <div class="footer-botom <?php echo ($widget_flag)?'':'footer-botom-top'; ?>">
        <div class="container a1-container">
            <div class="row">
                <?php
                if(get_theme_mod('footer-content',$a1_options['footer-content'])) {                            
                    echo "<p class='container a1-container footer-content'>" . esc_attr(get_theme_mod('footer-content',$a1_options['footer-content'])) . "</p>";
                } ?>
                <div class="col-md-6 col-sm-6 copyright-text">
                    <p><?php                        
                        if(get_theme_mod('footertext',$a1_options['footertext'])) {
                            echo wp_kses_post(get_theme_mod('footertext',$a1_options['footertext']));
                        }
                        esc_html_e('', 'a1');
                        echo ' <a target="_blank" href="'.esc_url('https://consultingservices.com/').'">';
                        esc_html_e('&copy; All Rights Reserved', '');
                        echo '</a>  '; ?>
                    </p>
                </div>
                <div class="col-md-6 col-sm-6 footer-menu">
                    <?php wp_nav_menu(array('theme_location' => 'secondary', 'fallback_cb' => false)); ?>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--footer end--> 
<?php wp_footer(); ?>
</body>
</html>