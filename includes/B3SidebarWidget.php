<?php

    class B3SidebarWidget extends WP_Widget {

        function __construct() {
            parent::__construct(
                'b3-widget',
                'B3: User menu',
                array(
                    'classname'   => 'b3__widget--user',
                    'description' => 'Custom user menu'
                )
            );
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget( $args, $instance ) {
            $count_setting = 0;
            $show_widget   = true;
            $show_account  = ! empty( $instance[ 'show_account' ] ) ? $instance[ 'show_account' ] : false;
            if ( $show_account ) {
                $account_id   = get_option( 'b3_account_page_id' );
                $account_link = ( false != $account_id ) ? get_permalink( $account_id ) : false;
                if ( false == $account_link ) {
                    $count_errors[] = 'account';
                }
                $count_setting++;
            }

            $show_login = ! empty( $instance[ 'show_login' ] ) ? $instance[ 'show_login' ] : false;
            if ( $show_login ) {
                $login_id   = get_option( 'b3_login_page_id' );
                $login_link = ( false != $login_id ) ? get_permalink( $login_id ) : network_site_url( 'wp-login.php' );
                if ( false == $login_link ) {
                    $count_errors[] = 'login';
                }
                $count_setting++;
            }

            $show_logout = ! empty( $instance[ 'show_logout' ] ) ? $instance[ 'show_logout' ] : false;
            if ( $show_logout ) {
                $logout_id   = get_option( 'b3_logout_page_id' );
                $logout_link = ( false != $logout_id ) ? get_permalink( $logout_id ) : wp_logout_url();
                if ( false == $logout_link ) {
                    $count_errors[] = 'logout';
                }
                $count_setting++;
            }

            $show_register = ! empty( $instance[ 'show_register' ] ) ? $instance[ 'show_register' ] : false;
            if ( $show_register ) {
                $register_id   = get_option( 'b3_register_page_id' );
                $register_link = ( false != $register_id ) ? get_permalink( $register_id ) : network_site_url( 'wp-login.php?action=register' );
                if ( false == $register_link ) {
                    $count_errors[] = 'register';
                }
                $count_setting++;
            }

            if ( current_user_can( 'manage_options' ) ) {
                $show_settings = ! empty( $instance[ 'show_settings' ] ) ? $instance[ 'show_settings' ] : false;
                if ( false == $show_settings ) {
                    $count_errors[] = 'settings';
                }

                $show_user_approval = ! empty( $instance[ 'show_approval' ] ) ? $instance[ 'show_approval' ] : false;
                if ( $show_user_approval ) {
                    $approval_id   = get_option( 'b3_approval_page_id' );
                    $approval_link = ( false != $approval_id ) ? get_permalink( $approval_id ) : admin_url( '/admin.php?page=b3-user-approval' );
                    if ( false == $approval_link ) {
                        $count_errors[] = 'approval';
                    }
                    $count_setting++;
                }
            }

            if ( ! empty( $count_errors ) ) {
                if ( $count_setting == count( $count_errors ) ) {
                    $show_widget = false;
                    if ( current_user_can( 'manage_options' ) ) {
                        echo $args[ 'before_widget' ];
                        if ( ! empty( $instance[ 'title' ] ) ) {
                            echo $args[ 'before_title' ] . apply_filters( 'widget_title', $instance[ 'title' ] ) . $args[ 'after_title' ];
                        }
                        echo '<p class="widget-no-settings">' . sprintf( __( 'You haven\'t set any widget settings. Configure them <a href="%s">here</a>.', 'b3-onboarding' ), esc_url( admin_url( 'widgets.php' ) ) ) . '</p>';
                        echo $args[ 'after_widget' ];
                    }
                }
            }

            if ( true === $show_widget ) {
                echo $args[ 'before_widget' ];

                if ( ! empty( $instance[ 'title' ] ) ) {
                    echo $args[ 'before_title' ] . apply_filters( 'widget_title', $instance[ 'title' ] ) . $args[ 'after_title' ];
                }

                echo '<ul>';
                if ( ! is_user_logged_in() ) {
                    echo '<li><a href="' . $login_link . '">' . esc_html__( 'Login', 'b3-onboarding' ) . '</a></li>';
                    echo '<li><a href="' . $register_link . '">' . esc_html__( 'Register', 'b3-onboarding' ) . '</a></li>';
                } else {
                    if ( isset( $account_link ) && false != $account_link ) {
                        echo '<li><a href="' . $account_link . '">' . esc_html__( 'Account', 'b3-onboarding' ) . '</a></li>';
                    }
                    if ( true == $show_settings && current_user_can( 'manage_options' ) ) {
                        echo '<li><a href="' . admin_url( 'admin.php?page=b3-onboarding' ) . '">' . esc_html__( 'Settings', 'b3-onboarding' ) . '</a></li>';
                    }
                    if ( isset( $logout_link ) && false != $logout_link ) {
                        echo '<li><a href="' . $logout_link . '">' . esc_html__( 'Log Out', 'b3-onboarding' ) . '</a></li>';
                    }
                }
                echo '</ul>';

                echo $args[ 'after_widget' ];
            }
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form( $instance ) {
            $registration_type  = get_option( 'b3_registration_type' );
            $show_account       = ! empty( $instance[ 'show_account' ] ) ? $instance[ 'show_account' ] : '';
            $show_login         = ! empty( $instance[ 'show_login' ] ) ? $instance[ 'show_login' ] : '';
            $show_logout        = ! empty( $instance[ 'show_logout' ] ) ? $instance[ 'show_logout' ] : '';
            $show_register      = ! empty( $instance[ 'show_register' ] ) ? $instance[ 'show_register' ] : '';
            $show_settings      = ! empty( $instance[ 'show_settings' ] ) ? $instance[ 'show_settings' ] : '';
            $show_user_approval = ! empty( $instance[ 'show_approval' ] ) ? $instance[ 'show_approval' ] : '';
            $title              = ! empty( $instance[ 'title' ] ) ? $instance[ 'title' ] : esc_html__( 'User menu', 'b3-onboarding' );
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'b3-onboarding' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <b><?php esc_html_e( 'Logged Out', 'b3-onboarding' ); ?></b>
            <p>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_register' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_register' ) ); ?>" type="checkbox" value="1"<?php if ( 1 == $show_register ) { echo ' checked="checked"'; } ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'show_register' ) ); ?>"><?php esc_attr_e( 'Show register link', 'b3-onboarding' ); ?></label>
            </p>
            <p>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_login' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_login' ) ); ?>" type="checkbox" value="1"<?php if ( 1 == $show_login ) { echo ' checked="checked"'; } ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'show_login' ) ); ?>"><?php esc_attr_e( 'Show login link', 'b3-onboarding' ); ?></label>
            </p>
            <b><?php esc_html_e( 'Logged In', 'b3-onboarding' ); ?></b>
            <p>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_account' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_account' ) ); ?>" type="checkbox" value="1"<?php if ( 1 == $show_account ) { echo ' checked="checked"'; } ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'show_account' ) ); ?>"><?php esc_attr_e( 'Show account link', 'b3-onboarding' ); ?></label>
            </p>
            <p>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_logout' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_logout' ) ); ?>" type="checkbox" value="1"<?php if ( 1 == $show_logout ) { echo ' checked="checked"'; } ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'show_logout' ) ); ?>"><?php esc_attr_e( 'Show log out link', 'b3-onboarding' ); ?></label>
            </p>
            <b><?php esc_html_e( 'Admin only', 'b3-onboarding' ); ?></b>
            <?php if ( 'request_access' == $registration_type ) { ?>
                <p>
                    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_approval' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_approval' ) ); ?>" type="checkbox" value="1"<?php if ( 1 == $show_user_approval ) { echo ' checked="checked"'; } ?>>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'show_approval' ) ); ?>"><?php esc_attr_e( 'Show user approval link', 'b3-onboarding' ); ?></label>
                </p>
            <?php } ?>
            <p>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_settings' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_settings' ) ); ?>" type="checkbox" value="1"<?php if ( 1 == $show_settings ) { echo ' checked="checked"'; } ?>>
                <label for="<?php echo esc_attr( $this->get_field_id( 'show_settings' ) ); ?>"><?php esc_attr_e( 'Show settings link', 'b3-onboarding' ); ?></label>
            </p>
            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update( $new_instance, $old_instance ) {
            error_log($new_instance[ 'show_register' ]);
            $instance                    = array();
            $instance[ 'show_account' ]  = ( ! empty( $new_instance[ 'show_account' ] ) ) ? $new_instance[ 'show_account' ] : '';
            $instance[ 'show_approval' ] = ( ! empty( $new_instance[ 'show_approval' ] ) ) ? $new_instance[ 'show_approval' ] : '';
            $instance[ 'show_login' ]    = ( ! empty( $new_instance[ 'show_login' ] ) ) ? $new_instance[ 'show_login' ] : '';
            $instance[ 'show_logout' ]   = ( ! empty( $new_instance[ 'show_logout' ] ) ) ? $new_instance[ 'show_logout' ] : '';
            $instance[ 'show_settings' ] = ( ! empty( $new_instance[ 'show_settings' ] ) ) ? $new_instance[ 'show_settings' ] : '';
            $instance[ 'show_register' ] = ( ! empty( $new_instance[ 'show_register' ] ) ) ? $new_instance[ 'show_register' ] : '';
            $instance[ 'title' ]         = ( ! empty( $new_instance[ 'title' ] ) ) ? sanitize_text_field( $new_instance[ 'title' ] ) : '';

            return $instance;
        }
    }
