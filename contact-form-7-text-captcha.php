<?php
/*
Plugin Name: Text Captcha For Contact Form 7 [GWE]
Plugin URI: https://getwebexperts.com/contact-form-7-text-captcha
Description: Protect Contact Form 7 forms from spam entries.
Version: 1.0.3
Author: Get Web Experts
Author URI: https://getwebexperts.com/
Text Domain: contact-form-7-text-captcha
License: GPL v2 or later 
*/
function cf7tc_activation_hook()
{
}
register_activation_hook(__FILE__, "cf7tc_activation_hook");
function cf7tc_deactivation_hook()
{
}
register_deactivation_hook(__FILE__, "cf7tc_deactivation_hook");

define("cf7tc_ASSETS_DIR", plugin_dir_url(__FILE__) . "assets/");
define("cf7tc_ASSETS_PUBLIC_DIR", plugin_dir_url(__FILE__) . "assets/public");
define( "cf7tc_ASSETS_ADMIN_DIR", plugin_dir_url( __FILE__ ) . "assets/admin" );
define('cf7tc_VERSION', time());


class basicCaptcha_cf7tc
{
    private $version;

    function __construct()
    {

        $this->version = time();

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'load_front_assets'));
        add_action('wp_enqueue_scripts', array($this, 'load_front_assets'));
    }

    function load_front_assets()
    {
        wp_enqueue_style('cf7tc-main-css', cf7tc_ASSETS_PUBLIC_DIR . "/css/cf7tcmain.css", null, $this->version);
        
        wp_enqueue_script('cf7tc-main-js', cf7tc_ASSETS_PUBLIC_DIR . "/js/cf7tcmain.js", null, $this->version, true );

    }


    function load_textdomain()
    {
        load_plugin_textdomain('contact-form-7-text-captcha', false, plugin_dir_url(__FILE__) . "/languages");
    }
}

new basicCaptcha_cf7tc();

add_action( 'wpcf7_init', 'add_shortcode_cf7tc' );
function add_shortcode_cf7tc() {
    wpcf7_add_form_tag( 'cf7tc', 'call_cf7tc', true );
}

function call_cf7tc($tag) {  
    $cf7tc_title_text = esc_html(get_option('cf7tc_title_text'));
    $tag = new WPCF7_FormTag( $tag );
    
   $output_cf7tc = '
   <div class="cf7tc_container">';
        $output_cf7tc .= __('<p class="noticecf7tc">'. $cf7tc_title_text. '</p>', 'contact-form-7-text-captcha');
       $output_cf7tc .= __('<span id="firstNumber"></span> +', 'contact-form-7-text-captcha');
       $output_cf7tc .= __('<span id="secondNumber"></span> =', 'contact-form-7-text-captcha');
       $output_cf7tc .= '<input type="text" id="result">';
    $output_cf7tc .= '</div>';
    return $output_cf7tc;
}


// Add Contact Form Tag Generator Button
add_action( 'wpcf7_admin_init', 'cf7tc_add_tag_generator', 55 );

function cf7tc_add_tag_generator() {
    $tag_generator = WPCF7_TagGenerator::get_instance();
    $tag_generator->add( 'cf7tc', __( 'Text Captcha', 'contact-form-7-image-captcha' ),
        'cf7tc_tag_generator', array( 'nameless' => 1 ) );
}

function cf7tc_tag_generator( $contact_form, $args = '' ) {
    $args = wp_parse_args( $args, array() ); ?>
    <div class="insert-box">
        <input type="text" name="cf7tc" class="tag code" readonly="readonly" onfocus="this.select()" />
        <div class="submitbox">
            <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
        </div>
    </div>
<?php
}

//Option Page Start
class cf7tc_Settings_Page
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'cf7tc_create_settings'));
        add_action('admin_init', array($this, 'cf7tc_setup_sections'));
        add_action('admin_init', array($this, 'cf7tc_setup_fields'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'cf7tc_settings_link'));
    }

    public function cf7tc_settings_link($links)
    {
        $newlink = sprintf("<a href='%s'>%s</a>", 'options-general.php?page=cf7tc', __('Settings', 'cf7tc'));
        $links[] = $newlink;
        return $links;
    }



    public function cf7tc_create_settings()
    {
        $page_title = __('Text Captcha For Contact Form 7', 'contact-form-7-image-captcha');
        $menu_title = __('Text Captcha For Contact Form 7', 'contact-form-7-image-captcha');
        $capability = 'manage_options';
        $slug       = 'cf7tc';
        $callback   = array($this, 'cf7tc_settings_content');
        add_options_page($page_title, $menu_title, $capability, $slug, $callback);
    }

    public function cf7tc_settings_content()
    { ?>
        <div class="wrap">
            <form class="cf7tc_form" method="POST" action="options.php">
                <?php
                settings_fields('cf7tc');
                do_settings_sections('cf7tc');
                submit_button();
                ?>
            </form>
        </div> <?php
            }

            public function cf7tc_setup_sections()
            {
                add_settings_section('cf7tc_section', 'Text Captcha For Contact Form 7', array(), 'cf7tc');
            }

            public function cf7tc_setup_fields()
            {
                $fields = array(
                    array(
                        'label'       => __('Title Text', 'contact-form-7-image-captcha'),
                        'id'          => 'cf7tc_title_text',
                        'type'        => 'text',
                        'section'     => 'cf7tc_section',
                         'placeholder' => __('Enter Title Text', 'contact-form-7-image-captcha'),
                    ),

                );
                foreach ($fields as $field) {
                    add_settings_field($field['id'], $field['label'], array(
                        $this,
                        'cf7tc_field_callback'
                    ), 'cf7tc', $field['section'], $field);
                    register_setting('cf7tc', $field['id']);
                }
            }
            public function cf7tc_field_callback($field)
            {
                $value = get_option($field['id']);
                switch ($field['type']) {
                    default:
                        printf(
                          '<input class="cf7tc_setting_form_field" name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s"/>',
                            $field['id'],
                            $field['type'],
                            isset($field['placeholder']) ? $field['placeholder'] : '',
                            $value
                        );
                }
                if (isset($field['desc'])) {
                    if ($desc = $field['desc']) {
                        printf('<p class="description">%s </p>', $desc);
                    }
                }
            }
        }
        new cf7tc_Settings_Page();
        //Option Page End
