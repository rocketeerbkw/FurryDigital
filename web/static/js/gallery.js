$(window).load(function(e) {
    $('div.grid').masonry({
        columnWidth: '.grid-sizer',
        itemSelector: '.grid-item',
        gutter: 10
    });
});