<?php
/*
 * Display name: ADMIN 📅 SCHEDULER - Editor Next Free Slot - v2
 * Scope: admin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Finds the first scheduling slot available from now, using the calendar's slot order.
 */
function clm_editor_next_free_slot( $excluded_post_id = 0 ) {
    global $wpdb;

    $timezone         = wp_timezone();
    $now              = new DateTimeImmutable( 'now', $timezone );
    $day              = new DateTimeImmutable( 'today', $timezone );
    $slot_hours       = array( 10, 14, 11, 12, 13 );
    $excluded_post_id = absint( $excluded_post_id );

    for ( $offset = 0; $offset < 366; $offset++ ) {
        $day_key = $day->format( 'Y-m-d' );
        $params  = array( $day_key );
        $exclude = '';

        if ( $excluded_post_id ) {
            $exclude  = ' AND ID != %d';
            $params[] = $excluded_post_id;
        }

        $occupied_hours = $wpdb->get_col(
            $wpdb->prepare(
                "
                SELECT DISTINCT HOUR(post_date)
                FROM {$wpdb->posts}
                WHERE post_type = 'post'
                  AND post_status IN ('publish', 'future', 'draft')
                  AND DATE(post_date) = %s
                  {$exclude}
                ",
                $params
            )
        );
        $occupied_hours = array_map( 'intval', $occupied_hours );

        foreach ( $slot_hours as $hour ) {
            $slot = $day->setTime( $hour, 0, 0 );
            if ( $slot <= $now || in_array( $hour, $occupied_hours, true ) ) {
                continue;
            }

            return $slot;
        }

        $day = $day->modify( '+1 day' );
    }

    return false;
}

function clm_editor_get_next_free_slot_ajax() {
    check_ajax_referer( 'clm_editor_next_free_slot', 'nonce' );

    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
    if ( $post_id ) {
        $post = get_post( $post_id );
        if ( ! $post || 'post' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( array( 'message' => 'Permissions insuffisantes.' ), 403 );
        }
    } elseif ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( array( 'message' => 'Permissions insuffisantes.' ), 403 );
    }

    $slot = clm_editor_next_free_slot( $post_id );
    if ( ! $slot ) {
        wp_send_json_error( array( 'message' => 'Aucun créneau disponible dans les 366 prochains jours.' ), 500 );
    }

    wp_send_json_success(
        array(
            'date' => $slot->format( 'Y-m-d\TH:i:s' ),
        )
    );
}
add_action( 'wp_ajax_clm_editor_get_next_free_slot', 'clm_editor_get_next_free_slot_ajax' );

function clm_editor_get_date_markers_ajax() {
    global $wpdb;

    check_ajax_referer( 'clm_editor_next_free_slot', 'nonce' );

    $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
    if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( array( 'message' => 'Permissions insuffisantes.' ), 403 );
    }
    if ( ! $post_id && ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( array( 'message' => 'Permissions insuffisantes.' ), 403 );
    }

    $timezone   = wp_timezone();
    $start_date = new DateTimeImmutable( 'today -1 year', $timezone );
    $end_date   = new DateTimeImmutable( 'today +1 year', $timezone );
    $params     = array(
        $start_date->format( 'Y-m-d H:i:s' ),
        $end_date->modify( '+1 day' )->format( 'Y-m-d H:i:s' ),
    );
    $exclude = '';

    if ( $post_id ) {
        $exclude  = ' AND ID != %d';
        $params[] = $post_id;
    }

    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT DATE(post_date) AS post_day, post_status
            FROM {$wpdb->posts}
            WHERE post_type = 'post'
              AND post_status IN ('draft', 'future', 'publish')
              AND post_date >= %s
              AND post_date < %s
              {$exclude}
            ",
            $params
        )
    );
    $markers = array();

    foreach ( $rows as $row ) {
        if ( ! isset( $markers[ $row->post_day ] ) ) {
            $markers[ $row->post_day ] = array();
        }
        $markers[ $row->post_day ][] = $row->post_status;
    }

    foreach ( $markers as $day => $statuses ) {
        $markers[ $day ] = array_values( array_unique( $statuses ) );
    }

    wp_send_json_success( array( 'markers' => $markers ) );
}
add_action( 'wp_ajax_clm_editor_get_date_markers', 'clm_editor_get_date_markers_ajax' );

