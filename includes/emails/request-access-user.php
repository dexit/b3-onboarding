<?php
    $request_access_email_subject_user   = get_option( 'b3_request_access_subject_user', false );
    $request_access_email_message_user   = get_option( 'b3_request_access_message_user', false );
?>
<table class="b3_table b3_table--emails" border="0" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td colspan="2">
            <?php esc_html_e( 'If any field is left empty the placeholder will be used.', 'b3-onboarding' ); ?>
        </td>
    </tr>
    <tr>
        <th>
            <label for="b3__input--request-access-subject-user" class=""><?php esc_html_e( 'Email subject', 'b3-onboarding' ); ?></label>
        </th>
        <td>
            <input class="" id="b3__input--request-access-subject-user" name="b3_request_access_subject_user" placeholder="<?php echo b3_request_access_subject_user(); ?>" type="text" value="<?php echo $request_access_email_subject_user; ?>" />
        </td>
    </tr>
    <tr>
        <th class="align-top">
            <label for="b3__input--request-access-message-user" class=""><?php esc_html_e( 'Email message', 'b3-onboarding' ); ?></label>
            <br /><br />
            <?php echo sprintf( __( '<a href="%s" target="_blank" rel="noopener">Preview</a>', 'b3-onboarding' ), esc_url( B3_PLUGIN_SETTINGS . '&preview=request-access-user' ) ); ?>
        </th>
        <td>
            <textarea id="b3__input--request-access-message-user" name="b3_request_access_message_user" placeholder="<?php echo esc_textarea( b3_request_access_message_user() ); ?>" rows="4"><?php echo $request_access_email_message_user; ?></textarea>
        </td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td>
            <input class="button button-primary" type="submit" value="<?php esc_html_e( 'Save settings', 'b3-onboarding' ); ?>" />
        </td>
    </tr>
    </tbody>
</table>