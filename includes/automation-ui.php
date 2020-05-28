<?php
/**
 * Automation UI
 *
 * @package     AutomatorWP\Automation_UI
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Automation UI meta boxes
 *
 * @since  1.0.0
 */
function automatorwp_automation_ui_add_meta_boxes() {

    add_meta_box( 'automatorwp_triggers', __( 'Triggers', 'automatorwp' ), 'automatorwp_automation_ui_triggers_meta_box', 'automatorwp_automations', 'normal', 'default' );
    add_meta_box( 'automatorwp_actions', __( 'Actions', 'automatorwp' ), 'automatorwp_automation_ui_actions_meta_box', 'automatorwp_automations', 'normal', 'default' );

}
add_action( 'add_meta_boxes', 'automatorwp_automation_ui_add_meta_boxes' );

/**
 * Renders the triggers meta box
 *
 * @since  1.0.0
 *
 * @param stdClass $automation The automation object
 * @param string   $type       Type to render form
 */
function automatorwp_automation_ui_triggers_meta_box( $automation, $type ) {

    $triggers = automatorwp_get_automation_triggers( $automation->id );

    ?>
    <div class="automatorwp-title"><?php _e( 'Triggers', 'automatorwp' ); ?></div>
    <div class="automatorwp-subtitle"><?php _e( 'When this happens...', 'automatorwp' ); ?></div>

    <div class="automatorwp-sequential-field cmb2-switch">
        <label for="sequential"><?php _e( 'Sequential', 'automatorwp' ); ?></label>
        <div class="cmb-td">
            <input type="checkbox" id="sequential" name="sequential" value="1" <?php checked( $automation->sequential, 1 ); ?> />
            <label for="sequential"><span class="cmb2-metabox-description"><?php _e( 'Check this option to force users to complete triggers in order.', 'automatorwp' ); ?></span></label>
        </div>
    </div>

    <div class="automatorwp-automation-items automatorwp-triggers">

        <?php foreach( $triggers as $trigger ) : ?>

            <?php automatorwp_automation_item_edit_html( $trigger, 'trigger', $automation ); ?>

        <?php endforeach; ?>
    </div>

    <?php automatorwp_automation_ui_add_item_form( 'trigger' ); ?>

    <button type="button" class="button automatorwp-add-trigger"><?php _e( 'Add Trigger', 'automatorwp' ); ?></button>
    <?php
}

/**
 * Renders the actions meta box
 *
 * @since  1.0.0
 *
 * @param stdClass $automation The automation object
 * @param string   $type       Type to render form
 */
function automatorwp_automation_ui_actions_meta_box( $automation, $type ) {

    $actions = automatorwp_get_automation_actions( $automation->id );

    ?>
    <div class="automatorwp-title"><?php _e( 'Actions', 'automatorwp' ); ?></div>
    <div class="automatorwp-subtitle"><?php _e( 'Do this...', 'automatorwp' ); ?></div>

    <div class="automatorwp-automation-items automatorwp-actions">

        <?php foreach( $actions as $action ) : ?>

            <?php automatorwp_automation_item_edit_html( $action, 'action', $automation ); ?>

        <?php endforeach; ?>
    </div>

    <?php automatorwp_automation_ui_add_item_form( 'action' ); ?>

    <button type="button" class="button automatorwp-add-action"><?php _e( 'Add Action', 'automatorwp' ); ?></button>
    <?php
}

/**
 * Automation UI add item form
 *
 * @since 1.0.0
 *
 * @param string $item_type The item type (trigger|action)
 */
