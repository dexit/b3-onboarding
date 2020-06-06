<?php

    #####################
    ## General filters ##
    #####################

    /**
     * Filter main logo
     *
     * Must be full URL.
     *
     * @since 2.0.0
     *
     * @param $logo
     *
     * @return false|string
     */
    function b3_main_logo( $logo ) {

        $logo = B3_PLUGIN_URL . '/assets/images/logo-salesforce.png';

        return $logo;
    }
    // add_filter( 'b3_main_logo', 'b3_main_logo' );


    /**
     * Override main link color - WITH HASHTAG
     *
     * @since 2.0.0
     *
     * @param $link_color
     *
     * @return string
     */
    function b3_link_color( $link_color ) {

        $link_color = '#ff0000'; // red
        $link_color = '#ee61'; // invalid hex code
        $link_color = '#6d32a8'; // purple

        return $link_color;
    }
    // add_filter( 'b3_link_color', 'b3_link_color' );
