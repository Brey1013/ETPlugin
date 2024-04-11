jQuery( document ).ready(function() {
    

    // Move the mobile version of the add listing header button to where it needs to be

    jQuery('a.et-mobile-header-btn').detach().appendTo('header#site-header .site-branding');

    // Change Archives to Search Results on Search results page

    jQuery('body.archive.post-type-archive.post-type-archive-listing_ad #content.site-content .theme-banner h1').html('Your Search Results: <span>Listing Ads</span>');


    // Replace product name on the cart page

    jQuery('.woocommerce-cart.woocommerce-page .product-name a').html('Basic Advert Listing');

    // Move more info button of home search form to where it needs to be

    // jQuery('a.et-search-more-info').detach().prependTo('.et-homepage-search-form form ul li:last-child');

    // Create a new UL in that same form and move some fields into it

    // var searchform2 = jQuery("<ul class='et-expanded-search'></ul>");

    // jQuery('.et-homepage-search-form form.searchandfilter').append(searchform2);

    // var searchformbottom = jQuery("<ul class='et-search-bottom'></ul>");

    // jQuery('.et-homepage-search-form form.searchandfilter').append(searchformbottom);

    // Move Fields into that new UL
    
    // jQuery('li:has("select#ofbrand")').detach().appendTo('ul.et-expanded-search');
    // jQuery('li:has("select#ofad-product-code")').detach().appendTo('ul.et-expanded-search');

    jQuery('.et-homepage-search-form form.searchandfilter li:has("input[type=submit]")').detach().appendTo('.et-search-bottom');

    // Toggle the expanding class on the parent element to trigger expansion or collapse

    jQuery('a.et-search-more-info').click(function(){
        jQuery('.et-homepage-search-form').toggleClass('et-search-expanded');
        jQuery('.et-advanced-homepage-search').toggleClass('et-search-column-expanded');
        jQuery(this).toggleClass('more');
    })

    // Add an arrow to the previous button on the add liasting form

    jQuery('body.page-id-1406 .pagination span.page-item:first-child a.page-link').html('< Previous');


    // Add class to body when on specific pages of the account

    switch (window.location.pathname) {
        case '/my-account/home/':
            jQuery('body').addClass('et-account-home');
            break;
        case '/my-account/add-listings/':
            jQuery('body').addClass('et-account-add-listings');
            break;
        case '/my-account/my-listings/':
            jQuery('body').addClass('et-account-my-listings');
            break;
        case '/my-account/orders/':
            jQuery('body').addClass('et-account-orders');
            break;
        case '/my-account/edit-account/':
            jQuery('body').addClass('et-account-edit');
            break;
        default: 
            // code block
    }






















    // Cut and paste top element on account pages
    // jQuery("#et-pagecut-top").detach().appendTo('.rtcl-user-info');
    // jQuery("#et-pagecut-login-top").detach().appendTo('.rtcl:has(rtcl-user-login-wrapper)');

    // Cut and paste seller registration form on registration page

    jQuery("#et-seller-register-account .um-register").detach().appendTo(".rtcl-registration-form-wrap");

    // Change naming on the frontend of pricing options
    
    jQuery('.rtcl-listing-pricing-type:nth-child(3) label').html('Disabled (POA)');
    jQuery('.rtcl-listing-pricing-type:nth-child(2)').detach();

    


    // Cut and paste header button
    // jQuery("#et-header-button").detach().prependTo('header .main-navigation-area');

    // Copy and paste items onlisting form
    jQuery('.page-id-7 .rtcl-post-section-title').detach().appendTo('.elementor-container:has("#rtcl-post-form") > .elementor-column:first-child');
    jQuery('.page-id-7 .rtcl-post-video-urls.rtcl-post-section').detach().appendTo('.elementor-container:has("#rtcl-post-form") > .elementor-column:first-child');
    jQuery('.page-id-7 .rtcl-post-gallery.rtcl-post-section').detach().appendTo('.elementor-container:has("#rtcl-post-form") > .elementor-column:first-child');
    
    
    // jQuery('.rtcl-post-contact-details').detach().appendTo('.elementor-container:has("#rtcl-post-form") > .elementor-column:first-child');

    jQuery('.page-id-326 .rtcl-post-section-title').detach().appendTo('#et-sell-page-form > .elementor-widget-container .et-sell-banner-login-intro');
    jQuery('.page-id-326 .rtcl-post-video-urls.rtcl-post-section').detach().appendTo('#et-sell-page-form > .elementor-widget-container .et-sell-banner-login-intro');
    jQuery('.page-id-326 .rtcl-post-gallery.rtcl-post-section').detach().appendTo('#et-sell-page-form > .elementor-widget-container .et-sell-banner-login-intro');
    
    
    // jQuery('.rtcl-post-contact-details').detach().appendTo('#et-sell-page-form > .elementor-widget-container .et-sell-banner-login-intro');

    // Cut and paste listings count homepage
    jQuery("#et-post-count").detach().appendTo('.et-post-count-heading');

    // Change "Membership" to "Types" on membership checkout page
    jQuery("#rtcl-checkout-pricing-option > tbody > tr > th:first-child").html("Account Type");

    // Change "Membership" to "Types" on membership checkout page
    jQuery(".rtcl-listing-info-selecting.classima-form h3").text('Select Category');
    jQuery("#rtcl-ad-type-selection .control-label").text('Ad Category');
    jQuery("select#rtcl-ad-type > option:first-child").text('-Select Category-');

    jQuery(".rtcl-listing-info-selecting.classima-form #rtcl-ad-category-selection h3").text('Select Type');
    jQuery("#rtcl-ad-category-selection .control-label").text('Ad Type');
    jQuery("select#rtcl-category > option:first-child").text('-Select Type-');

    jQuery('.page-id-40 .rtcl-search-type select > option:first-child').text('Select a Category');
    jQuery('.page-id-40 .ws-category select > option:first-child').text('Select Type');

    // Insert some content when a person has no listings yet
    jQuery("<div class='et-no-listing-advice'>You have not posted any listings yet. You can <a href='/listing-form/'>post your first listing here<a>.</div>").appendTo(".listing-archive-noresult");

    // Replace the word 'membership' with 'Listing package'

    jQuery(".membership-statistic-report-wrap h4").text(jQuery(".membership-statistic-report-wrap h4").text().replace("Membership", "Listing Package"));
    jQuery(".reports p").text(jQuery(".reports p").text().replace("membership", "listing package. A listing package is required to post ads"));
    jQuery(".reports + p").html("<a class='et-acc-membership-button' href='/checkout/membership/'>Buy listing package <i class='fa-solid fa-angles-right'></i></a> <a style='margin-left: 10px' class='et-acc-membership-button' href='/listing-form/'>Post listing<i class='fa-solid fa-angles-right'></i></a>");

    // Youtube videos no relative videos
         jQuery('iframe[src*="youtube.com"]').each(function () {

           var sVideoURL = jQuery(this).attr('src');

           if (sVideoURL.indexOf('rel=0') == -1) {

               jQuery(this).attr('src', sVideoURL + '&rel=0');

           }

       });

});