function automatorwp_automation_ui_add_item_form( $item_type ) {

    ?>

    <div class="automatorwp-add-item-form" style="display: none;">

        <div class="automatorwp-automation-item-details">
            <div class="automatorwp-integration-icon"></div>
        </div>

        <div class="automatorwp-automation-item-content">

            <div class="automatorwp-select-integration">

                <div class="automatorwp-select-integration-label"><?php _e( 'Select an integration', 'automatorwp' ); ?></div>

                <div class="automatorwp-integrations">

                    <?php foreach( AutomatorWP()->integrations as $integration => $args ) : ?>

                        <?php
                        switch ( $item_type ) {
                            case 'trigger':
                                $choices = automatorwp_get_integration_triggers( $integration );
                                break;
                            case 'action':
                                $choices = automatorwp_get_integration_actions( $integration );
                                break;
                            default:
                                $choices = array();
                                break;
                        }

                        // Skip integrations without triggers or actions
                        if( empty( $choices ) ) {
                            continue;
                        } ?>

                        <div class="automatorwp-integration"
                             data-integration="<?php echo esc_attr( $integration ); ?>"
                             data-label="<?php echo esc_attr( $args['label'] ); ?>"
                             data-icon="<?php echo esc_attr( $args['icon'] ); ?>">
                            <div class="automatorwp-integration-icon">
                                <img src="<?php echo esc_attr( $args['icon'] ); ?>" alt="<?php echo esc_attr( $args['label'] ); ?>">
                            </div>
                            <div class="automatorwp-integration-label"><?php echo $args['label']; ?></div>
                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <div class="automatorwp-integration-choices-container" style="display: none;">

                <?php if ( $item_type === 'trigger' ) : ?>

                    <div class="automatorwp-select-trigger-label"><?php _e( 'Select a trigger', 'automatorwp' ); ?></div>

                    <?php foreach( AutomatorWP()->integrations as $integration => $args ) : ?>

                        <select class="automatorwp-integration-choices"
                                data-integration="<?php echo esc_attr( $integration ); ?>"
                                data-placeholder="<?php echo esc_attr( __( 'Search for triggers', 'automatorwp' ) ); ?>"
                                style="display: none;">

                                <option value=""></option>
                            <?php foreach( automatorwp_get_integration_triggers( $integration ) as $trigger => $args ) : ?>
                                <option value="<?php echo esc_attr( $trigger ); ?>" data-text="<?php echo esc_attr( $args['select_option'] ); ?>"><?php echo $args['label']; ?></option>
                            <?php endforeach; ?>

                        </select>

                    <?php endforeach; ?>

                <?php elseif ( $item_type === 'action' ) : ?>

                    <?php foreach( AutomatorWP()->integrations as $integration => $args ) : ?>

                        <select class="automatorwp-integration-choices"
                                data-integration="<?php echo esc_attr( $integration ); ?>"
                                data-placeholder="<?php echo esc_attr( __( 'Search for actions', 'automatorwp' ) ); ?>"
                                style="display: none;">

                            <option value=""></option>
                            <?php foreach( automatorwp_get_integration_actions( $integration ) as $action => $args ) : ?>
                                <option value="<?php echo esc_attr( $action ); ?>" data-text="<?php echo esc_attr( $args['select_option'] ); ?>"><?php echo $args['label']; ?></option>
                            <?php endforeach; ?>

                        </select>

                    <?php endforeach; ?>

                <?php endif; ?>

                <button type="button" class="button automatorwp-cancel-choice-select"><?php _e( 'Cancel', 'automatorwp' ); ?></button>

                <div class="automatorwp-spinner" style="display: none;">
                    <span class="spinner is-active"></span>
                    <span class="spinner-label"><?php _e( 'Saving...', 'automatorwp' ); ?></span>
                </div>

            </div>

            <?php automatorwp_automation_ui_integrations_recommendations( $item_type ); ?>

        </div>

    </div>

    <?php

}

/**
 * Get the object type args
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 *
 * @return array|false
 */
function automatorwp_automation_item_type_args( $object, $item_type ) {

    $type_args = array();

    if( $item_type === 'trigger' ) {
        $type_args = automatorwp_get_trigger( $object->type );
    } else if( $item_type === 'action' ) {
        $type_args = automatorwp_get_action( $object->type );
    }

    return $type_args;

}

/**
 * Renders the trigger/action edit HTML
 *
 * @since  1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param stdClass  $automation The automation object
 */
