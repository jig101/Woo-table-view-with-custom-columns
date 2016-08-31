jQuery( document ).ready(function() {

    //Load posts on document ready
  // jplt_get_posts();

 
    //If list item is clicked, trigger input change and add css class
    // jQuery('#jplt-filter li').live('click', function(){
    //     var input = jQuery(this).find('input');
 
    //             //Check if clear all was clicked
    //     if ( jQuery(this).attr('class') == 'clear-all' )
    //     {
    //         jQuery('#jplt-filter li').removeClass('selected').find('input').prop('checked',false); //Clear settings
    //         jplt_get_posts(); //Load Posts
    //     }
    //     else if (input.is(':checked'))
    //     {
    //         input.prop('checked', false);
    //         jQuery(this).removeClass('selected');
    //     } else {
    //         input.prop('checked', true);
    //         jQuery(this).addClass('selected');
    //     }
 
    //     input.trigger("change");
    // });
 
    //If input is changed, load posts
    // jQuery('#jplt-filter input').live('change', function(){
    //     jplt_get_posts(); //Load Posts
    // });
 
    //Find Selected jplts
    // function getSelectedjplts()
    // {
    //     var jplts = []; //Setup empty array
 
    //     jQuery("#jplt-filter li input:checked").each(function() {
    //         var val = jQuery(this).val();
    //         jplts.push(val); //Push value onto array
    //     });     
 
    //     return jplts; //Return all of the selected jplts in an array
    // }
 
    //Fire ajax request when typing in search
    // jQuery('#jplt-search input.txt-search').live('keyup', function(e){
    //     if( e.keyCode == 27 )
    //     {
    //         jQuery(this).val(''); //If 'escape' was pressed, clear value
    //     }
 
    //     jplt_get_posts(); //Load Posts
    // });
 
    jQuery('#submit-search').live('click', function(e){
        e.preventDefault();
        jplt_get_posts(); //Load Posts
    });
 
    //Get Search Form Values
    function getSearchValue()
    {
        var searchValue = jQuery('#jplt-search input.txt-search').val(); //Get search form text input value
        return searchValue;
    }
 
    //If pagination is clicked, load correct posts
    jQuery('.jplt-list-navigation a').live('click', function(e){
        e.preventDefault();
 
        var url = jQuery(this).attr('href'); //Grab the URL destination as a string
        var paged = url.split('&paged='); //Split the string at the occurance of &paged=
 
        jplt_get_posts(paged[1]); //Load Posts (feed in paged value)
    });
 
    //Main ajax function
    function jplt_get_posts(paged)
    {
        var paged_value = paged; //Store the paged value if it's being sent through when the function is called
        var ajax_url = jplt_ajax_params.ajax_url; //Get ajax url (added through wp_localize_script)
        var security = jplt_ajax_params.security;
        var id =  jplt_ajax_params.id;
        jQuery.ajax({
            type: 'GET',
            url: ajax_url,
            data: {
                action: 'jplt_display_table_ajax',
                security: security,
                id: id,
                //jplts: getSelectedjplts, //Get array of values from previous function
                search: getSearchValue(), //Retrieve search value using function
                paged: paged_value //If paged value is being sent through with function call, store here
            },
            beforeSend: function ()
            {
                jQuery('.loading').show();
            },
            success: function(data)
            {
                alert(data);
                jQuery('.loading').hide();
                //Hide loader here
                jQuery('#jplt-results').html(data);
            },
            error: function()
            {
                                //If an ajax error has occured, do something here...
                jQuery("#jplt-results").html('<p>There has been an error</p>');
            }
        });
    }
 
});