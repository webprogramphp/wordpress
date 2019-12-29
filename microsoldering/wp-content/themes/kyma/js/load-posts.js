/*!
 * kyma-load-posts.js
 *
 * Load more post js in custom home page
 *
 * @package Kyma
 */
jQuery(document).ready(function ($) {
    /*----------------------------------------------------*/
    /*  Ajax Load More Posts
     /*----------------------------------------------------*/
    var $content = $('.ajax_posts');
    var $loader = $('#load-button > a');
    var cat = $loader.data('category');
    var ppp = load_more_posts_variable.ppp;
    $content.imagesLoaded(function(){
        $content.masonry({
            itemSelector: '.grid-item',
        });
    });
    $loader.on( 'click', load_ajax_posts );
    function load_ajax_posts() {
        if (!($loader.hasClass('loading') || $loader.hasClass('post_no_more_posts'))) {
            var offset = $('.masonry').find('.grid-item').length;
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: load_more_posts_variable.ajaxurl,
                data: {
                    'ppp': ppp,
                    'offset': offset,
                    'action': 'kyma_more_post_ajax'
                },
                beforeSend : function () {
                    $loader.button('loading');
                },
                success: function (data) {
                    var $data = $(data);
                    if ($data.length) {
                        var $newElements = $data.addClass('animated visible');
                        $content.append($newElements).each(function(){
                            $content.imagesLoaded(function(){
                            $content.masonry({
                                itemSelector: '.grid-item',
                            });
                        });
                            $content.masonry('reloadItems');
                        });
                        $newElements.animate({ opacity: 1 });
                        $content.masonry();
                        $loader.button('reset');
                    } else {
                        $loader.removeClass('loading').addClass('post_no_more_posts').html(load_more_posts_variable.noposts);
                    }
                },
                error : function (jqXHR, textStatus, errorThrown) {
                    $loader.html($.parseJSON(jqXHR.responseText) + ' :: ' + textStatus + ' :: ' + errorThrown);
                    console.log(jqXHR);
                },
            });
        }
        offset += ppp;
        return false;
    }
});