function automatorwp_automation_item_edit_html( $object, $item_type, $automation ) {

    // Check item type
    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        return;
    }

    // Check type args
    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        automatorwp_automation_missing_integration_item_edit_html( $object, $item_type, $automation );
        return;
    }

    // Check integration
    $integration = automatorwp_get_integration( $type_args['integration'] );

    if( ! $integration ) {
        automatorwp_automation_missing_integration_item_edit_html( $object, $item_type, $automation );
        return;
    }

    ?>
    <div id="automatorwp-item-<?php echo esc_attr( $object->id ); ?>" class="automatorwp-automation-item automatorwp-<?php echo esc_attr( $item_type ); ?>">

        <div class="automatorwp-automation-item-details">
            <div class="automatorwp-integration-icon">
                <img src="<?php echo esc_attr( $integration['icon'] ); ?>" title="<?php echo esc_attr( $integration['label'] ); ?>" alt="<?php echo esc_attr( $integration['label'] ); ?>">
            </div>
        </div>

        <div class="automatorwp-automation-item-content">

            <div class="automatorwp-automation-item-actions">
                <div class="automatorwp-automation-item-action automatorwp-automation-item-action-delete" title="<?php echo esc_attr( __( 'Delete', 'automatorwp') ); ?>"><span class="dashicons dashicons-trash"></span></div>
            </div>

            <div class="automatorwp-integration-label"><?php echo $integration['label']; ?></div>

            <div class="automatorwp-automation-item-position" style="<?php echo ( $automation->sequential ? '' : 'display: none;' ); ?>"><?php echo $object->position + 1; ?>.</div>
            <div class="automatorwp-automation-item-label"><?php echo automatorwp_parse_automation_item_edit_label( $object, $item_type ); ?></div>

            <?php
            /**
             * After item label
             *
             * @since 1.0.0
             *
             * @param stdClass  $object     The trigger/action object
             * @param string    $item_type  The object type (trigger|action)
             */
            do_action( 'automatorwp_automation_ui_after_item_label', $object, $item_type ); ?>

            <?php // Render the options form ?>
            <?php foreach( $type_args['options'] as $option => $args ) : ?>

                <div class="automatorwp-option-form-container" data-option="<?php echo esc_attr( $option ); ?>" data-from="<?php echo esc_attr( ( isset( $args['from'] ) ? $args['from'] : '' ) ); ?>" style="display: none;">

                    <?php
                    /**
                     * After option from
                     *
                     * @since 1.0.0
                     *
                     * @param stdClass  $object     The trigger/action object
                     * @param string    $item_type  The object type (trigger|action)
                     * @param string    $option     The option key
                     * @param array     $args       The option arguments
                     */
                    do_action( 'automatorwp_automation_ui_after_option_form', $object, $item_type, $option, $args ); ?>

                    <?php
                    // Get the option form
                    $cmb2 = automatorwp_get_automation_item_option_form( $object, $item_type, $option, $automation );

                    if( $cmb2 ) {

                        ct_setup_table( "automatorwp_{$item_type}s" );

                        // Render the form
                        CMB2_Hookup::enqueue_cmb_css();
                        CMB2_Hookup::enqueue_cmb_js();
                        $cmb2->show_form();

                        ct_reset_setup_table();
                    }
                    ?>

                    <?php
                    /**
                     * Before option from
                     *
                     * @since 1.0.0
                     *
                     * @param stdClass  $object     The trigger/action object
                     * @param string    $item_type  The object type (trigger|action)
                     * @param string    $option     The option key
                     * @param array     $args       The option arguments
                     */
                    do_action( 'automatorwp_automation_ui_before_option_form', $object, $item_type, $option, $args ); ?>

                    <button type="button" class="button button-primary automatorwp-save-option-form"><?php _e( 'Save', 'automatorwp' ); ?></button>
                    <button type="button" class="button automatorwp-cancel-option-form"><?php _e( 'Cancel', 'automatorwp' ); ?></button>

                    <div class="automatorwp-spinner" style="display: none;">
                        <span class="spinner is-active"></span>
                        <span class="spinner-label"><?php _e( 'Saving...', 'automatorwp' ); ?></span>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <?php // Hidden fields ?>
        <input type="hidden" class="id" value="<?php echo esc_attr( $object->id ); ?>"/>
        <input type="hidden" class="type" value="<?php echo esc_attr( $object->type ); ?>"/>
        <input type="hidden" class="status" value="<?php echo esc_attr( $object->status ); ?>"/>
        <input type="hidden" class="position" value="<?php echo esc_attr( $object->position ); ?>"/>

    </div>
    <?php

}

/**
 * Renders the trigger/action missing integration edit HTML
 *
 * @since  1.1.2
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param stdClass  $automation The automation object
 */