function clm_editor_enqueue_next_free_slot_script( $hook ) {
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }

    $screen = get_current_screen();
    if ( ! $screen || 'post' !== $screen->post_type || ! wp_script_is( 'wp-edit-post', 'registered' ) ) {
        return;
    }

    wp_enqueue_script( 'wp-edit-post' );

    $config = array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'clm_editor_next_free_slot' ),
        'months'  => array_values( $GLOBALS['wp_locale']->month ),
    );
    $script = 'window.clmEditorNextFreeSlot = ' . wp_json_encode( $config ) . ';';
    $script .= <<<'CSS'
const clmEditorDateMarkerStyle = document.createElement( 'style' );
clmEditorDateMarkerStyle.textContent = `
    .clm-editor-date-target.clm-editor-has-markers {
        position: relative;
        padding-bottom: 10px !important;
    }
    .clm-editor-date-markers {
        position: absolute;
        bottom: 2px;
        left: 50%;
        display: flex;
        gap: 2px;
        transform: translateX(-50%);
        pointer-events: none;
    }
    .clm-editor-date-marker {
        width: 5px;
        height: 5px;
        border-radius: 50%;
    }
    .clm-editor-date-marker--draft { background: #d63638; }
    .clm-editor-date-marker--publish { background: #00a32a; }
    .clm-editor-date-marker--future { background: #2271b1; }
`;
document.head.appendChild( clmEditorDateMarkerStyle );
CSS;
    $script .= <<<'JS'
