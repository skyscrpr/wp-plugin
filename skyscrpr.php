<?php
/*
Plugin Name: Skyscraper.io
Version: 0.1
Description: Include skyscraper.io script in your blog without effort
Author: Skyscraper
Author URI: http://www.skyscraper.io
*/

define( 'SKYSCRPR_MIN_WORDPRESS_REQUIRED', "2.7" );
define( 'SKYSCRPR_WORDPRESS_VERSION_SUPPORTED', version_compare( get_bloginfo( "version" ), SKYSCRPR_MIN_WORDPRESS_REQUIRED, ">=" ) );
define( 'SKYSCRPR_ENABLED', SKYSCRPR_WORDPRESS_VERSION_SUPPORTED && skyscrpr_validate_option( 'skyscrpr_site_id' ) );
define( 'SKYSCRPR_WIDGET_ENABLED', SKYSCRPR_WORDPRESS_VERSION_SUPPORTED && skyscrpr_validate_widget_option() );

/**
 * Print the Skyscraper EMK  <script/> tags
 */
function skyscrpr_widget_script() {
  $skyscrpr_site_id = get_option( "skyscrpr_site_id" );
  if( $skyscrpr_site_id ) {
    $script = "<!-- Skyscraper.io EMK Widget -->".
          "<script type='text/javascript'>".
          "  var skyscrprSettings = {".
          "    site_id: '". addslashes( $skyscrpr_site_id ). "',".
          "  };".
          "</script>".
          "<script>".
          "  (function() {".
          "    function async_load() {".
          "      var scr = document.createElement('script');".
          "      scr.type = 'text/javascript';".
          "      scr.async = true;".
          "      scr.src = '//install-skyscrpr-com.s3.amazonaws.com/publishers-widget.js';".
          "      var a = document.getElementsByTagName('script')[0];".
          "      a.parentNode.insertBefore(scr, a);".
          "    }".
          "    if (window.attachEvent) {".
          "      window.attachEvent('onload', async_load);".
          "    } else { ".
          "      window.addEventListener('load', async_load, false);".
          "    } ".
          "  })(); ".
          "</script>".
    "<!-- end Skyscraper.io EMK Widget -->".
    "<div id='skyscrpr-widget'></div>";
    return $script;
  }
}



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
        <script type='text/javascript' src='//install-skyscrpr-com.s3.amazonaws.com/skyscrpr.js'></script>
  <!-- end Skyscraper.io -->
<?php
  }
}

/**
 * Print the Skyscrpr plugin settings page
 */