function automatorwp_automation_missing_integration_item_edit_html( $object, $item_type, $automation ) {

    $warning_message = '';

    if( $item_type === 'trigger' ) {
        $warning_message = __( 'Trigger disabled because plugin associated couldn\'t be found. Please, re-install the plugin associated or remove this trigger.', 'automatorwp' );
    } else {
        $warning_message = __( 'Action disabled because plugin associated couldn\'t be found. Please, re-install the plugin associated or remove this action.', 'automatorwp' );

    }

    ?>
    <div id="automatorwp-item-<?php echo esc_attr( $object->id ); ?>" class="automatorwp-automation-item automatorwp-automation-missing-integration-item automatorwp-<?php echo esc_attr( $item_type ); ?>">

        <div class="automatorwp-automation-item-details">
            <div class="automatorwp-integration-icon">
                <img src="<?php echo esc_attr( AUTOMATORWP_URL . 'assets/img/integration-missing.svg' ); ?>" title="<?php echo esc_attr( __( 'Missing plugin', 'automatorwp' ) ); ?>">
            </div>
        </div>

        <div class="automatorwp-automation-item-content">

            <div class="automatorwp-automation-item-actions">
                <div class="automatorwp-automation-item-action automatorwp-automation-item-action-delete" title="<?php echo esc_attr( __( 'Delete', 'automatorwp') ); ?>"><span class="dashicons dashicons-trash"></span></div>
            </div>

            <div class="automatorwp-integration-label"><?php echo __( 'Missing plugin', 'automatorwp' ); ?></div>

            <div class="automatorwp-automation-item-position" style="<?php echo ( $automation->sequential ? '' : 'display: none;' ); ?>"><?php echo $object->position + 1; ?>.</div>
            <div class="automatorwp-automation-item-label"><?php echo $object->title; ?></div>

            <div class="automatorwp-notice-warning"><?php echo $warning_message; ?></div>

            <?php
            /**
             * After missing integration item label
             *
             * @since 1.1.2
             *
             * @param stdClass  $object     The trigger/action object
             * @param string    $item_type  The object type (trigger|action)
             */
            do_action( 'automatorwp_automation_ui_after_missing_integration_item_label', $object, $item_type ); ?>

        </div>

        <?php // Hidden fields ?>
        <input type="hidden" class="id" value="<?php echo esc_attr( $object->id ); ?>"/>
        <input type="hidden" class="type" value="<?php echo esc_attr( $object->type ); ?>"/>
        <input type="hidden" class="status" value="<?php echo esc_attr( $object->status ); ?>"/>
        <input type="hidden" class="position" value="<?php echo esc_attr( $object->position ); ?>"/>

    </div>
    <?php

}

/**
 * Parses the trigger/action edit label
 *
 * @since  1.0.0
 *
 * @param stdClass  $object     The trigger object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_parse_automation_item_edit_label( $object, $item_type, $context = 'edit' ) {

    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return '';
    }

    /**
     * Filter to dynamically change the edit label
     *
     * @since 1.0.0
     *
     * @param string    $label      The edit label
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param string    $context    The context this function is executed
     * @param array     $type_args  The type parameters
     *
     * @return string
     */
    $label = apply_filters( 'automatorwp_parse_automation_item_edit_label', $type_args['edit_label'], $object, $item_type, $context, $type_args );

    return automatorwp_parse_automation_item_label( $object, $item_type, $label, $context );

}

/**
 * Parses the trigger/action log label
 *
 * @since  1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_parse_automation_item_log_label( $object, $item_type, $context = 'edit' ) {

    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return '';
    }

    /**
     * Filter to dynamically change the log label
     *
     * @since 1.0.0
     *
     * @param string    $label      The edit label
     * @param stdClass  $object     The trigger/action object
     * @param string    $item_type  The item type (trigger|action)
     * @param string    $context    The context this function is executed
     * @param array     $type_args  The type parameters
     *
     * @return string
     */
    $label = apply_filters( 'automatorwp_parse_automation_item_log_label', $type_args['log_label'], $object, $item_type, $context, $type_args );

    return automatorwp_parse_automation_item_label( $object, $item_type, $label, $context );

}

