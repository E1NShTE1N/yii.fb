$(document).ready(function(){

    $spinner1 = $('#spinner1');

    $(document).on('change', '#FilterForm_discipline', function() {

        var $form  = $(this).closest('form');
        var formId = $form.attr('id');
        var url    = $form.attr('action');

        $spinner1.show();

        $.get(url, $form.serialize(), function(data){

            var html = $('#'+formId, data).html();
            $('div', $form).replaceWith(html);
            initChosen();
            $spinner1.hide();
        })
    });

    $(document).on('change', '#FilterForm_group', function() {
        $(this).closest('form').submit();
    });

});