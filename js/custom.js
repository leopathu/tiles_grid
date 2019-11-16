(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.LotusBehavior = {
    attach: function (context, settings) {

      // init Isotope
      var $grid = $('.grid').isotope({
        // options
        // options
        itemSelector: '.grid-item',
        layoutMode: 'fitRows'
      });
      // filter items on button click
      $('.filter-button-group').on( 'click', 'button', function() {
        var filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });
      });

    }
  }
})(jQuery, Drupal, drupalSettings);
