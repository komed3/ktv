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

                if( 'redirect' in _data ) {

                    __load( _data.redirect );

                } else if( _data.content != $( 'main' ).html() ) {

                    history.pushState( _data, '', _data.url );

                    document.title = _data.title + ' — K3TV';

                    $( 'html, body' ).attr( 'page', _data.page ).animate( {
                        scrollTop: 0
                    }, 10 );

                    $( 'main' ).html( _data.content );

                }

                if( parseInt( _data.refresh || -1 ) > 1000 ) {

                    __refresh = setTimeout( () => {
                        __load( dataload );
                    }, _data.refresh );

                }

            }
        } );

    };

    $( document ).on( 'click', 'a[page]', function ( e ) {

        e.preventDefault();
        e.stopImmediatePropagation();
        e.stopPropagation();

        let dataload = {};

        $.each( this.attributes, ( _i, attr ) => {

            dataload[ attr.name ] = attr.value;

        } );

        __load( dataload );

    } );

    /* load content */

    let path = window.location.pathname.split( '/' );

    __load( {
        page: path[1] || 'live',
        request: path[2] || null
    } );

} );