/**
 * Parses the trigger/action label given
 *
 * @since  1.0.0
 *
 * @param stdClass  $object     The trigger object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $label      The label to parse
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_parse_automation_item_label( $object, $item_type, $label, $context = 'edit' ) {

    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return '';
    }

    $replacements = array();

    foreach( $type_args['options'] as $option => $args ) {
        $replacements['{' . $option . '}'] = automatorwp_get_automation_item_option_replacement( $object, $item_type, $option, $context );
    }

    /**
     * Trigger/action label replacements
     *
     * @since 1.0.0
     *
     * @param array     $replacements   The replacements to apply
     * @param stdClass  $object         The trigger object
     * @param string    $item_type      The item type (trigger|action)
     * @param string    $label          The label to parse
     * @param string    $context        The context this function is executed
     *
     * @return array
     */
    $replacements = apply_filters( 'automatorwp_parse_automation_item_label_replacements', $replacements, $object, $item_type, $label, $context );

    $tags = array_keys( $replacements );

    $label_parsed = str_replace( $tags, $replacements, $label );

    /**
     * Trigger/action label parsed
     *
     * @since 1.0.0
     *
     * @param string    $label_parsed   The label parsed
     * @param stdClass  $object         The trigger object
     * @param string    $item_type      The item type (trigger|action)
     * @param string    $label          The originallabel to parse
     * @param string    $context        The context this function is executed
     * @param array     $tags           The tags applied
     * @param array     $replacements   The replacements applied
     *
     * @return string
     */
    return apply_filters( 'automatorwp_parse_automation_item_label', $label_parsed, $object, $item_type, $label, $context, $tags, $replacements );

}

/**
 * Get the option replacement
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $option     The option name
 * @param string    $context    The context this function is executed
 *
 * @return string
 */
function automatorwp_get_automation_item_option_replacement( $object, $item_type, $option, $context = 'edit' ) {

    // Check item type
    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        return false;
    }

    // Check type args
    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return '';
    }

    // Bail if this type hasn't any option
    if( ! isset( $type_args['options'][$option] ) ) {
        return '';
    }

    $option_args = $type_args['options'][$option];

    $field_id = ( isset( $option_args['from'] ) ? $option_args['from'] : '' );

    // If not isset the from field, try to return a default value
    if( ! isset( $option_args['fields'][$field_id] ) ) {

        $default = '';

        if( isset( $option_args['default'] ) && ! empty( $option_args['default'] ) ) {
            $default = $option_args['default'];
        }

        if( $context === 'edit' ) {
            $default = '<span class="button button-primary automatorwp-option" data-option="' . $option . '">' . $default . '</span>';
        }

        return $default;


    }

    ct_setup_table( "automatorwp_{$item_type}s" );

    $field = $option_args['fields'][$field_id];
    $value = ct_get_object_meta( $object->id, $field_id, true );

    if( empty( $value ) && isset( $field['default'] ) ) {
        $value = $field['default'];
    }

    // Select field
    if( $field['type'] === 'select' ) {

        $options = array();

        // Try to get the field options from field args
        if( isset( $field['options'] ) ) {
            $options = $field['options'];
        } else if( isset( $field['options_cb'] ) && is_callable( $field['options_cb'] ) ) {

            $field['value'] = $value;
            $field['escaped_value'] = $value;
            $field['args'] = $field;

            $options = call_user_func( $field['options_cb'], (object) $field );
        }

        if( isset( $options[$value] ) ) {
            $value = $options[$value];
        }
    }

    // Fallback to default option if exists
    if( empty( $value ) && isset( $option_args['default'] ) && ! empty( $option_args['default'] ) ) {
        $value = $option_args['default'];
    }

    if( $context === 'edit' ) {
        $value = '<span class="button button-primary automatorwp-option" data-option="' . $option . '">' . $value . '</span>';
    }

    ct_reset_setup_table();

    return $value;

}

/**
 * Gets a CMB2 object from a trigger option
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $option     Option form to render
 * @param stdClass  $automation The automation object
 *
 * @return CMB2|false
 */
