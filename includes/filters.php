<?php
    
    /**
     * Filter lost password URL
     *
     * @param $lostpassword_url
     * @param $redirect
     *
     * @return false|mixed|string
     */
    function b3_lost_password_page_url( $lostpassword_url, $redirect ) {
        
        $lost_password_page_id = b3_get_forgotpass_id();
        if ( false != $lost_password_page_id ) {
            $lost_pass_url = esc_url( get_permalink( $lost_password_page_id ) );
            if ( class_exists( 'SitePress' ) ) {
                $lost_pass_url = esc_url( get_permalink( apply_filters( 'wpml_object_id', $lost_password_page_id, 'page', true ) ) );
            }
            if ( false != $redirect ) {
                return $lost_pass_url . '?redirect_to=' . $redirect;
            }
            
            return $lost_pass_url;
            
        }
        
        return $lostpassword_url;
    }
    // add_filter( 'lostpassword_url', 'b3_lost_password_page_url', 10, 2 );
    
    
    /**
     * Override new user notification for admin
     *
     * @param $wp_new_user_notification_email_admin
     * @param $user
     * @param $blogname
     *
     * @return mixed
     */
    function b3_new_user_notification_email_admin( $wp_new_user_notification_email_admin, $user, $blogname ) {
        
        if ( 'request_access' == get_option( 'b3_registration_type' ) ) {
            $wp_new_user_notification_email_admin[ 'to' ]      = get_option( 'admin_email' ); // add filter for override
            $wp_new_user_notification_email_admin[ 'subject' ] = __( 'New user access request', 'b3-onboarding' );
            $wp_new_user_notification_email_admin[ 'message' ] = __( 'A new user has requested access. You can approve/deny him/her in the User approval panel.', 'b3-onboarding' );
        } elseif ( 'open' == get_option( 'b3_registration_type' ) ) {
            // @TODO: add if user wants to receive admin notification on open registration
            $wp_new_user_notification_email_admin[ 'to' ]      = get_option( 'admin_email' ); // add filter for override
            $wp_new_user_notification_email_admin[ 'subject' ] = apply_filters( 'b3_new_user_subject', b3_get_new_user_subject( $blogname ) );
            $wp_new_user_notification_email_admin[ 'message' ] = apply_filters( 'b3_new_user_mesage', b3_get_new_user_message( $blogname, $user ) );
        }
        
        return $wp_new_user_notification_email_admin;
        
    }
    add_filter( 'wp_new_user_notification_email_admin', 'b3_new_user_notification_email_admin', 9, 3 );
    
    
    /**
     * Override new user notification email for user
     *
     * @param $wp_new_user_notification_email
     * @param $user
     * @param $blogname
     *
     * @return mixed
     */
    function b3_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {
        
        global $wpdb;
        
        $wp_new_user_notification_email[ 'to' ] = $user->user_email;
        if ( 'request_access' == get_option( 'b3_registration_type' ) ) {
            $wp_new_user_notification_email[ 'subject' ] = apply_filters( 'b3_request_access_subject', b3_get_request_access_subject( $blogname ) );
            $wp_new_user_notification_email[ 'message' ] = apply_filters( 'b3_request_access_message', b3_get_request_access_message( $blogname, $user ) );
            
        } elseif ( 'email_activation' == get_option( 'b3_registration_type' ) ) {
            // Generate an activation key
            $key = wp_generate_password( 20, false );
            
            // Set the activation key for the user
            $wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'user_login' => $user->user_login ) );
            
            $login_url = home_url( 'login' );
            if ( false != b3_get_login_id() ) {
                $login_url = get_permalink( b3_get_login_id() );
            }
            
            $activation_url = add_query_arg( array( 'action' => 'activate', 'key' => $key, 'user_login' => rawurlencode( $user->user_login ) ), home_url( 'login' ) );
            
            $wp_new_user_notification_email[ 'subject' ] = esc_html__( 'Activate your account', 'b3-onboarding' );
            $wp_new_user_notification_email[ 'message' ] = 'Add a link here: ' . $activation_url;
            
        } elseif ( 'open' == get_option( 'b3_registration_type' ) ) {
            
            $wp_new_user_notification_email[ 'subject' ] = apply_filters( 'b3_welcome_user_subject', b3_get_welcome_user_subject( $blogname ) );
            $wp_new_user_notification_email[ 'message' ] = apply_filters( 'b3_welcome_user_message', b3_get_welcome_user_message( $blogname, $user ) );

        } else {
            error_log( 'OOPS, else' );
        }
        
        return $wp_new_user_notification_email;
        
    }
    add_filter( 'wp_new_user_notification_email', 'b3_new_user_notification_email', 10, 3 );
    
    
    /**
     * Returns the message subject for the password reset mail.
     *
     * @param $subject
     * @param $user_login
     * @param $user_data
     *
     * @return mixed
     */
    function b3_replace_retrieve_password_subject( $subject, $user_login, $user_data ) {
    
        $b3_forgot_password_subject = get_option( 'b3_forgot_password_subject', false );
        if ( false != $b3_forgot_password_subject ) {
            return $b3_forgot_password_subject;
        }
        
        return b3_default_forgot_password_subject();

    }
    add_filter( 'retrieve_password_title', 'b3_replace_retrieve_password_subject', 10, 3 );


    /**
     * Returns the message body for the password reset mail.
     *
     * @param string  $message    Default mail message.
     * @param string  $key        The activation key.
     * @param string  $user_login The username for the user.
     * @param WP_User $user_data  WP_User object.
     *
     * @return string   The mail message to send.
     */
    function b3_replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
    
        $b3_forgot_password_message = get_option( 'b3_forgot_password_message', false );
        if ( false != $b3_forgot_password_message ) {
            $message = $b3_forgot_password_message;
        } else {
            $message = b3_default_forgot_password_message();
        }
    
        if ( false != get_option( 'b3_custom_emails', false ) ) {
            $message = b3_replace_template_styling( $message );
        }
    
        // replace email variables
        $vars = [
            'reset_url' => network_site_url( "wp-login.php?action=rp&key=" . $key . "&login=" . rawurlencode( $user_data->user_login ), 'login' ) . "\r\n\r\n",
        ];
        $message = strtr( $message, b3_replace_email_vars( $vars ) );
    
        return $message;
    }
    add_filter( 'retrieve_password_message', 'b3_replace_retrieve_password_message', 10, 4 );
    
