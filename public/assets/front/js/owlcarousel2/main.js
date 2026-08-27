$(".owl-carousel").owlCarousel({
    loop:true,
    margin:10,
    nav:true,
    dots:false,
    dotsEach:true,
    navText:[
        '<img src="/img/arrow-left1.png" alt="arrow-left">',
        '<img src="/img/arrow-right1.png" alt="arrow-right">'
    ],
    responsive:{
        0:{
            items:3,
            nav:false
        },
        768:{
            items:5,
            dots:true,
            nav:false
        },
        1000:{
            items:8,
            nav:true
        }
    }
});