function automatorwp_get_automation_item_option_form( $object, $item_type, $option, $automation ) {

    // Check item type
    if( ! in_array( $item_type, array( 'trigger', 'action' ) ) ) {
        return false;
    }

    // Check type args
    $type_args = automatorwp_automation_item_type_args( $object, $item_type );

    if( ! $type_args ) {
        return false;
    }

    // Bail if this type hasn't any option
    if( ! isset( $type_args['options'][$option] ) ) {
        return false;
    }

    $args = $type_args['options'][$option];

    ct_setup_table( "automatorwp_{$item_type}s" );

    // Setup the CMB2 form
    $cmb2 = new CMB2( array(
        'id'        => $option .'_form',
        'object_types' => array( 'automatorwp_triggers', 'automatorwp_actions' ),
        'classes'   => 'automatorwp-form automatorwp-option-form',
        'hookup'    => false,
    ), $object->id );

    // Setup the options fields
    foreach ( $args['fields'] as $field_id => $field ) {

        $field['id'] = $field_id;

        if( $field['type'] === 'group' ) {
            // Group fields

            // Setup field arguments on each group field
            foreach ( $field['fields'] as $field_group_id => $field_group ) {

                $field_group['id'] = $field_group_id;

                $field['fields'][$field_group_id] = automatorwp_automation_item_option_field_args( $object, $item_type, $option, $automation, $field_id . '[' .$field_group_id . ']', $field_group );

            }

        } else {
            // Single fields

            $field = automatorwp_automation_item_option_field_args( $object, $item_type, $option, $automation, $field_id, $field );

        }

        // Add the field to the form
        $cmb2->add_field( $field );

    }

    ct_reset_setup_table();

    return $cmb2;

}

/**
 * Gets a CMB2 object from a trigger option
 *
 * @since 1.0.0
 *
 * @param stdClass  $object     The trigger/action object
 * @param string    $item_type  The item type (trigger|action)
 * @param string    $option     Option form to render
 * @param stdClass  $automation The automation object
 *
 * @return array
 */
function automatorwp_automation_item_option_field_args( $object, $item_type, $option, $automation, $field_id, $field ) {

    $repeatable = ( isset( $field['repeatable'] ) && $field['repeatable'] === true );

    // Prevent to render field names to avoid conflicts on the main form
    $field['attributes']['name'] = '';

    // Update id attribute to avoid id collisions
    $field['attributes']['id'] = $field_id . '-' . $object->id;
    $field['attributes']['data-option'] = $field_id . ( $repeatable ? '[]' : '' );

    // Setup the fields tags selector
    if( $item_type === 'action' ) {

        // Check if field type is compatible with tags selector
        if( in_array( $field['type'], array( 'text', 'textarea', 'wysiwyg' ) ) ) {
            $field['after_field'] = automatorwp_get_tags_selector_html( $automation->id );
        }

    }

    return $field;

}

/**
 * Automation UI add-ons recommendations
 *
 * @since 1.1.2
 *
 * @param string $item_type The item type (trigger|action)
 */
function automatorwp_automation_ui_integrations_recommendations( $item_type ) {

    $integrations = automatorwp_get_recommended_integrations();

    // If not recommendations, show a generic message
    if ( is_wp_error( $integrations ) ||  empty( $integrations ) ) { ?>

        <div class="automatorwp-more-integrations">
            <span><?php if ( $item_type === 'trigger' ) : _e( 'Looking for more triggers?', 'automatorwp' ); elseif ( $item_type === 'action' ) : _e( 'Looking for more actions?', 'automatorwp' ); endif; ?></span>
            <a href="https://automatorwp.com/add-ons/" target="_blank"><?php _e( 'View all add-ons', 'automatorwp' ); ?></a>
        </div>

        <?php
        return;
    }
    ?>

    <div class="automatorwp-recommended-integrations">

        <div class="automatorwp-recommended-integrations-label">
            <span><?php printf( _n( '%d plugin of your site can be connected with AutomatorWP.', '%d plugins of your site can be connected with AutomatorWP.', count( $integrations ), 'automatorwp' ), count( $integrations ) ); ?></span>
            <a href="#"><?php _e( 'View plugins', 'automatorwp' ); ?></a>
        </div>

        <div class="automatorwp-integrations" style="display: none;">

            <?php foreach ( $integrations as $integration ) :

                // Setup the triggers and actions information
                $triggers_and_actions = array();

                if( count( $integration->triggers ) ) {
                    $triggers_and_actions[] = sprintf( _n( '%d trigger', '%d triggers', count( $integration->triggers ), 'automatorwp' ), count( $integration->triggers ) );
                }

                if( count( $integration->actions ) ) {
                    $triggers_and_actions[] = sprintf( _n( '%d action', '%d actions', count( $integration->actions ), 'automatorwp' ), count( $integration->actions ) );
                }

                // Setup the add-on slug for the add-on URL
                $slug = str_replace( '_', '-', $integration->code ); ?>

                <a class="automatorwp-integration"
                     href="https://automatorwp.com/add-ons/<?php echo $slug; ?>/"
                     target="_blank"
                     data-integration="<?php echo esc_attr( $integration->code ); ?>"
                     data-label="<?php echo esc_attr( $integration->title ); ?>"
                     data-icon="<?php echo esc_attr( $integration->icon ); ?>">
                    <div class="automatorwp-integration-icon">
                        <img src="<?php echo esc_attr( $integration->icon ); ?>" alt="<?php echo esc_attr( $integration->title ); ?>">
                    </div>
                    <div class="automatorwp-integration-label"><?php echo $integration->title; ?></div>
                    <div class="automatorwp-integration-triggers-and-actions"><?php echo implode( ', ', $triggers_and_actions ); ?></div>
                </a>

            <?php endforeach; ?>

        </div>

    </div>

    <?php

}

