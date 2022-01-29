<?php

namespace EasyJob\Portal\Frontend;

class Shortcode {
    
    public function __construct() {
        add_shortcode( 'wedevs-acdemy', [ $this, 'shortcode_callback_func' ] );
    }

    public function shortcode_callback_func( $atts, $content = '' ) {
        return 'This is new shortcode';
    }
}
