<?php
/*
 * Main Sidebar File.
 */
?>
<div class="col-md-3 col-sm-4 col-xs-12">	
 <div class="sidebar a1-sidebar">
   <?php if ( is_active_sidebar( 'sidebar-1' ) ) : dynamic_sidebar( 'sidebar-1' ); endif; ?>
 </div> 
</div>