jQuery( document ).ready( function ( $ ) {

    var __load = function ( page ) {

        $.ajax( {
            xhr: function () {

                let xhr = new window.XMLHttpRequest();

                xhr.upload.addEventListener( 'loadstart', function ( e ) {

                    $( '.progress' ).css( {
                        opacity: 1,
                        width: 0
                    } );

                }, false );

                xhr.upload.addEventListener( 'progress', function ( e ) {

                    if( e.lengthComputable ) {

                        $( '.progress' ).css( {
                            opacity: 1,
                            width: e.loaded / e.total * 100 + '%'
                        } );

                    }

                }, false );

                xhr.upload.addEventListener( 'loadend', function ( e ) {

                    setTimeout( function () {

                        $( '.progress' ).css( {
                            opacity: 0,
                            width: 0
                        } );

                    }, 500 );

                }, false );

                return xhr;

            },
            url: window.location.origin + '/wp-admin/admin-ajax.php',
            type: 'post',
            data: {
                action: '__ktv',
                page: page
            },
            success: function ( response ) {

                let _data = JSON.parse( response );

                history.pushState( _data, '', _data.url );

                $( 'html, body' ).attr( 'page', _data.page );

                $( 'main' ).html( _data.content );

            }
        } );

    };

    $( document ).on( 'click', 'a[page]', function ( e ) {

        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();

        __load( $( this ).attr( 'page' ) );

    } );

    __load( window.location.pathname.split( '/' )[0] || 'live' );

} );