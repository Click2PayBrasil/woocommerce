jQuery(function ($) {
  // enable copy button
  var clipboard = new ClipboardJS( '.pix-copy' );

  clipboard.on('success', function() {
    const button = $( '.pix-copy-button' );
    const buttonText = button.text();

    button.text( 'Copiado!' );

    setTimeout(() => {
      button.text( buttonText );
    }, 1000);
  });
});
