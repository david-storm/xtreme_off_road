(($, Drupal) => {
  const originalDialogAttach = Drupal.AjaxCommands.prototype.openDialog;
  // Override Drupal.AjaxCommands.prototype.openDialog
  Drupal.AjaxCommands.prototype.openDialog = function (ajax, response, status) {
    originalDialogAttach(ajax, response, status);

    // Add ResizeObserver to dialog.
    new ResizeObserver((element) => {
      $(element[0].target).trigger('resize.dialogResize');
    }).observe($(response.selector)[0]);
  }

})(jQuery, Drupal);
