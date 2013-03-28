<?php
/*
Plugin Name: Skyscraper.io
Version: 0.1
Description: Include skyscraper.io script in your blog withoit effor
Author: Skyscraper
Author URI: http://www.skyscraper.io
*/

define( 'SKYSCRPR_MIN_WORDPRESS_REQUIRED', "2.7" );
define( 'SKYSCRPR_WORDPRESS_VERSION_SUPPORTED', version_compare( get_bloginfo( "version" ), SKYSCRPR_MIN_WORDPRESS_REQUIRED, ">=" ) );
define( 'SKYSCRPR_ENABLED', SKYSCRPR_WORDPRESS_VERSION_SUPPORTED && skyscrpr_validate_option( 'skyscrpr_site_id' ) );

/**
 * Print the Skyscraper <script/> tags
 */
function skyscrpr_script() {
  $skyscrpr_site_id = get_option( "skyscrpr_site_id" );
  if( $skyscrpr_site_id ) {
?>
  <!-- Skyscraper.io -->
        <script type="text/javascript" id="skyscrpr-config-script">
          var skyscrprSettings = {
            site_id: '<?php print addslashes( $skyscrpr_site_id ); ?>',
          };
        </script>

        <script>
          (function() {
            function async_load() {
              var scr = document.createElement('script');
              scr.type = 'text/javascript';
              scr.async = true;
              scr.src = '//install-skyscrpr-com.s3.amazonaws.com/skyscrpr.js';
              var a = document.getElementsByTagName('script')[0];
              a.parentNode.insertBefore(scr, a);
            }
            if (window.attachEvent) {
              window.attachEvent('onload', async_load);
            } else {
              window.addEventListener('load', async_load, false);
            }
          })();
        </script>
  <!-- end Skyscraper.io -->
<?php
  }
}

/**
 * Print the VigLink plugin settings page
 */
function skyscrpr_options() { ?>
  <div class="wrap">
    <div class="icon32">&nbsp;</div>
    <h2>Skyscraper.io Settings</h2>
<?php
  if( ! SKYSCRPR_WORDPRESS_VERSION_SUPPORTED ) {
?>
    <p style="width: 50%;">
      Thanks for your interest in Skyscraper!  Unfortunately, the VigLink plugin
      requires WordPress <?php print SKYSCRPR_MIN_WORDPRESS_REQUIRED ?> or newer.
      Please try again once you've upgraded.
    </p>
<?php
  }
?>
    <p class="instructions">
      Copy your SiteId skyscrpr_site_id from
      <a href="http://www.skyscraper.io/sites">skyscraper.io</a> and paste it
      below.
    </p>

    <form method="post" action="options.php">
      <?php settings_fields( "skyscrpr" ); ?>
      <table class="form-table" style="width: auto;">
        <tr valign="top">
          <th style="width: auto;">Site ID</th>
          <td>
            <input id="skyscrpr-skyscrpr_site_id" type="text" name="skyscrpr_site_id" value="<?php print get_option( "skyscrpr_site_id" ); ?>" class="regular-text"/>
            <span class="description">Required</span>
          </td>
        </tr>
      </table>
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        <span class="reminder" style="display: none;">&larr; Don't forget to save!</span>
      </p>
    </form>
  </div>
<?php
}

/**
 * Validate an option value
 */
function skyscrpr_validate_option( $name ) {
  $value = get_option( $name );
  return true;
}

/**
 * Initialize admin-specific hooks and settings
 */
function skyscrpr_admin_init() {
  wp_register_script( "skyscrpr_admin_script", WP_PLUGIN_URL . "/skyscraper/skyscrpr.js" );
  wp_register_style( "skyscrpr_admin_style", WP_PLUGIN_URL . "/skyscraper/skyscrpr.css" );

  // register settings for the options page
  register_setting( "skyscrpr", "skyscrpr_site_id", "skyscrpr_sanitize_option" );
}

/**
 * Include javascript needed by the options page
 */
function skyscrpr_admin_includes() {
  wp_enqueue_script( "skyscrpr_admin_script" );
  wp_enqueue_style( "skyscrpr_admin_style" );
}

/**
 * Add the options menu item to the sidebar settings panel
 */
function skyscrpr_options_menu() {
  // add the options page to the settings menu
  $page = add_options_page( "Skyscraper Options", "Skyscraper", 8, __FILE__, "skyscrpr_options" );

  // include plugin-specific includes on the options page
  add_action( "admin_print_scripts-" . $page, "skyscrpr_admin_includes" );
  add_action( "admin_print_styles-" . $page, "skyscrpr_admin_includes" );
}

/**
 * Sanitize an options form field on submit
 */
function skyscrpr_sanitize_option( $value ) {
  // now that the form has been submitted at least once, start showing the
  // error for an empty/invalid api skyscrpr_site_id
  update_option( "is-not-first-load", true );

  return htmlspecialchars( $value );
}

// options

add_option( "is-not-first-load" );
add_option( "skyscrpr_site_id" );

// hooks

// register settings for the admin options page
add_action( "admin_init", "skyscrpr_admin_init" );
// add a menu item to the "settings" sidebar menu
add_action( "admin_menu", "skyscrpr_options_menu");

// add the <script/> tags to the footer
if( VIGLINK_ENABLED ) {
  add_action( "wp_footer", "skyscrpr_script" );
}
