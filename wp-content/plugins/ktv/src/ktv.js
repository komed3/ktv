jQuery( document ).ready( function ( $ ) {

    var __refresh = null;

    var __load = function ( dataload ) {

        clearTimeout( __refresh );

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
                data: dataload
            },
            success: function ( response ) {

                let _data = JSON.parse( response );

                if( _data.content != $( 'main' ).html() ) {

                    history.pushState( _data, '', _data.url );

                    $( 'html, body' ).attr( 'page', _data.page );

                    $( 'main' ).html( _data.content );

                }

                __refresh = setTimeout( () => {
                    __load( dataload );
                }, 90000 );

            }
        } );

    };

    $( document ).on( 'click', 'a[page]', function ( e ) {

        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();

        __load( {
            page: $( this ).attr( 'page' )
        } );

    } );

    __load( window.location.pathname.split( '/' )[0] || 'live' );

} );