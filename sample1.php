<?php 
//Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class for registering a new settings page under Settings.
 */
class IsbaOptionsPage {

    /**
    * Holds the values to be used in the fields callbacks
    */
    private $options;
 
    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'isba_admin_menu' ) );
        add_action('admin_init', array( $this, 'isba_register_add_settings') );
    }

    /**
    * Registers a new settings page under Settings.
    */
    public function isba_admin_menu() {

        add_options_page(
            __( 'Infinite Scroll By Ajax Settings Page', 'hire-expert-developer' ),
            __( 'ISBA Settings', 'hire-expert-developer' ),
            'manage_options', // Capablilties
            'isba_settings_admin', // Page slug
            array(
                $this,
                'isba_settings_page_cb' // callback function
            )
        );
    }
   
    /**
     * Settings page display callback.
     */ 
    public function isba_settings_page_cb() {

        // Set options class property
        $this->options = get_option( 'isba_settings_option' );
       
        ?>
        <div id="isba_settings_id" class="wrap">
        <h1>Infinite Scroll By Ajax Settings Page</h1>
        
        <form method="post" action="options.php">

          <?php 
            wp_nonce_field( 'isba_setting_nonce_action', 'isba_security' ); 
            settings_fields( 'isba-reg-setting-group' ); // Settubg Group
            do_settings_sections( 'isba_settings_admin' ); // Page slug
            submit_button(); 
          ?>
          </form>
        </div>


        <?php  
      
    }

    /**
     * Register and Add Settings.
     */
    public function isba_register_add_settings() {

       //Register Settings.    
        register_setting( 'isba-reg-setting-group', 'isba_settings_option' );
        
        add_settings_section(
        'isba_settings_section_id', // Section ID
        'Main Settings', // Title
        '', // Callback
        'isba_settings_admin' // Page Name isba_settings_admin
        );  

        add_settings_field(
        'isba_post_excerpt_size', // ID
        'Post Excerpt Size', // Title 
        array( $this, 'isba_post_excerpt_size_cb' ), // Callback
        'isba_settings_admin', // Page Name isba_settings_admin
        'isba_settings_section_id' // Section ID          
        );     

        add_settings_field(
        'isba_load_more_button_label', // ID
        'Load More Button Label', // Title 
        array( $this, 'isba_load_more_button_label_cb' ), // Callback
        'isba_settings_admin', // Page Name isba_settings_admin
        'isba_settings_section_id' // Section ID          
        );

        add_settings_field(
        'isba_post_auto_load', // ID
        'Post Auto Load', // Title 
        array( $this, 'isba_post_auto_load_cb' ), // Callback
        'isba_settings_admin', // Page Name isba_settings_admin
        'isba_settings_section_id' // Section ID          
        );     

        add_settings_field(
        'isba_blog_template', // ID
        'Select blog template', // Title 
        array( $this, 'isba_blog_template_cb' ), // Callback
        'isba_settings_admin', // Page Name isba_settings_admin
        'isba_settings_section_id' // Section ID          
        );
        
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function isba_post_excerpt_size_cb()
    {   
        printf(
            '<input type="number" min="20" max="100" name="isba_settings_option[isba_post_excerpt_size]" id="isba_post_excerpt_size" value="%s" class="small-text">
            <p class="description">Set the size of the text for blog posts.</p>',
            isset( $this->options['isba_post_excerpt_size'] ) ? esc_attr( $this->options['isba_post_excerpt_size']) : '20'
        );
    }

    public function isba_load_more_button_label_cb()
    {   
        printf(
            '<input type="text" name="isba_settings_option[isba_load_more_button_label]" id="isba_load_more_button_label" value="%s" class="regular-text">
            <p class="description">This text will replace show more button label.</p>',
            isset( $this->options['isba_load_more_button_label'] ) ? esc_attr( $this->options['isba_load_more_button_label']) : 'Load More'
        );
    }

    public function isba_post_auto_load_cb()
    {   
        if(isset( $this->options['isba_post_auto_load'] )) {
            $isba_post_auto_load = esc_attr( $this->options['isba_post_auto_load']);
        } else {
            $isba_post_auto_load = 1;
        } 
        ?>
        <select id="isba_post_auto_load" name="isba_settings_option[isba_post_auto_load]">
            <option <?php selected( $isba_post_auto_load , 1 ); ?> value="1">Yes</option>
            <option <?php selected( $isba_post_auto_load , 0 ); ?> value="0">No</option>
        </select>
        <p class="description">Select auto load posts on scroll or load on button click.</p>
<?php 
    }

     public function isba_blog_template_cb()
    {   
        if(isset( $this->options['isba_blog_template'] )) {
            $isba_blog_template = esc_attr( $this->options['isba_blog_template']);
        } else {
            $isba_blog_template = 1;
        } 
        ?>
        <select id="isba_blog_template" name="isba_settings_option[isba_blog_template]">
            <option <?php selected( $isba_blog_template , 1 ); ?> value="1">Template 1</option>
            <option <?php selected( $isba_blog_template , 2 ); ?> value="2">Template 2</option>
            <option <?php selected( $isba_blog_template , 3 ); ?> value="3">Template 3</option>
            </select>
        <p class="description">Select a template for your blog page.</p>
<?php 
    }
    

}
 
$IsbaOptionsPage = new IsbaOptionsPage();
