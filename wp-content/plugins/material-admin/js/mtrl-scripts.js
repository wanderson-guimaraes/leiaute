/**
 * @Package: WordPress Plugin
 * @Subpackage: Material - White Label WordPress Admin Theme Theme
 * @Since: Mtrl 1.0
 * @WordPress Version: 4.0 or above
 * This file is part of Material - White Label WordPress Admin Theme Theme Plugin.
 */


jQuery(function($) {

    'use strict';

    var MTRL_SETTINGS = window.MTRL_SETTINGS || {};

    
    /******************************
     Menu resizer
     *****************************/
    MTRL_SETTINGS.menuResizer = function() {
        var menuWidth = $("#adminmenuwrap").width();
        if($("#adminmenuwrap").is(":hidden")){
          $("body").addClass("menu-hidden");
          $("body").removeClass("menu-expanded");
          $("body").removeClass("menu-collapsed");
        }
        else if(menuWidth > 60){
          $("body").addClass("menu-expanded");
          $("body").removeClass("menu-hidden");
          $("body").removeClass("menu-collapsed");
        } else {
          $("body").addClass("menu-collapsed");
          $("body").removeClass("menu-expanded");
          $("body").removeClass("menu-hidden");
        }
    };

    MTRL_SETTINGS.menuClickResize = function() {
      $('#collapse-menu, #wp-admin-bar-menu-toggle').click(function(e) {
        var menuWidth = $("#adminmenuwrap").width();
        if($("#adminmenuwrap").is(":hidden")){
          $("body").addClass("menu-hidden");
          $("body").removeClass("menu-expanded");
          $("body").removeClass("menu-collapsed");
        }
        else if(menuWidth > 46){
          $("body").addClass("menu-expanded");
          $("body").removeClass("menu-hidden");
          $("body").removeClass("menu-collapsed");
        } else {
          $("body").addClass("menu-collapsed");
          $("body").removeClass("menu-expanded");
          $("body").removeClass("menu-hidden");
        }
      });
    };

    MTRL_SETTINGS.logoURL = function() {

      $("#adminmenuwrap").prepend("<div class='logo-overlay'></div>");

      $('#adminmenuwrap .logo-overlay').click(function(e) {
        var logourl = $("#mtrl-logourl").attr("data-value");
        if(logourl != ""){
          window.location = logourl;
        }
      });
    };

    MTRL_SETTINGS.iconPanel = function(e) {

      $('.mtrlicon').click(function(e) {
        e.stopPropagation();
        var panel = $(this).parent().find(".mtrliconpanel");
        var iconstr = $(".mtrlicons").html();
        panel.html("");
        panel.append(iconstr);
        panel.show();
      });


    };




    MTRL_SETTINGS.menuToggle = function() {

      $('.mtrltoggle').click(function(e) {

        var id = $(this).parent().attr("data-id");

        if($(this).hasClass("plus")) {
          $(this).removeClass("plus dashicons-plus").addClass("minus dashicons-minus");
          //$(this).html("-");
          $(this).parent().parent().find(".mtrlmenupanel").removeClass("closed").addClass("opened");
        } else if($(this).hasClass("minus")) {
          $(this).removeClass("minus dashicons-minus").addClass("plus dashicons-plus");
          //$(this).html("+");
          $(this).parent().parent().find(".mtrlmenupanel").removeClass("opened").addClass("closed");
        }

      });


      $('.mtrlsubtoggle').click(function(e) {

        var id = $(this).parent().attr("data-id");

        if($(this).hasClass("plus")) {
          $(this).removeClass("plus dashicons-plus").addClass("minus dashicons-minus");
          //$(this).html("-");
          $(this).parent().parent().find(".mtrlsubmenupanel").removeClass("closed").addClass("opened");
        } else if($(this).hasClass("minus")) {
          $(this).removeClass("minus dashicons-minus").addClass("plus dashicons-plus");
          //$(this).html("+");
          $(this).parent().parent().find(".mtrlsubmenupanel").removeClass("opened").addClass("closed");
        }

      });


    };

    MTRL_SETTINGS.saveMenu = function() {

      $('#mtrl-savemenu').click(function(e) {

          var neworder = "";
          var newsuborder = "";
          var menurename = "";
          var submenurename = "";
          var menudisable = "";
          var submenudisable = "";

          $(".mtrlmenu").each(function(){
                    var id = $(this).attr("data-id");
                    var menuid = $(this).attr("data-menu-id");
                    neworder += menuid+"|";
                    if($(this).hasClass("disabled")){
                      menudisable += menuid+"|";
                    }
          });

          $(".mtrlsubmenu").each(function(){
                    var id = $(this).attr("data-id");
                    var parentpage = $(this).attr("data-parent-page");
                    newsuborder += parentpage+":"+id+"|";
                    if($(this).hasClass("disabled")){
                      submenudisable += parentpage+":"+id+"|";
                    }
          });

          $(".mtrl-menurename").each(function(){
                    var id = $(this).attr("data-id");
                    var sid = $(this).attr("data-menu-id");
                    var val = $(this).attr("value");
                    var icon = $(this).parent().parent().find(".mtrl-menuicon").attr("value");
                    //console.log(icon);
                    menurename += id+":"+sid+"@!@%@"+val+"[$!&!$]"+icon+"|#$%*|";
          });


          $(".mtrl-submenurename").each(function(){
                    var id = $(this).attr("data-id");
                    var parent = $(this).attr("data-parent-id");
                    var parentpage = $(this).attr("data-parent-page");
                    var val = $(this).attr("value");
                    submenurename += parentpage+"[($&)]"+parent+":"+id+"@!@%@"+val+"|#$%*|";
          });


          //console.log(neworder);
          //console.log(menurename);

            var action = 'mtrl_savemenu';
            var data = {
                neworder: neworder,
                newsuborder: newsuborder,
                menurename: menurename,
                submenurename: submenurename,
                menudisable: menudisable,
                submenudisable: submenudisable,
                action: action,
                mtrl_nonce: mtrl_vars.mtrl_nonce
            };

        $.post(ajaxurl, data, function(response) {
             //console.log(response);
             location.reload();
            //console.log(response);
        });

        return false;

        });

    };


    MTRL_SETTINGS.resetMenu = function() {

      $('#mtrl-resetmenu').click(function(e) {

            var action = 'mtrl_resetmenu';
            var data = {
                action: action,
                mtrl_nonce: mtrl_vars.mtrl_nonce
            };

        $.post(ajaxurl, data, function(response) {
             location.reload();
            //console.log(response);
        });

        return false;

        });

    };





    MTRL_SETTINGS.menuDisplay = function() {

      $('.mtrldisplay, .mtrlsubdisplay').click(function(e) {

        //var id = $(this).parent().attr("data-id");

        if($(this).hasClass("disable")) {
          $(this).removeClass("disable").addClass("enable");
          //$(this).html("show");
          $(this).parent().parent().removeClass("enabled").addClass("disabled");
        } else if($(this).hasClass("enable")) {
          $(this).removeClass("enable").addClass("disable");
          //$(this).html("hide");
          $(this).parent().parent().removeClass("disabled").addClass("enabled");
        }

      });

    };


    MTRL_SETTINGS.TopbarFixed = function() {
            var menu = $('#wpadminbar');
            if ($(window).scrollTop() > 60) {
                menu.addClass('showfixed');
                $("#wpcontent").addClass('hasfixedtopbar');
            } else {
                menu.removeClass('showfixed');
                $("#wpcontent").removeClass('hasfixedtopbar');
            }

    };


    MTRL_SETTINGS.menuUserProfileInfo = function() {
        function mtrl_menu_userinfo_ajax(){
              jQuery.ajax({
                  type: 'POST',
                  url: mtrl_wp_stats_ajax.mtrl_wp_stats_ajaxurl,
                  data: {"action": "mtrl_wp_stats_ajax_online_total"},
                  success: function(data)
                      {
                        //console.log("Hello world"+data);
                        //jQuery("#adminmenuback").append(data);
                        jQuery("#adminmenuwrap").prepend(data);
                        //jQuery(".mtrl_online_total").html(data);
                        console.log(window.innerHeight);
                        jQuery("#adminmenu").height(window.innerHeight - 100);
                        var links = jQuery("#wp-admin-bar-user-actions").html();
                        //console.log(links);
                        jQuery(".mtrl-menu-profile-links .all-links").html(links);
                        jQuery("#wp-admin-bar-my-account").remove();
                      }
                });
            }
              mtrl_menu_userinfo_ajax();
    };

    /******************************
     initialize respective scripts 
     *****************************/
    $(document).ready(function() {
        MTRL_SETTINGS.menuResizer();
        MTRL_SETTINGS.menuClickResize();
        MTRL_SETTINGS.logoURL();
        MTRL_SETTINGS.menuToggle();
        MTRL_SETTINGS.saveMenu();
        MTRL_SETTINGS.menuDisplay();
        MTRL_SETTINGS.iconPanel();
        MTRL_SETTINGS.resetMenu();
        //MTRL_SETTINGS.menuUserProfileInfo();

 // disabled for adding extra element to buttons.. causing issues in 3rd party plugins compatibility
/*
   $(".button, .button-primary,.button-secondary,.button-primary, .fmenu__button--main, .fmenu__list li a").not(".submit-add-to-menu,.menu-save").addClass("mtrlwaves-effect mtrlwaves-light");
*/
    Waves.attach('li a.menu-top', ['waves-button', 'waves-float', 'waves-ripple']);
    Waves.attach('.row-actions a', ['waves-button', 'waves-float', 'waves-ripple']);

    //usof is and options plugin used in some themes
    if($(".redux-container").length == "0" && $(".usof-content").length == "0"){
//      Waves.attach('input[type="checkbox"],input[type="radio"],.wp-list-table .column-primary .toggle-row', ['waves-ripple','waves-circle']);
    }
    Waves.init();
    
    
    $(document).on('click', "#screen-meta-links .screen-meta-toggle", function () {
        
        setInterval(function(){
          var h=$("#screen-meta").height();
          $("#screen-meta-links").css({'top':h});
          if(h > 0){
            $("#screen-meta-links").addClass("opened");
          } else {
            $("#screen-meta-links").removeClass("opened");
          }
        }, 1);

      });

    if($("#wpbody-content .wrap h1:not(.screen-reader-text)").length == 0){
        //console.log("noh1");
        $("#wpcontent").addClass("mtrl_nopagetitle");
    } else {
        $("#wpcontent").removeClass("mtrl_nopagetitle");
        //console.log("h1present");
    }


/*
    $(document).on('click', "#mtrl_browser_type_wp_dashboard .ui-sortable-handle", function () {
                    setTimeout(function(){
                            console.log("hgihi");
                              myChart14.resize();
                    }, 100);

        });
*/


    });

    $(window).resize(function() {
        MTRL_SETTINGS.menuResizer();
        MTRL_SETTINGS.menuClickResize();

            //console.log("hello");
            //$(".chartBox").each(function(){
               // var id = "mtrl_comments_byMonthYear";
                //var id = $(this).find().attr('_echarts_instance_');
               // window.echarts.getInstanceById(id).redraw();
            //});
    });

    $(window).load(function() {
        MTRL_SETTINGS.menuResizer();
        MTRL_SETTINGS.menuClickResize();
    });

    $(window).scroll(function() {
        MTRL_SETTINGS.TopbarFixed();
    });

});



jQuery(function($) {
    if($.isFunction($.fn.sortable)){
        $( "#mtrl-enabled, #mtrl-disabled" ).sortable({
          connectWith: ".mtrl-connectedSortable",
          handle: ".mtrlmenu-wrap",
          cancel: ".mtrltoggle",
          placeholder: "ui-state-highlight",
        }).disableSelection();
      }
  });


jQuery(function($) {
    if($.isFunction($.fn.sortable)){
      $( ".mtrlsubmenu-wrap" ).sortable({
        placeholder: "ui-state-highlight",
      }).disableSelection();
  }
  });


jQuery(function($) {
  $(document).ready(function(){
    $(document).on('click', ".pickicon", function () {
          var clss = $(this).attr("data-class");
          var prnt = $(this).parent().parent();
          //console.log(clss);
          prnt.find("input").attr("value",clss);
          prnt.find("input").val(clss);
          var main = prnt.find(".mtrlmenuicon");
          main.removeClass(main.attr("data-class")).addClass(clss);
          main.attr("data-class",clss);
          return false;
      });

    $(document).on('click', "body", function () {
          $(".mtrliconpanel").hide();
          //return false;
      });



    });
});