( function() {
    let requested = false;
    let attempts = 0;
    let dateMarkers = {};
    let markerObserver;
    let editor;

    function normalizeLabel( value ) {
        return String( value || '' ).toLocaleLowerCase().normalize( 'NFD' ).replace( /[\u0300-\u036f]/g, '' );
    }

    function getDateFromButton( button ) {
        const label = [
            button,
            button.parentElement,
            button.closest( '[role="gridcell"]' )
        ]
            .filter( Boolean )
            .map( function( element ) { return element.getAttribute( 'aria-label' ) || ''; } )
            .find( function( value ) { return /\b\d{4}\b/.test( value ); } ) || '';
        const yearMatch = label.match( /\b(\d{4})\b/ );
        const dayMatch = label.match( /\b([1-9]|[12]\d|3[01])\b/ );
        if ( ! yearMatch || ! dayMatch ) return null;

        const normalizedLabel = normalizeLabel( label );
        const monthIndex = window.clmEditorNextFreeSlot.months.findIndex( function( month ) {
            return normalizedLabel.includes( normalizeLabel( month ) );
        } );
        if ( monthIndex < 0 ) return null;

        return `${yearMatch[1]}-${String( monthIndex + 1 ).padStart( 2, '0' )}-${String( dayMatch[1] ).padStart( 2, '0' )}`;
    }

    function renderDateMarkers() {
        const dateCells = new Set(
            document.querySelectorAll(
                '.components-datetime__date, .components-datetime__calendar [role="gridcell"], .components-date-time-picker [role="gridcell"]'
            )
        );
        dateCells.forEach( function( cell ) {
            const button = cell.matches( 'button' ) ? cell : cell.querySelector( 'button' ) || cell;
            const day = getDateFromButton( button );
            const statuses = day && dateMarkers[ day ] ? dateMarkers[ day ] : [];
            const target = cell.matches( 'button' ) ? cell : cell;
            const existing = target.querySelector( '.clm-editor-date-markers' );
            target.classList.add( 'clm-editor-date-target' );
            target.classList.toggle( 'clm-editor-has-markers', statuses.length > 0 );
            if ( ! statuses.length ) {
                if ( existing ) existing.remove();
                return;
            }

            const statusKey = statuses.slice().sort().join( ',' );
            if ( existing && existing.dataset.statuses === statusKey ) return;

            const markers = existing || document.createElement( 'span' );
            markers.className = 'clm-editor-date-markers';
            markers.dataset.statuses = statusKey;
            markers.replaceChildren();
            statuses.forEach( function( status ) {
                const marker = document.createElement( 'span' );
                marker.className = `clm-editor-date-marker clm-editor-date-marker--${status}`;
                marker.setAttribute( 'aria-hidden', 'true' );
                markers.appendChild( marker );
            } );
            if ( ! existing ) target.appendChild( markers );
        } );
    }

    function startMarkerObserver() {
        if ( markerObserver ) return;
        markerObserver = new MutationObserver( function() {
            window.requestAnimationFrame( renderDateMarkers );
        } );
        markerObserver.observe( document.body, { childList: true, subtree: true } );
    }

    function addCurrentPostMarker( date, status ) {
        if ( ! date ) return;
        const day = String( date ).slice( 0, 10 );
        const markerStatus = status === 'future' ? 'future' : 'draft';
        if ( ! dateMarkers[ day ] ) dateMarkers[ day ] = [];
        if ( ! dateMarkers[ day ].includes( markerStatus ) ) dateMarkers[ day ].push( markerStatus );
        renderDateMarkers();
    }

    function requestDateMarkers( postId, status ) {
        const body = new URLSearchParams( {
            action: 'clm_editor_get_date_markers',
            nonce: window.clmEditorNextFreeSlot.nonce,
            post_id: String( postId || 0 )
        } );

        return window.fetch( window.clmEditorNextFreeSlot.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString(),
            credentials: 'same-origin'
        } )
            .then( function( response ) { return response.json(); } )
            .then( function( response ) {
                if ( response.success && response.data && response.data.markers ) {
                    dateMarkers = response.data.markers;
                    startMarkerObserver();
                    addCurrentPostMarker( editor.getEditedPostAttribute( 'date' ) || editor.getCurrentPostAttribute( 'date' ), status );
                }
            } );
    }

    function setNextFreeSlot() {
        editor = window.wp && window.wp.data && window.wp.data.select( 'core/editor' );
        const dispatch = window.wp && window.wp.data && window.wp.data.dispatch( 'core/editor' );
        if ( ! editor || ! dispatch || ! editor.getCurrentPostType() ) {
            if ( attempts++ < 20 ) {
                window.setTimeout( setNextFreeSlot, 250 );
            }
            return;
        }

        const postType = editor.getCurrentPostType();
        const status = editor.getEditedPostAttribute( 'status' ) || editor.getCurrentPostAttribute( 'status' );
        if ( requested || postType !== 'post' || ! [ 'auto-draft', 'draft', 'future' ].includes( status ) ) {
            return;
        }

        requested = true;
        const body = new URLSearchParams( {
            action: 'clm_editor_get_next_free_slot',
            nonce: window.clmEditorNextFreeSlot.nonce,
            post_id: String( editor.getCurrentPostId() || 0 )
        } );

        window.fetch( window.clmEditorNextFreeSlot.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString(),
            credentials: 'same-origin'
        } )
            .then( function( response ) { return response.json(); } )
            .then( function( response ) {
                if ( response.success && response.data && response.data.date ) {
                    dispatch.editPost( { date: response.data.date } );
                }
                return requestDateMarkers( editor.getCurrentPostId(), status );
            } )
            .catch( function() {
                requested = false;
            } );
    }

    window.setTimeout( setNextFreeSlot, 0 );
}() );
JS;

    wp_add_inline_script( 'wp-edit-post', $script, 'after' );
}
add_action( 'admin_enqueue_scripts', 'clm_editor_enqueue_next_free_slot_script' );
