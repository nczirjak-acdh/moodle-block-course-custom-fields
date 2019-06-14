 require(['jquery'], function($) {
    $(document).ready(function(){ 

        /* course custom  */
        $(".show_hide_ccf_block").click(function(e){
            var height = $(".block_course_custom_fields").css("height");
            if(height > "200px") {
                $(".block_course_custom_fields").css("height", "200px");
                $(".show_hide_ccf_block").text('Show All');
                e.preventDefault();
            }else {
                $(".block_course_custom_fields").css("height", "100%");
                $(".show_hide_ccf_block").text('Hide All');
                e.preventDefault();
            }
            e.preventDefault();
        });
    });
});   