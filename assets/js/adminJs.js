   var $ =jQuery.noConflict();
    $(document).ready(function() {
      
      /**
       * shows and hides tabbed content in the custom metabox
       */
      $('#jplt-tabs ul li').click(function(){
        var tab_id = $(this).attr('data-tab');
        $('ul.tabs li').removeClass('current');
        $('.tab-content').removeClass('current');
        $(this).addClass('current');
        $("#"+tab_id).addClass('current');
      });

      /**
       * On load lets get the current count of the columns class
       */
      var rowCount =  $( "#jplt-meta-wrap #tab-1 .col" ).length; 
      /**
       * Setup coller picker for the correct fields( table property tab).
       */
      $('.jpltcolorpic').wpColorPicker();

      // $( ".prtog-op" ).click(function() {
      //   var tis = $(this);
      //   $( ".meta-property-fields .jpltfields" ).toggle( "flip", function() {
          
      //     var val = tis.html();
      //     if( val == 'Show'){
      //      tis.html('Hide');
      //     } else {
      //       tis.html('Show');
      //     }
          
      //   });
      //    return false;
      // });
      
      /**
       * Add new column.
       */
      $( "#jplt-add-new" ).click( function( ) {
        rowCount++;
        jplt_add_new_row_col();
        return false;
      } );
      /**
       * Remove selected column.
       */
      $( ".jplt-column-wrapper .col #jplt-remove" ).live('click', function() {
        var colId = $(this).data('countrow');
        var pstId = $(this).data('postid');
        jplt_remove_row_col(colId, pstId, $(this));
        rowCount--;
        return false;
      });        
        
      /**
       * On load check column type fields and call the show_linked_field
       */
      $( "#jplt-meta-wrap .col #jplt-on-select" ).each( function( ) {
          var vaal = $( this ).val( ); 
          show_linked_field( $( this ) );
      });
      /**
       * When a new column is added and not saved this will allow us to show and hide its hidden field 
       */
      $('#jplt-meta-wrap .col #jplt-on-select').live('change', function() {
        $(this).parent().find('.length').hide();
        $(this).parent().find('.ssp').hide();
        $(this).parent().find('.att').hide();
        $(this).parent().toggleClass( "highlight" );
        show_linked_field($(this));        
      });

      /**
      * [show_linked_field Toggels hidden fields for selected column type.]
      * @param  {[type]} t [current selected field AS object.]
      * @return {[type]}   [no return just toggle]
      */
      function show_linked_field( t ) {
      var vaal = t.val(); 
      t.parent().find('.length').hide();
      t.parent().find('.ssp').hide();
      t.parent().find('.att').hide();
      if(vaal == 'Product Description'){
        console.log('Enable field: '+vaal);
        t.parent().find('.length').show();
      }
      if(vaal == 'Price'){
         t.parent().find('.ssp').show();
      }
      if(vaal == 'Attribute'){
       t.parent().find('.att').show();
      }
      }


      /**
       * [jplt_add_new_row_col Call the back end function to add a new column to the current window.]
       * @return {[type]} [Return a fresh column in HTML]
       */
      function jplt_add_new_row_col()
      {
          var ajax_url = jpb_ajax_params.ajax_url; //Get ajax url (added through wp_localize_script)
          var security = jpb_ajax_params.security;
          $.ajax({
              type: 'GET',
              url: ajax_url,
              data: {
                  action: 'jplt_get_col_fields_ajax',
                  security: security,
                  countq: rowCount,
                  //jpbs: getSelectedjpbs, //Get array of values from previous function
                  //search: getSearchValue(), //Retrieve search value using function
                  //paged: paged_value //If paged value is being sent through with function call, store here
              },
              beforeSend: function ()
              {
                  //$('.loading').show();
                  $("#jplt-add-new").attr("disabled", true);
              },
              success: function(data)
              {
                  $('#jplt-meta-wrap #tab-1').append(data);
                  $("#jplt-add-new").removeAttr("disabled");
              },
              error: function()
              {
                alert("Sorry, there was a problem!");
                                  //If an ajax error has occured, do something here...
                 // $("#jpb-results").html('<p>There has been an error</p>');
              }
          });
      }
      /**
       * [jplt_remove_row_col Call the back end function to remove the selected column.]
       * @return {[type]} [Return all new columns in HTML]
       */      
      function jplt_remove_row_col(colId, pstId, tis)
      {
          var ajax_url = jpb_ajax_params.ajax_url; //Get ajax url (added through wp_localize_script)
          var security = jpb_ajax_params.security;
          $.ajax({
              type: 'GET',
              url: ajax_url,
              data: {
                  action: 'jplt_remove_col_fields_ajax',
                  security: security,
                  colId: colId,
                  pstId: pstId,
                  //jpbs: getSelectedjpbs, //Get array of values from previous function
                  //search: getSearchValue(), //Retrieve search value using function
                  //paged: paged_value //If paged value is being sent through with function call, store here
              },
              beforeSend: function ()
              {
                  //$('.loading').show();
                  //$("#jplt-add-new").attr("disabled", true);
              },
              success: function(data)
              {
               // console.log(data);
                //alert("Success!" + data);
                $('#jplt-meta-wrap #tab-1').html('');
                $('#jplt-meta-wrap #tab-1').append(data);
                // $( ".jplt-column-wrapper .col #jplt-on-select" ).each(function(){
                //       var vaal = $(this).val(); 
                //     show_linked_field($(this));
                // });
                 // $('.loading').hide();
                  //Hide loader here
                  //$('#jplt-append').append(data);
                 // rowCount++;
                  //$("#jplt-add-new").removeAttr("disabled");
              },
              error: function()
              {
                alert("Sorry, there was a problem!");
                                  //If an ajax error has occured, do something here...
                 // $("#jpb-results").html('<p>There has been an error</p>');
              }
          });
      }
        // $(".remove").live('click', function() {
        //     $(this).parent().remove();
        // });
    });