function skyscrpr_options() { ?>
  <div class="wrap">
    <div class="icon32">&nbsp;</div>
    <h2>Skyscraper.io Settings</h2>
<?php
  if( ! SKYSCRPR_WORDPRESS_VERSION_SUPPORTED ) {
?>
    <p style="width: 50%;">
      Thanks for your interest in Skyscraper!  Unfortunately, the Skyscraper plugin
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
          <th style="width: auto;">Use Skyscrpr Media Kit</th>
          <td>
            <input id="skyscrpr-skyscrpr_widget_enabled" type="checkbox" name="skyscrpr_widget_enabled" value="1" <?php checked( 1 == get_option( "skyscrpr_widget_enabled" ) ); ?> />
            <span class="description">Required</span>
          </td>
          <?php if (SKYSCRPR_WIDGET_ENABLED) { ?>
            <th style="width: auto;">Permanent link</th>
            <td>
              <input id="skyscrpr-skyscrpr_widget_permlink" type="text" name="skyscrpr_widget_permlink" value="<?php print get_option( "skyscrpr_widget_permlink" ); ?>" class="regular-text"/>
            </td>
            <th style="width: auto;">Page Title</th>
            <td>
              <input id="skyscrpr-skyscrpr_widget_title" type="text" name="skyscrpr_widget_title" value="<?php print get_option( "skyscrpr_widget_title" ); ?>" class="regular-text"/>
            </td>
          <?php
          }
          else {?>
             <input type="hidden" name="skyscrpr_widget_permlink" value="<?php print get_option( "skyscrpr_widget_permlink" ); ?>">
             <input type="hidden" name="skyscrpr_widget_title" value="<?php print get_option( "skyscrpr_widget_title" ); ?>">
          <?php
          }
          ?>
        </tr>
      </table>
   <?php submit_button(); ?>

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
 *  Validate widget option
 */
function skyscrpr_validate_widget_option() {
  $value = get_option('skyscrpr_widget_enabled');
  return $value == 1;
}

/**
 *  Change the status of a post
 */

function update_post($post_id, $key,$value){
  $current_post = get_post( $post_id, 'ARRAY_A' );
  $current_post[$key] = $value;
  wp_update_post($current_post);
}

/**
 *  Create a page for the Skyscrpr Widget
 */

function create_widget_page() {
  $skyscrpr_widget_page_id = get_option("skyscrpr_widget_page_id" );
  if ($skyscrpr_widget_page_id) {
    $new_page = array(
      'slug' => 'media-kit',
      'title' => 'Advertise Here',
      'content' => skyscrpr_widget_script()
    );
    $new_page_id = wp_insert_post( array(
            'post_title' => $new_page['title'],
            'post_type'     => 'page',
            'post_name'      => $new_page['slug'],
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => '[skyscraper-advertise-here]',
            'post_status' => 'publish',
            'post_author' => 1,
    'menu_order' => 0
    ));
    update_option('skyscrpr_widget_permlink', $new_page['slug']);
    update_option('skyscrpr_widget_title', $new_page['title']);
    update_option('skyscrpr_widget_page_id', $new_page_id);
  }
}

/**
 *  Remove EMK Widget
 */

function remove_widget() {
  $skyscrpr_widget_page_id = get_option("skyscrpr_widget_page_id" );
  if ($skyscrpr_widget_page_id) {
    update_option('skyscrpr_widget_enabled', false);
    wp_delete_post($skyscrpr_widget_page_id, true );
  }
}



/**
 * Initialize admin-specific hooks and settings
 */
function skyscrpr_admin_init() {
  wp_register_script( "skyscrpr_admin_script", WP_PLUGIN_URL . "/skyscraper/skyscrpr.js" );
  wp_register_style( "skyscrpr_admin_style", WP_PLUGIN_URL . "/skyscraper/skyscrpr.css" );

  // register settings for the options page
  register_setting( "skyscrpr", "skyscrpr_site_id", "skyscrpr_sanitize_option" );
  register_setting( "skyscrpr", "skyscrpr_widget_enabled");
  register_setting( "skyscrpr", "skyscrpr_widget_permlink");
  register_setting( "skyscrpr", "skyscrpr_widget_title");
}

/**
 * Include javascript needed by the options page
 */
function skyscrpr_admin_includes() {
  wp_enqueue_script( "skyscrpr_admin_script" );
  wp_enqueue_style( "skyscrpr_admin_style" );
}

function skyscrpr_admin_page_loaded_callback() {
  if (SKYSCRPR_WIDGET_ENABLED) {
    $skyscrpr_widget_page_id = get_option("skyscrpr_widget_page_id");
    $skyscrpr_widget_permlink = get_option("skyscrpr_widget_permlink");
    $skyscrpr_widget_title = get_option("skyscrpr_widget_title");

    update_post($skyscrpr_widget_page_id, 'post_status', 'publish');
    update_post($skyscrpr_widget_page_id, 'post_name', $skyscrpr_widget_permlink);
    update_post($skyscrpr_widget_page_id, 'post_title', $skyscrpr_widget_title);
  }
  else {
    $skyscrpr_widget_page_id = get_option( "skyscrpr_widget_page_id" );
    update_post($skyscrpr_widget_page_id,'post_status', 'draft');
  }
}

/**
 * Add the options menu item to the sidebar settings panel
 */
function skyscrpr_options_menu() {
  // add the options page to the settings menu
  $page = add_options_page( "Skyscraper Options", "Skyscraper", 8, __FILE__, "skyscrpr_options" );
  add_action('load-'.$page,'skyscrpr_admin_page_loaded_callback');

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

add_option("is-not-first-load");
add_option("skyscrpr_site_id");
add_option("skyscrpr_widget_enabled");
add_option("skyscrpr_widget_permlink");
add_option("skyscrpr_widget_title");

// hooks

// register settings for the admin options page
add_action( "admin_init", "skyscrpr_admin_init" );
// add a menu item to the "settings" sidebar menu
add_action( "admin_menu", "skyscrpr_options_menu");

register_activation_hook(__FILE__, 'create_widget_page' );
register_deactivation_hook(__FILE__, 'remove_widget');

// shortcodes

add_shortcode('skyscraper-advertise-here', 'skyscrpr_widget_script' );

// add the <script/> tags to the footer

if( SKYSCRPR_ENABLED) {
  add_action( "wp_head", "skyscrpr_script" );
}
