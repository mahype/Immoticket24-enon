<script id="op-scroll-to-top-script">

    //Append scroll to top button to body
    var scrollElement = document.createElement('a');
    scrollElement.id = "op-scroll-to-top-element";
    scrollElement.className = "oppp-top";
    scrollElement.setAttribute('href',"javascript:");
    scrollElement.style.backgroundColor = '<?php echo $color; ?>';
    scrollElement.style.borderRadius = '<?php echo $shape?>'

    //Append icon to scroll button
    var scrollIcon = document.createElement('div');
    scrollIcon.id = "op-scroll-to-top-element-icon";
    scrollIcon.style.backgroundImage = 'url(<?php echo $icon ?>)';

    scrollElement.appendChild(scrollIcon);
    var body = document.getElementsByTagName("body");
    body[0].appendChild(scrollElement);

    opjq(document).ready(function($){
        //remove duplicate with same id
        opjq('#op-scroll-to-top-element').each(function (i) {
            $('[id="' + this.id + '"]').slice(1).remove();
        });

        var offset = 50,
            //browser window scroll (in pixels) after which the "back to top" link opacity is reduced
            offset_opacity = 12000,
            //duration of the top scrolling animation (in ms)
            scroll_top_duration = 700,
            //grab the "back to top" link
            $back_to_top = $('.oppp-top');

        //hide or show the "back to top" link
        $(window).scroll(function(){
            ( $(this).scrollTop() > offset ) ? $back_to_top.addClass('oppp-is-visible') : $back_to_top.removeClass('oppp-is-visible oppp-fade-out');
            if( $(this).scrollTop() > offset_opacity ) {
                $back_to_top.addClass('oppp-fade-out');
            }
        });

        //smooth scroll to top
        $back_to_top.on('click', function(event){
            event.preventDefault();
            $('body,html').animate({
                scrollTop: 0 ,
                }, scroll_top_duration
            );
        });

    });
</script>
