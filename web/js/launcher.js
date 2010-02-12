(function($)
{
  $(function() {
    // admin
    $('#dm_admin_content div.sf_widget_form_dm_tags_autocomplete select').fcbkcomplete({
      json_url: $.dm.ctrl.getHref('+/dmTagAdmin/getTagsForAutocomplete'),
      cache: true,
      filter_case: true,
      filter_hide: true,
      newel: true
    });
  });
  
})(jQuery);