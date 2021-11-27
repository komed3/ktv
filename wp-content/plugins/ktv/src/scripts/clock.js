( function( $ ) {

    function clock( element ) {

        sec = parseInt( $( element ).attr( 'time' ) );
        abs = Math.abs( sec );

        if( abs < 30 )
            txt = 'wenigen Sekunden';
        else if( abs < 90 )
            txt = Math.round( abs ) + ' Sekunden';
        else if( abs < 5400 )
            txt = Math.round( abs / 60 ) + ' Minuten';
        else if( abs < 259200 )
            txt = Math.round( abs / 3600 ) + ' Stunden';
        else if( abs < 864000 )
            txt = Math.round( abs / 86400 ) + ' Tage';
        else if( abs < 3628800 )
            txt = Math.round( abs / 604800 ) + ' Wochen';
        else if( abs < 47336400 )
            txt = Math.round( abs / 2629800 ) + ' Monate';
        else
            txt = Math.round( abs / 31557600 ) + ' Jahre';
  
        $( element ).html( ( sec < 0 ? 'in' : 'seit' ) + ' <span>' + txt + '</span>' )
                    .attr( 'time', sec + 1 );

        setTimeout( function() {
            clock( element);
        }, 1000 );

    }

    $( '.clock' ).each( function() {
        clock( this );
    } );

} )( jQuery );