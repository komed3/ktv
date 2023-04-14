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

    var __clock = function () {

        let curr = Date.now() - ( new Date().getTimezoneOffset() * 60000 );

        $( 'clock[time]' ).each( function () {

            let time = parseInt( $( this ).attr( 'time' ) ) * 1000,
                diff = curr - time,
                absD = Math.abs( diff ) / 1000,
                text = '',
                extd = true;

            if( absD < 90 ) {

                text = 'few Seconds';

            } else if( absD < 5400 ) {

                text = Math.round( absD / 60 ) + ' Minutes';

            } else if( absD < 172800 ) {

                text = Math.round( absD / 3600 ) + ' Hours';

            } else if( absD < 1209600 ) {

                text = Math.round( absD / 86400 ) + ' Days';

            } else {

                extd = false;

                text = '@ ' + (
                    new Intl.DateTimeFormat( 'en-US' )
                ).format(
                    new Date().setTime( time )
                );

            }

            $( this ).text( extd ? ( diff < 0 ? 'in ' + text : text + ' ago' ) : text );

        } );

        setTimeout( __clock, 999 );

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

    __clock();

} );