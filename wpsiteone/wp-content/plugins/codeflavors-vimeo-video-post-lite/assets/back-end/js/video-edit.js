/**
 * 
 */
;(function($){
	$(document).ready(function(){

        $(document).on('change', '.cvm_aspect_ratio', function () {
            var aspect_ratio_input = this,
                parent = $(this).parents('.cvm-player-settings-options'),
                width_input = $(parent).find('.cvm_width'),
                height_output = $(parent).find('.cvm_height');

            var val = $(this).val(),
                w = Math.round(parseInt($(width_input).val())),
                h = 0;
            switch (val) {
                case '4x3':
                    h = Math.floor((w * 3) / 4);
                    break;
                case '16x9':
                    h = Math.floor((w * 9) / 16);
                    break;
                case '2.35x1':
                    h = Math.floor(w / 2.35);
                    break;
            }
            $(height_output).html(h);
        });


        $(document).on('keyup input', '.cvm_width', function () {
            var parent = $(this).parents('.cvm-player-settings-options'),
                aspect_ratio_input = $(parent).find('.cvm_aspect_ratio');

            if ('' == $(this).val()) {
                return;
            }
            var val = Math.round(parseInt($(this).val()));
            $(this).val(val);
            $(aspect_ratio_input).trigger('change');
        });


        // hide options dependant on controls visibility
        $('.cvm_controls').click(function (e) {
            if ($(this).is(':checked')) {
                $('.controls_dependant').show();
            } else {
                $('.controls_dependant').hide();
            }
        })

        // in widgets, show/hide player options if latest videos isn't displayed as playlist
        $(document).on('click', '.cvm-show-as-playlist-widget', function () {
            var parent = $(this).parents('.cvm-player-settings-options'),
                player_opt = $(parent).find('.cvm-recent-videos-playlist-options'),
                list_thumbs = $(parent).find('.cvm-widget-show-vim-thumbs');
            if ($(this).is(':checked')) {
                $(player_opt).show();
                $(list_thumbs).hide();
            } else {
                $(player_opt).hide();
                $(list_thumbs).show();
            }

        })
		
	});
})(jQuery);