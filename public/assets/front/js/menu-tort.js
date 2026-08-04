$(document).ready(function() {
	$('.menu-burger__header').click(function(){
        $('.menu-burger__header').toggleClass('open-menu');
        $('.header__nav').toggleClass('open-menu');
        $('body').toggleClass('fixed-page');
        $('.container').toggleClass('opacity-container-mob');
     
	});
    });

    
    
    
$(function() {
    $(document).on("click", ".mobile_menu_container .parent", function(e) {
        e.preventDefault(), $(".mobile_menu_container .activity").removeClass("activity"), $(this).siblings("ul").addClass("loaded").addClass("activity"), $("body").addClass("lock")
    }), $(document).on("click", ".mobile_menu_container .back", function(e) {
        e.preventDefault(), $(".mobile_menu_container .activity").removeClass("activity"), $(this).parent().parent().removeClass("loaded"), $(this).parent().parent().parent().parent().addClass("activity"), $(body).toggleClass('lock'), $("body").addClass("lock")
    }), $(document).on("click", ".mobile_menu", function(e) {
        e.preventDefault(), $(".mobile_menu_container").addClass("loaded"), $(".mobile_menu_overlay").fadeIn(), $("body").addClass("lock")
    }), $(document).on("click", ".mobile_menu_overlay", function(e) {
        $(".mobile_menu_container").removeClass("loaded"), $(this).fadeOut(function() {
            $(".mobile_menu_container .loaded").removeClass("loaded"), $(".mobile_menu_container .activity").removeClass("activity"), $("body").addClass("lock")
            
            
           
           
            
            
            
            
        })
    })
});

$("body").addClass("lock");
