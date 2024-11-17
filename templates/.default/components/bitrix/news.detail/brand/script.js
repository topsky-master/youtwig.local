function bindFilterLeft() {
    if ($(window).width() < 767) {
        $('.filter-parameters-content').css('display', 'none').addClass('filter-collapsed');
        $('.filter-title').addClass('filter-collapsed');
    }

    $('.filter-title').bind("click", function() {
        var oContainer = $(this).next('div');
        switch (oContainer.css('display')) {
            case 'none':
                $(this).removeClass('filter-collapsed');
                oContainer.css('display', 'block');
                break;
            default:
                $(this).addClass('filter-collapsed');
                oContainer.css('display', 'none');
                break;
        }
    });

    $('.filter-parameters-content > p').bind("click", function() {
        if ($(this).parent().hasClass('filter-expanded')) {
            $(this).parent().removeClass('filter-expanded');
        } else {
            $(this).parent().addClass('filter-expanded');
        }
    });
}

$(function() {
    bindFilterLeft();

    galleryTop = new Swiper(".slider .gallery-top", {
        direction: "horizontal",
        spaceBetween: 0,
        pagination: {
            el: '.swiper-pagination',
            type: 'progressbar'
        },
        keyboard: {
            enabled: true
        }
    });
});
