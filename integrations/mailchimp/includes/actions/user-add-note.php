<?php
/**
 * User Add Note
 *
 * @package     AutomatorWP\Integrations\Mailchimp\Actions\Add_Note
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_Mailchimp_User_Add_Note extends AutomatorWP_Integration_Action {

    public $integration = 'mailchimp';
    public $action = 'mailchimp_user_add_note';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_action( $this->action, array(
            'integration'       => $this->integration,
            'label'             => __( 'Add note to user', 'automatorwp-mailchimp' ),
            'select_option'     => __( 'Add <strong>note</strong> to <strong>user</strong>', 'automatorwp-mailchimp' ),
            /* translators: %1$s: Tag. */
            'edit_label'        => sprintf( __( 'Add note to user in %1$s', 'automatorwp-mailchimp' ), '{note}' ),
            /* translators: %1$s: Tag. */
            'log_label'         => sprintf( __( 'Add note to user in %1$s', 'automatorwp-mailchimp' ), '{note}' ),
            'options'           => array(
                'note' => array(
                    'from' => 'list',
                    'default' => __( 'audience', 'automatorwp-mailchimp' ),
                    'fields' => array(
                        'audience' => automatorwp_utilities_ajax_selector_field( array(
                            'option_none'       => false,
                            'option_custom'     => false,
                            'placeholder'       => __( 'Select an audience', 'automatorwp-mailchimp' ),
                            'name'              => __( 'Audience:', 'automatorwp-mailchimp' ),
                            'action_cb'         => 'automatorwp_mailchimp_get_lists',
                            'options_cb'        => 'automatorwp_mailchimp_options_cb_lists',
                            'default'           => ''
                        ) ),
                        'note' => array(
                            'placeholder'       => __( 'Write a note', 'automatorwp-mailchimp' ),
                            'name'              => __( 'Note:', 'automatorwp-mailchimp' ),
                            'desc'              => __( 'Note to publish in contact profile', 'automatorwp-mailchimp' ),
                            'type'              => 'textarea_small'
                        ),
                    ),
                ),
            ),
        ) );

    }

    /**
     * Action execution function
     *
     * @since 1.0.0
     *
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     */
    public function execute( $action, $user_id, $action_options, $automation ) {

        $this->result = '';

        // Shorthand
        $user = get_user_by ( 'ID', $user_id );
        $list_id = $action_options['audience'];
        $note = $action_options['note'];
        
        // Get object to connect to API
        $mailchimp = automatorwp_mailchimp_get_authorization();

        // Bail if no authorization
        if ( ! $mailchimp ){
            return;
        }

        try {

            $response = $mailchimp->lists->createListMemberNote( $list_id, $user->user_email, ['note' => $note] );

            $this->result = __( 'Note added', 'automatorwp-mailchimp' );
            
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            $decoded = json_decode($responseBodyAsString);
            $this->result = $decoded->title;
            return;

        }

    }

    /**
     * Register required hooks
     *
     * @since 1.0.0
     */
    public function hooks() {

        // Configuration notice
        add_filter( 'automatorwp_automation_ui_after_item_label', array( $this, 'configuration_notice' ), 10, 2 );

        // Log meta data
        add_filter( 'automatorwp_user_completed_action_log_meta', array( $this, 'log_meta' ), 10, 5 );

        // Log fields
        add_filter( 'automatorwp_log_fields', array( $this, 'log_fields' ), 10, 5 );

        parent::hooks();

    }

    /**
     * Configuration notice
     *
     * @since 1.0.0
     *
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The object type (trigger|action)
     */
    public function configuration_notice( $object, $item_type ) {

        // Bail if action type don't match this action
        if( $item_type !== 'action' ) {
            return;
        }

        if( $object->type !== $this->action ) {
            return;
        }

        // Warn user if the authorization has not been setup from settings
        if( ! automatorwp_mailchimp_get_authorization() ) : ?>
            <div class="automatorwp-notice-warning" style="margin-top: 10px; margin-bottom: 0;">
                <?php echo sprintf(
                    __( 'You need to configure the <a href="%s" target="_blank">Mailchimp settings</a> to get this action to work.', 'automatorwp-mailchimp' ),
                    get_admin_url() . 'admin.php?page=automatorwp_settings&tab=opt-tab-mailchimp'
                ); ?>
                <?php echo sprintf(
                    __( '<a href="%s" target="_blank">Documentation</a>', 'automatorwp-mailchimp' ),
                    'https://automatorwp.com/docs/mailchimp/'
                ); ?>
            </div>
        <?php endif;

    }

    /**
     * Action custom log meta
     *
     * @since 1.0.0
     *
     * @param array     $log_meta           Log meta data
     * @param stdClass  $action             The action object
     * @param int       $user_id            The user ID
     * @param array     $action_options     The action's stored options (with tags already passed)
     * @param stdClass  $automation         The action's automation object
     *
     * @return array
     */
    public function log_meta( $log_meta, $action, $user_id, $action_options, $automation ) {

        // Bail if action type don't match this action
        if( $action->type !== $this->action ) {
            return $log_meta;
        }

        // Store the action's result
        $log_meta['result'] = $this->result;

        return $log_meta;
    }

    /**
     * Action custom log fields
     *
     * @since 1.0.0
     *
     * @param array     $log_fields The log fields
     * @param stdClass  $log        The log object
     * @param stdClass  $object     The trigger/action/automation object attached to the log
     *
     * @return array
     */
    public function log_fields( $log_fields, $log, $object ) {

        // Bail if log is not assigned to an action
        if( $log->type !== 'action' ) {
            return $log_fields;
        }

        // Bail if action type don't match this action
        if( $object->type !== $this->action ) {
            return $log_fields;
        }

        $log_fields['result'] = array(
            'name' => __( 'Result:', 'automatorwp-mailchimp' ),
            'type' => 'text',
        );

        return $log_fields;
    }

}

new AutomatorWP_Mailchimp_User_Add_Note();