/**
 * Get recommended integrations
 *
 * @since 1.1.2
 *
 * @return array|WP_Error Object with recommended integrations
 */
function automatorwp_get_recommended_integrations() {

    $integrations = automatorwp_integrations_api();

    if( is_wp_error( $integrations ) ) {
        return $integrations;
    }

    $recommended_integrations = array();

    foreach ( $integrations as $integration ) {

        // Skip integration if can't determine its class
        if( empty( $integration->integration_class ) ) {
            continue;
        }

        // Skip integration if already installed
        if( class_exists( $integration->integration_class ) ) {
            continue;
        }

        // Skip integration if free version already installed
        if( class_exists( $integration->integration_class . '_Integration' ) ) {
            continue;
        }

        // Skip integration if hasn't defined any way to meet if plugin is installed
        if( empty( $integration->required_class )
            && empty( $integration->required_function )
            && empty( $integration->required_constant ) ) {
            continue;
        }

        // Skip if integrated plugin is not installed
        if( ! empty( $integration->required_class ) && ! class_exists( $integration->required_class ) ) {
            continue;
        }

        // Skip if integrated plugin is not installed
        if( ! empty( $integration->required_function ) && ! function_exists( $integration->required_function ) ) {
            continue;
        }

        // Skip if integrated plugin is not installed
        if( ! empty( $integration->required_constant ) && ! defined( $integration->required_constant ) ) {
            continue;
        }

        $recommended_integrations[] = $integration;

    }

    return $recommended_integrations;

}

/**
 * Function to contact with the AutomatorWP integrations API
 *
 * @since  1.1.2
 *
 * @return object|WP_Error Object with AutomatorWP integrations
 */
function automatorwp_integrations_api() {

    // If a integrations api request has been cached already, then use cached integrations
    if ( false !== ( $res = get_transient( 'automatorwp_integrations_api' ) ) ) {
        return $res;
    }

    $url = $http_url = 'http://automatorwp.com/wp-json/automatorwp/integrations';

    if ( $ssl = wp_http_supports( array( 'ssl' ) ) ) {
        $url = set_url_scheme( $url, 'https' );
    }

    $http_args = array(
        'timeout' => 15,
    );

    $request = wp_remote_get( $url, $http_args );

    if ( $ssl && is_wp_error( $request ) ) {
        trigger_error(
            sprintf(
                __( 'An unexpected error occurred. Something may be wrong with automatorwp.com or this server&#8217;s configuration. If you continue to have problems, please try to <a href="%s">contact us</a>.', 'automatorwp' ),
                'https://automatorwp.com/contact-us/'
            ) . ' ' . __( '(WordPress could not establish a secure connection to automatorwp.com. Please contact your server administrator.)' ),
            headers_sent() || WP_DEBUG ? E_USER_WARNING : E_USER_NOTICE
        );

        $request = wp_remote_get( $http_url, $http_args );
    }

    if ( is_wp_error( $request ) ) {
        $res = new WP_Error( 'automatorwp_integrations_api_failed',
            sprintf(
                __( 'An unexpected error occurred. Something may be wrong with automatorwp.com or this server&#8217;s configuration. If you continue to have problems, please try to <a href="%s">contact us</a>.', 'automatorwp' ),
                'https://automatorwp.com/contact-us/'
            ),
            $request->get_error_message()
        );
    } else {
        $res = json_decode( $request['body'] );

        $res = (array) $res;

        // Set a transient for 1 week with api integrations
        set_transient( 'automatorwp_integrations_api', $res, ( 24 * 7 ) * HOUR_IN_SECONDS );
    }

    return $res;

}