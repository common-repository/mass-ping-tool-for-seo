jQuery(document).ready(function () {
    jQuery('.mpt-alert').on('click', '.closebtn', function () {
        jQuery(this).closest('.mpt-alert').fadeOut(); //.css('display', 'none');
    });
    jQuery('.promotion-container').on('click', 'input', function() {
        jQuery(this).parent().parent().find('.promotion').slideToggle();
    });

    jQuery('.ping-limit').on('click', '#limit', function() {
        jQuery('.limiter').fadeToggle();
    });

    jQuery("#fs_connect button[type=submit]").on("click", function(e) {
        console.log("open verify window")
        window.open('https://better-robots.com/subscribe.php?plugin=mass-ping','mass-ping','resizable,height=400,width=700');
    });


    jQuery(".mpt-accordion").on("click", ".mpt-accordion-heading", function() {

        jQuery(this).toggleClass("mpt-accordion-active").next().slideToggle();

        jQuery(".mpt-accordion-contents").not(jQuery(this).next()).slideUp(300);
                    
        jQuery(this).siblings().removeClass("mpt-accordion-active");
    });

});