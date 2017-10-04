jQuery(document).ready(function ($) {
      $(document).on("click", ".upload_image_button", function (e) {
         e.preventDefault();
         var $button = $(this);
    
    
         var file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Selecione ou faça o upload de uma imagem',
            library: { 
               type: 'image'
            },
            button: {
               text: 'Selecionar'
            },
            multiple: false
         });
    
         file_frame.on('select', function () {

            var attachment = file_frame.state().get('selection').first().toJSON();
    
            $button.siblings('img').attr('src', attachment.url);  
            $button.siblings('input').val(attachment.url);  
            $button.siblings('input').change();    
         });
    
         
         file_frame.open();
      });

      $(document).on('change', '#tipo', function() {
            $(this).siblings('#' + $(this).val() + '-link').removeClass('hidden');
            $(this).siblings('select:not(#' + $(this).val() + '-link)').addClass('hidden').val('false');
      });
   });