<?php
define('STRIPE_API_KEY', 'asjdfljaslfjl'); 
define('STRIPE_PUBLISHABLE_KEY', 'asdfjsadk'); 

// Fontawesome Icons
include_once('inc/fa_icons_array.php');
/*
* Manage session in WordPress
* Commented because header is already send 
*/
add_action('init', 'start_session', 1);
function start_session() {
    if (!session_id()) {
        session_start();
    }
    generateDefaultPages();
}

add_action('wp_logout', 'end_session');
add_action('wp_login', 'end_session');
add_action('end_session_action', 'end_session');
function end_session() {
    session_destroy();
}



/*
* Enqueue scripts and styles files
*/

if (!function_exists('sample_theme_scripts_styles')) {

    function sample_theme_scripts_styles() {
        // Theme info
        $my_theme = wp_get_theme();
        $path_url = network_site_url().'/wp-content/themes/sample';
        // Fonts & enqueue css
        wp_enqueue_style('font-css1', 'https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap');
        wp_enqueue_style('font-css2', 'https://fonts.googleapis.com/css?family=Rock+Salt:200,300,400,700&display=swap');
        wp_enqueue_style('font-css3', 'https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500&display=swap');
       
        // Custom style
        wp_enqueue_style('fonts-css', $path_url . '/fonts/fonts.css');
        wp_enqueue_style('main-css', $path_url . '/css/main-min.css');

        $blog_id = get_current_blog_id();
        $user_id  = get_users_of_blog($blog_id)[0]->user_id; 
        if(is_page('preview')) {
            $user_id  = get_current_user_id(); 
        }
        $template_name = get_user_meta($user_id, 'lp_selected_template' , 'true' ); 
        if(is_page('demos')) {
            $template_name = sanitize_title($_GET['t']);
        }
        if(!empty($template_name) && (($blog_id != 1) || (is_page('preview')) || (is_page('demos'))) && ($template_name!='lp-template-sample')){
            $template_name = str_replace('lp-template-', '', trim(strtolower($template_name)));
            wp_enqueue_style('sample_style-css', $path_url . '/css/style.css');
            wp_enqueue_style($template_name . '-style', $path_url . '/css/'.$template_name.'-style.css');
            wp_enqueue_style('sample_responsive-css', $path_url . '/css/responsive.css');
            wp_enqueue_style($template_name . '-responsive', $path_url . '/css/'.$template_name.'-responsive.css');
        } else {
            wp_enqueue_style('sample_style-css', $path_url . '/css/style.css');
            wp_enqueue_style('sample_responsive-css', $path_url . '/css/responsive.css');
        }
    
        // enqueue js files inside footer
        wp_enqueue_script('main-js', $path_url . '/js/main-min.js', array() , $my_theme->get('Version') , true);
        wp_enqueue_script('stripe_js', 'https://js.stripe.com/v3/', '' , '' , true);
        //wp_enqueue_script('validate-js', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array() , $my_theme->get('Version') , true);
        // Add library for repeater field
        if(is_page('registration')){
            //enque select 2 css 
          //  wp_enqueue_style('select-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css');
            wp_enqueue_style('dropzone-css', 'https://unpkg.com/dropzone/dist/dropzone.css');
            wp_enqueue_style('cropper-css', 'https://unpkg.com/cropperjs/dist/cropper.css');
            wp_enqueue_script('select-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', array() , $my_theme->get('Version') , true);
            // wp_enqueue_script('repeater1-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js', array() , $my_theme->get('Version') , true);
            wp_enqueue_script('repeater2-js', $path_url . '/js/repeater2.js', array() , $my_theme->get('Version') , true);
            wp_enqueue_script('dropzone-js', 'https://unpkg.com/dropzone', array() , $my_theme->get('Version') , true);
            wp_enqueue_script('cropper-js', 'https://unpkg.com/cropperjs', array() , $my_theme->get('Version') , true);
            wp_enqueue_script('tinymce-js', 'https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js', array() , $my_theme->get('Version') , true);
            wp_enqueue_script('canvastoblob-js', $path_url . '/js/canvastoblob.js', array() , $my_theme->get('Version') , true);
            
        }
        wp_enqueue_script('ytvideo_js', $path_url . '/js/bg-yt-video.js', array() , $my_theme->get('Version') , true);
        // include stripe js file 
      //http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js
        wp_enqueue_script('style_js', $path_url . '/js/style.js', array() , $my_theme->get('Version') , true);
       
        // Localize the script with new data
        $steps_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'stripe_publishable_key' => STRIPE_PUBLISHABLE_KEY,
            'theme_uri' => $path_url,
        );
        wp_localize_script('style_js', 'ajax_object', $steps_array);

    }
    add_action('wp_enqueue_scripts', 'sample_theme_scripts_styles');
}

/*
* Admin Styles
*/
add_action( 'admin_enqueue_scripts', 'lp_pages_style_files' ); 
function lp_pages_style_files(){
    $my_theme = wp_get_theme();
    $path_url = network_site_url().'/wp-content/themes/sample';

    wp_enqueue_style('sample-admin', get_template_directory_uri() . '/css/admin-style.css');
    wp_enqueue_style('sample-fontawsome', 'https://use.fontawesome.com/releases/v5.0.1/css/all.css');
     // Localize the script with new data
    
    wp_enqueue_script('custom_js', $path_url. '/js/custom-admin.js', array() , '' , true);
    $translation_array = array(
        'ajax_url' => admin_url('admin-ajax.php')
    );
    wp_localize_script('custom_js', 'translation_object', $translation_array);

}

/*
* Register Post type
*/
$postTypes = array(
	'agl_abandoned', 
    'agl_translations', 
    'algp_transaltion',
);
if ($postTypes) {
	foreach($postTypes as $type) {
		// Post Types
		include_once('inc/post-types/'.$type.'.php');
	}
}

// Check email address already exists database or not 

add_action('wp_ajax_email_address_check', 'email_address_exist_call_back');
add_action('wp_ajax_nopriv_email_address_check', 'email_address_exist_call_back');

function email_address_exist_call_back(){
    $email_address = $_POST['user_email_address'];
    // check valid email address 
    if(email_exists($email_address)){
        $status = array(
            'message' => 'Email already exists.' ,
            'status_code' => 1
        );       
    } else {
        $status = array(
            'message' => 'Please enable button.',
            'status_code' => 0
        );
    }

    return wp_send_json($status);
    wp_die();
}

/* 
* Email Verification Ajax Callback function
* Status Codes: 0 = fail, 1 = success, 2 = already sent
*/ 
add_action('wp_ajax_email_verification', 'email_verification_call_back');
add_action('wp_ajax_nopriv_email_verification', 'email_verification_call_back');
function email_verification_call_back(){
    $_SESSION['code_generator'] = '';
    // random code generator
    if(!isset($_SESSION['code_generator']) || empty($_SESSION['code_generator']) || $_POST['code_status'] == 'resend_code'  ){

        // Generate OTP
        $code_generator = rand(10000,999999);
        $_SESSION['code_generator'] = $code_generator; 

        // Mail Details
        $to_email   = $_POST['user_email_address'];
        $subject    = "Email Verification for Sample.";
        $body       = "Your verification code is - ".$code_generator;
        $headers    = "From: Sample";

        // Send Mail
        $mailSent   = wp_mail($to_email, $subject, $body);    

        // Update status details
        $status['email_address']    = $_POST['user_email_address'];
        $status['generated_code']   = $_SESSION['code_generator'];

        if ($mailSent) {
            // Check if mail sent
            $status['message']      = 'OTP has been sent to your email.';
            $status['status_code']  = 1;
        } else {
            // Check if mail not sent
            $status['message']      = 'Unable to send OTP try again.';
            $status['status_code']  = 0;
        }   

    } else {
        // Check if mail already sent
        $status['message']      = 'OTP already sent on your email.';
        $status['status_code']  = 2;
    }
    
    return wp_send_json($status);
    wp_die(); 
}

/*
* OTP Verification 
* Status Codes: 1 = success, 0 = fail, -1 = empty
*/ 
add_action('wp_ajax_otp_verification', 'otp_verification_call_back');
add_action('wp_ajax_nopriv_otp_verification', 'otp_verification_call_back');
function otp_verification_call_back(){
    
    $otp_code           = $_POST['otp_code'];
    $user_email_address = $_POST['user_email_address'];
    $session_otp_code   = $_SESSION['code_generator'];
    $abandand_id        = $_SESSION['abandand_post_id'];
    $user_name          = $_POST['user_name'];

    // Set Session values
    $_SESSION['abandand_user_info']['user_password'] = base64_encode($_POST['user_password']);;
    $_SESSION['abandand_user_info']['user_cpassword'] = base64_encode($_POST['user_cpassword']);;
 
    $_SESSION['abandand_user_info']['user_email']     =  $user_email_address;
    $_SESSION['abandand_user_info']['user_name']      = $user_name;
    $_SESSION['abandand_user_info']['email_verified'] = 'Unverified';

    // Status 0 if failed
    $output['status']   = 0;     
    
    if(!empty($otp_code) && !empty($session_otp_code)){
        // Check Input Otp code and session data  
        if($otp_code == $session_otp_code ){
            $_SESSION['abandand_user_info']['email_verified'] = 'Verified';
            $_SESSION['abandand_user_info']['user_everified'] = 'Verified';
            if(is_user_logged_in()){
                update_user_meta(get_current_user_id(),'email_verified','Verified');
            }
            //update_post_meta($abandand_id, 'email_verified', 'Verified');
            $output['status'] = 1;
        }
    } else {
        $output['status'] = -1;
    }
    
    return wp_send_json($output);
    wp_die(); 
}

/*
* Ajax Multistep for submit data callback
*/
add_action('wp_ajax_step_form', 'step_form_submit_call_back');
add_action('wp_ajax_nopriv_step_form', 'step_form_submit_call_back');
function step_form_submit_call_back()  {
    $form_data      = $_POST['form_data'];
    $steps_status   = $_POST['steps_status'];
    parse_str($form_data, $data);
    // $remove_filter  = array_map('array_filter', $data);
    // $data = array_filter($remove_filter);
    // Store data into session 
    $_SESSION['abandand_user_info'] = $data;

    $data['user_password']  = base64_encode($data['user_password']);
    $data['user_cpassword'] = base64_encode($data['user_cpassword']);

    $_SESSION['abandand_user_info']['user_password']  = $data['user_password'];
    $_SESSION['abandand_user_info']['user_cpassword'] = $data['user_cpassword'];

    // Check if data and not user logged in
    if(!empty($data) && !is_user_logged_in()){ 
        if($steps_status == 'next'){

            if(!isset($_SESSION['abandand_post_id'])){
                 
                // Insert user abandoned post 
                $abandoned_args = array(
                    'post_title'    => "Abandoned user - ".$data['user_name'],
                    'post_status'   => 'publish',
                    'post_type'     => 'agl_abandoned' 
                );

                $abandoned_id = wp_insert_post($abandoned_args);
                
                // Store id into session
                $_SESSION['abandand_post_id'] = $abandoned_id;
                
                // set status
                $output['message'] = 'User abandand data inserted successfully';
                $output['abandand_post_id'] = $abandoned_id;
                $output['code']     = 1;

            } else {
                // update post title
                $update_abandoned_args = array(
                    'ID'            => $_SESSION['abandand_post_id'],
                    'post_title'    => "Abandoned user --".$data['user_name'],
                );
                wp_update_post( $update_abandoned_args );

                // set status
                $output['message'] = 'User abandand data updated successfully';
                $output['abandand_post_id'] = $_SESSION['abandand_post_id'];
                $output['code']     = 1;
            }

            // check session id empty default post id inserted
            $abandoned_inserted_id = $_SESSION['abandand_post_id'] ?: $abandoned_id;

            // Update Meta
            update_post_meta($abandoned_inserted_id , 'lp_template_data', $data); 

            if($_SESSION['cusrid']){

               update_user_meta( $_SESSION['cusrid'], 'lp_template_data', $data) ; 
            }

                    
        } else if($steps_status == 'register'){

            // Create new user
            $postId     =    $_SESSION['abandand_post_id'];

            if ($postId) {

                $postData = get_post_meta($postId, 'lp_template_data', true);

                $password       = base64_decode($_SESSION['abandand_user_info']['user_password']);
                
                $email          = $postData['user_email'];
                $everified      = $postData['user_everified'];

                if ($everified) {
                    
                    $userData = create_new_user('', $password, $email);
                    $output['message'] = $userData['message'];
                    $output['code'] = $userData['code'];
                    $output['userData'] = $userData;

                    if ($userData['userid']) {
                        $_SESSION['cusrid'] = $userData['userid']; 
                    }

                    update_user_meta( $userData['userid'], 'lp_template_data', $data) ; 
                    update_user_meta( $userData['userid'], 'display_name', trim($data['user_name'])) ; 
                    update_user_meta( $userData['userid'], 'email_verified','Verified');
                }

            } else {

                $output['message']  = 'Unable to verify user.';
                $output['code']     = 0;
                $output['redirect'] = home_url().'/log-in';
            }

        } else {
            // To do 
           // fetch_payment_data($data);
        }    

    } else if(!empty($data) && is_user_logged_in()){  // if data and user logged in
        $currentUserId = get_current_user_id();
        if ($currentUserId) {

            $_SESSION['cusrid'] = $currentUserId; 

            update_user_meta($currentUserId, 'lp_template_data', $data);
            update_user_meta( $currentUserId, 'display_name', trim($data['user_name'])) ;
            
            // check preview data 
            $prevData = get_user_meta( $currentUserId, 'lp_template_data', true);
            // If remove uncessary user info
            $allEmpty = remove_site_preview_empty($prevData);
            // fetch value empty or not 
            $disabled = (!in_array( 1, $allEmpty ) ? 'disabled' : '');
            $output['message'] = 'User data updated.';
            $output['code']     = 1;
            $output['disabled_status'] = $disabled; 
            // Reset user password
            $password = base64_decode($data['user_password']);
            if ($password) {
                $user = get_user_by( 'id', $currentUserId );
                if ( wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
                    $output['message'] = 'New password must be different from old password.';
                    $output['redirect'] = home_url().'/registration/?rstep=1';
                    $output['code']     = 0;
                    $output['type']     = 'passreset';
                } else {
                    wp_set_password( $password, $currentUserId );
                    $output['message'] = 'User details and password updated.';
                    $output['redirect'] = home_url().'/log-in/';
                    $output['code']     = 2;
                    $output['type']     = 'passreset';
                }
            }
        }

    } else { // no data
        $output['message'] = 'Form data empty.';
        $output['code']     = 0;
    }

    return wp_send_json($output);

    wp_die();
}

/*
* Function to unserialize for data
*/
function unserializeForm($data) {
    $strArray = explode("&", $data);
    foreach($strArray as $item) {
        $array = explode("=", $item);
        $returndata[$array[0]] = $array;
    }
    return $returndata;
}

/*
* LP Settings for Network site (Super Administrator)
*/
add_action( 'network_admin_menu', 'sample_network_admin_menus' );
function sample_network_admin_menus(){
    
    add_menu_page( 
        __( 'AGLP Settings', 'textdomain' ),
        __( 'AGLP Settings', 'textdomain' ),
        'manage_options',
        'sample-lp-settings',
        'sample_network_lp_settings_cb',
        'dashicons-admin-tools', // icon
        '100' // menu position
    );

    add_submenu_page(
        'sample-lp-settings',
        'AGLP Settings',
        'AGLP Settings',
        'manage_options',
        'sample-lp-settings'
        
    );
    add_submenu_page(
        'sample-lp-settings',
        'LP Settings', 
        'Maintenance Mode',
        'manage_options', 
        'maintenance',
        'maintenance_setting'
    );

    add_submenu_page(
        'sample-lp-settings',
        'LP Settings', 
        'AGLP Translation',
        'manage_options', 
        'algp_transaltion',
        'algp_transaltion'
    );
   
}


// call translation function start
function algp_transaltion(){ 
        // include tranlsation html 
        get_template_part( 'template-parts/admin-translation/sample-translation' );

}
// Translation function end 


function maintenance_setting(){ ?>
    <?php
    $network_id = get_current_network_id();
    $mdata = get_network_option($network_id, 'maintenance_data', true );
    $mdata = unserialize($mdata);

    if ($mdata['show'] == 'yes') {
        $isChecked = 1;
    }
    ?>
  <div class="maintenance_ui">
    <h2>Maintenance Bar for admins</h2>    
    <form method="post" name="maintenance" action="edit.php?action=maintenance">
        <?php wp_nonce_field( "maintenance_nonce" ); ?>
        <input type="hidden" name="maintenance_mode" value="yes" />
        <label for="maintenance">Show maintenance bar</label>
        <p><input type="checkbox" class="maintenance" name="maintenance_data[show]" id="maintenance" <?php if ($isChecked) echo 'checked="checked"'; ?> value="yes" /><span class="maintenance-bar"> Show</span></p>
        <label for="maintenance">Title</label>
        <p><input type="text" name="maintenance_data[title]" value="<?php echo $mdata['title']; ?>" /></p>
        <label for="maintenance">Description</label>
        <p><textarea name="maintenance_data[desc]"><?php echo $mdata['desc']; ?></textarea></p>
        <p><?php submit_button();?></p>
    </form> 
    </div>
</div>
<?php }

// save data 
add_action( 'network_admin_edit_maintenance', 'update_maintenance_data' );
function update_maintenance_data(){
    check_admin_referer('maintenance_nonce');

     if (isset($_POST['maintenance_mode'])) {
        $fieldValue = serialize($_POST['maintenance_data']);
        $network_id = get_current_network_id();
        update_network_option($network_id, 'maintenance_data', $fieldValue );
    }

    wp_redirect( add_query_arg( array(
        'page' => 'maintenance',
        'updated' => true ), network_admin_url('admin.php?page=maintenance')
    ));
    exit();
    
}

/*
* function to create admin Tabs
*/
function template_admin_tabs( $current = 'sample' ) {
    // List of Tabs array.
    $tabs_list = array('sample' => 'sample' , 'Sample' => 'Sample');
    $tabs = apply_filters( 'sample_tab_item_list', $tabs_list);
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=sample-lp-settings&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}


 
/**
 * Network settings page callback function
 */
function sample_network_lp_settings_cb(){

    // get current id 
    $network_id = get_current_network_id();

    // First active tab
    $tab = 'sample';

    // Check if tab is changed
    if ( isset( $_GET['tab'] ) ) {
        $tab = $_GET['tab'];
    } 

    // Get Settings Data
    $autolpSettings = unserialize(get_network_option($network_id, 'sample_network_options' ));
    ?>
    <div class="wrap">
        <?php 
        // Tabs 
        template_admin_tabs($tab); 
        ?>
        <div class="lp-panel custom-ui-sample">
            <div class="lp-panel-tab-section"> 
                <form method="post" action="edit.php?action=sample_lp_update">
                <input type="hidden" name="sample_network_options" value="update" />
                    <?php
                    wp_nonce_field( "sample_lp_settings_nonce" ); 
                        
                    echo '<table class="form-table">';
                    switch ( $tab ){
                        case 'sample' :
                            $sampleFields = array(
                                array(
                                    'name' => 'section_1',
                                    'label' => 'Banner' ,
                                ),
                                array(
                                    'name' => 'section_2',
                                    'label' => 'Are you worried?..' ,
                                ),
                                array(
                                    'name' => 'section_3',
                                    'label' => 'Leave it to..' ,
                                ),
                                array(
                                    'name' => 'section_4',
                                    'label' => 'Why is Instagram..' ,
                                ),
                                array(
                                    'name' => 'section_5',
                                    'label' => 'Register now!..' ,
                                ),
                                array(
                                    'name' => 'section_6',
                                    'label' => 'Why sample..' ,
                                ),
                                array(
                                    'name' => 'section_7',
                                    'label' => 'Only 3 steps..' ,
                                ),
                                array(
                                    'name' => 'section_8',
                                    'label' => 'Register now!..' ,
                                ),
                                array(
                                    'name' => 'section_9',
                                    'label' => 'AI Targeting..' ,
                                ),
                                array(
                                    'name' => 'section_10',
                                    'label' => 'List of Functions..' ,
                                ),
                                array(
                                    'name' => 'section_11',
                                    'label' => 'FAQs' ,
                                ),
                                array(
                                    'name' => 'section_12',
                                    'label' => 'Register now!.' ,
                                ),
                                array(
                                    'name' => 'section_13',
                                    'label' => 'Footer' ,
                                ),

                            ); 

                            ?>
                            <h2>Landing Page section show / hide options</h2>
                            <p>By default all sections are visible on the landing page. You can uncheck the box right side of the section to hide that section</p>
                                <?php foreach($sampleFields as $skey => $sfield) { 
                                   $count = $skey + 1;
                                   $fieldName = $sfield['name'];
                                   $fieldLabel = $sfield['label'];
                                   $isChecked = $autolpSettings[$fieldName];
                                ?>
                                <tr>
                                    <th><?php echo $count; ?>. <?php echo $fieldLabel; ?></th>
                                    <td>
                                        <input name="sample[<?php echo $fieldName; ?>]" type="checkbox" <?php if ($isChecked) echo 'checked="checked"'; ?> value="true" />
                                    </td>
                                </tr>
                                <?php } ?>
                            <?php
                            break;

                            case 'Sample' :
                                $SampleFields = array(
                                    array(
                                        'name' => 'section_1',
                                        'label' => 'Banner' ,
                                    ),
                                    array(
                                        'name' => 'section_2',
                                        'label' => 'Do you have such..' ,
                                    ),
                                    array(
                                        'name' => 'section_3',
                                        'label' => 'But I often hear this..' ,
                                    ),
                                    array(
                                        'name' => 'section_4',
                                        'label' => 'It was created..' ,
                                    ),
                                    array(
                                        'name' => 'section_5',
                                        'label' => 'Register now!..' ,
                                    ),
                                    array(
                                        'name' => 'section_6',
                                        'label' => 'Three major benefits..' ,
                                    ),
                                    array(
                                        'name' => 'section_7',
                                        'label' => 'Who do you learn from?...' ,
                                    ),
                                    array(
                                        'name' => 'section_8',
                                        'label' => 'Useful content full of volume..' ,
                                    ),
                                    array(
                                        'name' => 'section_9',
                                        'label' => 'Register now!...' ,
                                    ),
                                    array(
                                        'name' => 'section_10',
                                        'label' => 'Why are you chosen?..' ,
                                    ),
                                    array(
                                        'name' => 'section_11',
                                        'label' => 'Message from the mentor...' ,
                                    ),
                                    array(
                                        'name' => 'section_12',
                                        'label' => 'Message Video?..' ,
                                    ),
                                    array(
                                        'name' => 'section_13',
                                        'label' => 'Register Now..' ,
                                    ),
                                    array(
                                        'name' => 'section_14',
                                        'label' => 'You can learn this..' ,
                                    ),
                                    array(
                                        'name' => 'section_15',
                                        'label' => 'Voices of participating members..' ,
                                    ),
                                    array(
                                        'name' => 'section_16',
                                        'label' => 'FAQ' ,
                                    ),
                                    array(
                                        'name' => 'section_17',
                                        'label' => 'Register Now..' ,
                                    ),
    
                                ); 
    
                                ?>
                                <h2>Landing Page section show / hide options</h2>
                                <p>By default all sections are visible on the landing page. You can uncheck the box right side of the section to hide that section</p>
                                    <?php foreach($SampleFields as $skey => $sfield) { 
                                       $count = $skey + 1;
                                       $fieldName = $sfield['name'];
                                       $fieldLabel = $sfield['label'];
                                       $isChecked = $autolpSettings[$fieldName];
                                    ?>
                                    <tr>
                                        <th><?php echo $count; ?>. <?php echo $fieldLabel; ?></th>
                                        <td>
                                            <input name="sample[<?php echo $fieldName; ?>]" type="checkbox" <?php if ($isChecked) echo 'checked="checked"'; ?> value="true" />
                                        </td>
                                    </tr>
                                    <?php } ?>
                                <?php
                                break;

                        }
                        echo '</table>'; 
                        submit_button();
                        ?>
                </form>
            </div>  
            <div class="lp-panel-image-section">
                <?php 
                switch ( $tab ){
                 case 'sample' :
                ?>
                <img src="<?php echo get_template_directory_uri().'/images/demo_setting_img.png'; ?>" alt="">           
                <?php break; 
                 case 'Sample' :
                 ?>
                  <img src="<?php echo get_template_directory_uri().'/images/demo_setting_img.png'; ?>" alt="">           
                  <?php
                  break;   
                } ?>
            </div>
        </div>    
    </div>  
    <?php
}

/*
* Save / Update Network admin setting 
*/
add_action( 'network_admin_edit_sample_lp_update', 'template_save_settings_options' );
function template_save_settings_options($network_id) {
    check_admin_referer('sample_lp_settings_nonce');

    // Saving sample LP values
    if (isset($_POST['sample_network_options'])) {
        $fieldValue = serialize($_POST['sample']);
        update_network_option($network_id, 'sample_network_options', $fieldValue );
    }
  
	wp_redirect( add_query_arg( array(
		'page' => 'sample-lp-settings',
		'updated' => true ), network_admin_url('admin.php?page=sample-lp-settings')
	));
 
	exit;
}

/*
* Notification for network settings update.
*/
add_action( 'network_admin_notices', 'network_settings_update_notices' );
function network_settings_update_notices(){
	if( isset($_GET['page']) && $_GET['page'] == 'sample-lp-settings' && isset( $_GET['updated'] )  ) {
		echo '<div id="message" class="updated notice is-dismissible"><p>Network LP Settings has been updated.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	}
}

/* 
* Create new User in WordPress
*/
// create_new_user('', '', $email = 'webdevin@yahoo.com');
function create_new_user($username, $password, $email) {

    $output['status'] = 'userCreateError';
    $output['message'] = 'Unable to create new user.';
    $output['code'] = '0';

    // If email not given
    if (!$email) {
        $output['message'] = 'Email address not given';
    }

    // Auto Generate a password
    if (!$password) {
        $password = wp_generate_password( 10, true, true );
    }

    // Use username with email if not given
    if (!$username) {
        $emailArray = explode('@', $email);
        if(count($emailArray)>1) {
            $username = $emailArray['0'];
        }
    }

    // Generate a new username if already exists
    if (username_exists($username) && (!email_exists($email))) {
        do {
            $randomString = sprintf("%04d", mt_rand(1, 9999));
            $username = $username . $randomString;
            $user_exists = username_exists( $username );
        } while( $user_exists > 0 );
    }

    // Check if user or email already exists
    if ((!username_exists($username)) && (!email_exists($email))) {
       
        $user_id = wpmu_create_user($username, $password, $email);
       
        if ($user_id) {
            $user = get_user_by('id', $user_id);
            $user->remove_role('subscriber');
            $user->add_role('administrator');
            // User created status 
            $output['status'] = 'userCreated';
            $output['userid'] = $user_id;
            $output['message'] = 'New user created.';
            $output['code'] = '1';
            // if user now logged in will be automatically login
            if(!is_user_logged_in()){
                $user = get_user_by('email', $email );
                $user_id = $user->ID;
                if ($user && wp_check_password($password, $user->data->user_pass, $user_id)) {
                    clean_user_cache($user_id);
                    wp_clear_auth_cookie();
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id, true, false);
                    update_user_caches($user);
                    $current_user = wp_get_current_user();
                    $user_info = array('email'     => $current_user->user_email,
                                    'firstname' => $current_user->user_firstname,
                                    'lastname'  => $current_user->user_lastname,
                                    );
                    $output['message']  = 'User Login Successfully';
                    $output['user_info'] = json_encode($user_info);
                    $output['code']     = 2; 
                }

            }       

        }

    } else {

        $user = get_user_by( 'email', $email );

        $output['message'] = 'User already exists.';
        $output['userid'] = $user->ID;
        $output['status'] = 'alreadyExists';
        $output['code'] = '2';
    }

    return $output;    
}

/*
* Create new sub site
*/ 
//create_new_site($domain = '/', $path = 'sample/akshaysite2', $title = "Sub site", $userid = '1');
function create_new_site($domain, $path, $title, $user_id) {
    global $current_site;
    $blog = wpmu_create_blog($current_site->domain, $path, $title, $user_id);
    if (!is_array($blog)) {
        $blog_id = $blog;
        $blogTheme = get_blog_option( $blog_id, 'stylesheet' );
        // save blog id in usermeta 
        update_user_meta($user_id, 'blog_id',$blog_id );
        update_user_meta($user_id, 'site_title',$title );
        update_user_meta($user_id, 'site_path',$path );
        if ($blogTheme != 'sample') {
            $blogTheme = 'sample';
            update_blog_option( $blog_id, 'stylesheet', $blogTheme );
            update_blog_option( $blog_id, 'template', $blogTheme );
            update_blog_option( $blog_id, 'option_template', $blogTheme );
        }
    }

    return $blog_id;
}

/*
* Fontawesome Icons List
*/
function fontawesome_icons($sicon = '') {
    $icons = get_fa_icons();
    echo '<option value="">Select Icon</option>';
    if ($icons) {
        foreach($icons as $ikey => $icon) {
        if ($sicon == $ikey) {    
        ?>
            <option data-icon="<?php echo $ikey; ?>" data-tokens="<?php echo $icon; ?>" selected="selected" value="<?php echo $ikey; ?>"><?php echo $icon; ?></option>
        <?php 
        } else {
        ?>
            <option data-icon="<?php echo $ikey; ?>" data-tokens="<?php echo $icon; ?>" value="<?php echo $ikey; ?>"><?php echo $icon; ?></option>
        <?php
        }
        }
    }
}



/*
* Code to upload a file | Site - allprograms.tech 
*/
function istl_upload_file_php($file) {
    $output = array();
    if($file) { 
      $errors= array();
      $file_name = $file['name'];
      $file_size = $file['size'];
      $file_tmp = $file['tmp_name'];
      $file_type = $file['type'];
      $file_ext=strtolower(end(explode('.',$file['name'])));
  
      // Allowed extensions
      $expensions= array("jpg", "jpeg", "png");
  
      if(in_array($file_ext,$expensions)=== false){
         $errors[]="Extension not allowed $expensions .";
      }
  
      if($file_size > 2097152) {
         $errors[]='File size should not be greater than 2 MB';
      }
      
      $upload_dir = wp_upload_dir();
      $upload_dir_path = $upload_dir['basedir'];   
  
      if(empty($errors)==true) {
          move_uploaded_file($file_tmp, $upload_dir_path."/tempdata/".$file_name);
          $output['error'] = 0;
          $output['attachment_id'] = 0; 
          $output['file_path'] = $upload_dir_path."/tempdata/".$file_name;    
          $output['file_url'] = '';   
      } else {
          $output['error'] = 1;
          $output['error_message'] = $errors;
      } 
    }  
    return $output; 
  }

/*
* Function to add post/page slug as class in body | Site - allprograms.tech
*/
add_filter( 'body_class', 'addPageSlugAsBodyClass' );
function addPageSlugAsBodyClass( $classes ) {
    global $post;
    if ( isset( $post ) ) {
    		// Get Post/Page Slug
        $pageSlug = $post->post_name;    
        $classes[] = 'page-'.$pageSlug;
    }
    return $classes;
}

/* 
* Generate Pages dyanmically.
*/
function generateDefaultPages() {

    $pages = array (
        'Landing Page',
        'Blog',
    );
    if ($pages) {
  
      update_option('show_on_front', 'page'); 
  
      foreach($pages as $page) {
          $pageData = get_page_by_title( $page );
          if (!$pageData) {
  
            // Create new page
            $my_post = array(
              'post_title'    => wp_strip_all_tags( $page ),
              'post_content'  => '',
              'post_status'   => 'publish',
              'post_type'     => 'page',
            );
             
            // Insert page in Database
            wp_insert_post( $my_post );
  
          } else {
  
            // Front Page / Landing Page
            if ($page == 'Landing Page') {
              update_option('page_on_front', $pageData->ID);   
            }
  
            // Blog Page
            if ($page == 'Blog') {
              update_option('page_for_posts', $pageData->ID ); 
            }
          }
      }
    }
  }


/*
* Function to get video URL type
*/
function getVideoUrlType($url) {
    if (strpos($url, 'youtube') > 0) {
        return 'youtube';
    } elseif (strpos($url, 'vimeo') > 0) {
        return 'vimeo';
    } else {
        return 'unknown';
    }
} 

/*
* Code to hide admin bar | Site - allprograms.tech
*/
add_filter('show_admin_bar', '__return_false'); 


function get_stripe_file(){
     // Include stripe library
     require_once('stripe/init.php');  
     // Set API key
     \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
}

// Handle Stripe payment info
add_action( 'wp_ajax_custom_subscription_payment_getway' , 'subscription_payment_getway_call_back' );
add_action( 'wp_ajax_nopriv_custom_subscription_payment_getway' , 'subscription_payment_getway_call_back' );

function subscription_payment_getway_call_back(){
    // Fetch session data info 
    $session_data = $_SESSION['abandand_user_info']; 
    $payment_id = $statusMsg = $api_error = '';
    $ordStatus = 'error';
    $form_data      = $_POST['form_data'];
    $subscriber_id = $_POST['subscriber_id'];
    $current_user_id = get_current_user_id() ?: 1;
    parse_str($form_data, $data);
    // cancle plan if already subscriber id 
    if(!empty($subscriber_id)){
         require_once('stripe/init.php');  
        // // Set API key
         \Stripe\Stripe::setApiKey(STRIPE_API_KEY);                
         $stripe = new \Stripe\StripeClient(
            'sk_test_51HuWprGmKHCBqlqFfMNUqcjS7QHv5i1kgaSFKVlnHy8LLAya2TwG1KutK5avkZOSW16CW4U3TeUI7RC862XFhEDG00eLWtDMIY'
          );
         $cancle_array = $stripe->subscriptions->cancel(
            $subscriber_id
          );

          
          if( $cancle_array['status'] == 'canceled' ){
            $paymentData['subscriber_id'] = $subscriber_id;
            $paymentData['subscriber_customer_id']  = $cancle_array['customer'];
            $paymentData['subscriber_plan_id']  = $cancle_array['plan']['id'];
            $paymentData['subscriber_plan_amount']  = $cancle_array['plan']['amount'];
            $paymentData['subscriber_plan_currency']  = $cancle_array['plan']['currency'];
            $paymentData['subscriber_plan_interval']  = $cancle_array['plan']['interval'];
            $paymentData['subscriber_plan_interval_count']  = $cancle_array['plan']['interval_count'];
            $paymentData['subscriber_created']  = date("Y-m-d H:i:s", $cancle_array['created']);
            $paymentData['subscriber_plan_activate_date']  = date("Y-m-d H:i:s", $cancle_array['current_period_start']);
            $paymentData['subscriber_plan_end_date']  = date("d-m-Y", strtotime(date("Y-m-d H:i:s", $cancle_array['current_period_end'])));
            $paymentData['subscriber_status']  = $cancle_array['status'];
            $paymentData['abandand_post_id'] = $_SESSION['abandand_post_id'];
            $paymentData['user_payment_by'] = $current_user_id;
            $paymentData['customer_name'] = $name;
            // Set current status 
                if($current_user_id){
                    update_user_meta( $current_user_id , 'payment_info', $paymentData);

                    $statusMsg = array(
                        'status' => $cancle['status'],
                        'message' => 'Your plan cancelled successfully ', 
                        'plan_experiy_date' => $paymentData['subscriber_plan_end_date'],
                        'payment_data' => $paymentData
                    );
                }
          }else{

                $statusMsg = array(
                    'status' => $paymentData['subscriber_status'],
                    'message' => 'Something went wrong!!', 
                );

          }
          
       
        return wp_send_json( $statusMsg );
    }

    // Check whether stripe token is not empty
    if(!empty($data['stripeToken'])){
       // Retrieve stripe token from the submitted form data
        $token  = $data['stripeToken'];
        $name =   $session_data['user_name'] ?: 'Test'; 
        $email =  $session_data['user_email'] ?: 'test123@mailinator.com'; 
        // Plan info
        $planID = 'Subscription';
        $planInfo = '';
        $planName = 'prod_HKLJEqrCBoxgHx';
        $planInterval = 'day';
        $price = 100;
        // get stripe library
        require_once('stripe/init.php');  
        // Set API key
        \Stripe\Stripe::setApiKey(STRIPE_API_KEY);    
        // Add customer to stripe
        try { 
            $customer = \Stripe\Customer::create(array(
                'name' => $name,
                'email' => $email,
                'source'  => $token,
                'shipping' => [
                    'name' => $name,
                    'address' => [
                      'line1' => '510 Townsend St',
                      'postal_code' => '98140',
                      'city' => 'San Francisco',
                      'state' => 'CA',
                      'country' => 'US',
                    ],
                  ],
            ));
        }catch(Exception $e) { 
            $api_error = $e->getMessage(); 
        }
        
        if(empty($api_error) && $customer){
            // Convert price to cents
            $priceCents = 100; 
	 	
            // Create a plan
            try {
                $plan = \Stripe\Plan::create(array(
                    "product" => [
                        "name" => $planName
                    ],
                    "amount" => $priceCents,
                    "currency" => 'USD',
                    "interval" => $planInterval,
                    "interval_count" => 1
                ));
            }catch(Exception $e) {
                $api_error = $e->getMessage();
            }
            
            if(empty($api_error) && $plan){
                // Creates a new subscription
                try {
                    $subscription = \Stripe\Subscription::create(array(
                        "customer" => $customer->id,
                        "items" => array(
                            array(
                                "plan" => $plan->id,
                            ),
                        ),
                    ));
                }catch(Exception $e) {
                    $api_error = $e->getMessage();
                }
              
                if(empty($api_error) && $subscription){
                    // Retrieve subscription data
                    $subsData = $subscription->jsonSerialize();

                    // Check whether the subscription activation is successful
                    if($subsData['status'] == 'active'){
                        // Subscription info
                        $paymentData['subscriber_id'] = $subsData['id'];
                        $paymentData['subscriber_customer_id']  = $subsData['customer'];
                        $paymentData['subscriber_plan_id']  = $subsData['plan']['id'];
                        $paymentData['subscriber_plan_amount']  = ($subsData['plan']['amount']);
                        $paymentData['subscriber_plan_currency']  = $subsData['plan']['currency'];
                        $paymentData['subscriber_plan_interval']  = $subsData['plan']['interval'];
                        $paymentData['subscriber_plan_interval_count']  = $subsData['plan']['interval_count'];
                        $paymentData['subscriber_created']  = date("Y-m-d H:i:s", $subsData['created']);
                        $paymentData['subscriber_plan_activate_date']  = date("Y-m-d H:i:s", $subsData['current_period_start']);
                        $paymentData['subscriber_plan_end_date']  = date("d-m-Y", strtotime(date("Y-m-d H:i:s", $subsData['current_period_end'])));
                        $paymentData['subscriber_status']  = $subsData['status'];
                        $paymentData['abandand_post_id'] = $_SESSION['abandand_post_id'];
                        $paymentData['user_payment_by'] = $current_user_id;
                        $paymentData['customer_name'] = $name;
                        // insert transtation post type
                        //return wp_send_json( $subsData );
                        $_SESSION['payment_info'] = $subsData;
                        $args = array(
                            'post_title'    => 'New Subscriber User '.$name.' Subscriber ID '.$paymentData['subscriber_id'],
                            'post_status'   => 'publish',
                            'post_type'     => 'agl_translations',
                            'post_author'   => $current_user_id,
                        );
                       // Insert the post into the database
                        $payment_id = wp_insert_post( $args );
                        if($payment_id){
                                // Update post meta info
                                update_post_meta($payment_id , 'payment_info', $paymentData);
                                // Update payment info using user id 
                                update_user_meta( $current_user_id , 'payment_info', $paymentData);
                             }

                        // set success response    
                        $statusMsg = array(
                            'status' => $paymentData['subscriber_status'],
                            'translations_post_id' => $payment_id ,
                            'subscriber_id' => $paymentData['subscriber_id'],
                            'message' => 'Your Subscription Payment has been Successful', 
                            'plan_experiy_date' => $paymentData['subscriber_plan_end_date'],
                            'payment_data' => $paymentData
                        );
                        
                    }else{
                        $statusMsg = "Subscription activation failed!";
                    }
                }else{
                    $statusMsg = "Subscription creation failed! ".$api_error;
                }
            }else{
                $statusMsg = "Plan creation failed! ".$api_error;
            }
        }else{ 
            $statusMsg = "Invalid card details! $api_error"; 
        }
    }else{
        //Token empty
        $statusMsg = "Token Not Found"; 
    }

    return wp_send_json( $statusMsg );

    wp_die();


}


add_action( 'wp_ajax_site_creation', 'site_creation_call_back');
add_action( 'wp_ajax_nopriv_site_creation', 'site_creation_call_back');

function site_creation_call_back(){
    $site_title = $_POST['site_title'];
    $end_point = sanitize_title($_POST['site_path']);
    $site_path = $end_point;
    $user_id = $_SESSION['cusrid'];
    // Check payment Success 
    $payment_status = get_user_meta( get_current_user_id(), 'payment_info' , true);
    $payment_id = $payment_status['payment_id'];
    $subscriber_status = $payment_status['subscriber_status'];
    // Payment status is active
    if($subscriber_status == 'active'){
        if(!empty($site_title) && !empty($site_path) && !empty($user_id )){
            // Set correct site path localhost
            $domain_url = get_home_url(); 
            if($domain_url['host'] == 'localhost'){
                $path = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $domain_url['path']); 
                $site_path = $path.'/'.$site_path;
            }
            // create new site 
            $blog_id = create_new_site( '/', $site_path , $site_title, $user_id);
            // Update blog id user meta 
            update_user_meta($user_id , 'blog_id', $blog_id );
            //store session 
            $_SESSION['site_title'] = $site_title;
            $_SESSION['site_path'] = $site_path;
            $_SESSION['end_point'] = $end_point;
            $_SESSION['blog_id'] = $blog_id;
            // Set status
            $status  = array(
               'message' => 'Landing page created successfully. Your landing page id is '.$blog_id,
               'new_site_url' => get_site_url($blog_id),
               'status_code' => 1
           );
       }else{
           $status = array(
               'message' => 'Please check input field again Not getting proper data',
               'status_code' => 2
           );
       }
    }else{
        $status = array(
            'message' => 'Please subscribe a plan to create sub site',
            'status_code' => 0
        );
    }
   
    return wp_send_json( $status );
}


/*
* Save meta data
*/
add_action( 'wp_ajax_update_sample_seo_meta', 'update_sample_seo_meta_call_back');
add_action( 'wp_ajax_nopriv_update_sample_seo_meta', 'update_sample_seo_meta_call_back');
function update_sample_seo_meta_call_back(){

    $meta_image = $_POST['meta_image'];
    $meta_title = $_POST['meta_title'];
    $meta_desc = $_POST['meta_desc'];
    $meta_gsv = $_POST['meta_gsv'];
    $meta_gas = $_POST['meta_gas'];

    $user_id = $_SESSION['cusrid'];

    if(!empty($user_id)){

        // Save to session
        $_SESSION['meta_image'] = $meta_image;
        $_SESSION['meta_title'] = $meta_title;
        $_SESSION['meta_desc'] = $meta_desc;
        $_SESSION['meta_gsv'] = $meta_gsv;
        $_SESSION['meta_gas'] = $meta_gas;

        // Save to user meta
        update_user_meta($user_id, 'sample_meta_image', $meta_image);
        update_user_meta($user_id, 'sample_meta_title', $meta_title);
        update_user_meta($user_id, 'sample_meta_desc' , $meta_desc);
        update_user_meta($user_id, 'sample_meta_gsv' , $meta_gsv);
        update_user_meta($user_id, 'sample_meta_gas' , $meta_gas);

        // Set status
        $status  = array(
            'message' => 'Meta details has been saved.',
            'status' => 1
        );

    } else {
        $status = array(
            'message' => 'Unable to save data. User id not found.',
            'status' => 2
        );
    }
 
    return wp_send_json( $status );
}

/*
* Ajax File Upload Code WordPress
*/
add_action( 'wp_ajax_file_upload', 'file_upload_callback' );
add_action( 'wp_ajax_nopriv_file_upload', 'file_upload_callback' );
function file_upload_callback() {
    $arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');
    if (in_array($_FILES['file']['type'], $arr_img_ext)) {
        $upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));
        // echo $upload['url'];
        echo json_encode($upload);
    }
    wp_die();
}


// Session distroy on click signup button 
add_action('wp_ajax_auto_generated_session_distroy', 'auto_generated_session_distroy_call_back');
add_action('wp_ajax_nopriv_auto_generated_session_distroy', 'auto_generated_session_distroy_call_back');

function auto_generated_session_distroy_call_back(){
    if($_POST['current_session_status'] == 1){
        session_destroy();
        $data['current_session_status'] = 0;
        $data['sign_up_url'] = get_site_url().'/registration';
        return wp_send_json($data);
    }
    wp_die();
}


function isValidDomainName($domain_name) {
    return (preg_match("/^([a-zd](-*[a-zd])*)(.([a-zd](-*[a-zd])*))*$/i", $domain_name) //valid characters check
    && preg_match("/^.{1,253}$/", $domain_name) //overall length check
    && preg_match("/^[^.]{1,63}(.[^.]{1,63})*$/", $domain_name) ); //length of every label
 }

// Domain creation save
add_action('wp_ajax_domain_save', 'domain_save_call_back');
add_action('wp_ajax_nopriv_domain_save', 'domain_save_call_back');

function domain_save_call_back(){
    // fetch user id 
    $current_user_id = get_current_user_id(); 
    $domain_name = str_replace(' ', '', $_POST['domain_name']);
    // check condition
    if($current_user_id && !empty($domain_name) && isValidDomainName($domain_name)){
        // update user meta info 
        update_user_meta($current_user_id, 'domain_name', $domain_name );
        $data['message'] = 'Congratulations your domain is saved in our record!!';
        $data['status_code'] = 1;
    } else if (!isValidDomainName($domain_name)) {
        $data['message'] = 'Invalid Domain Name.';
        $data['status_code'] = 0;
    } else {
        $data['message'] = 'Please Login and Enter Domain to save domain!!';
        $data['status_code'] = 0;
    }

    return wp_send_json($data); 
}

/*
* WordPress logout URL to login page
*/
add_action( 'wp_logout', 'wp_logout_redirect_login');
function wp_logout_redirect_login(){
    $redirect = home_url() . '/log-in/';
    wp_safe_redirect( $redirect );
    exit();
}


/*
* Auto Generate Service Templates
*/
function get_all_template(){
    $template_array = array(
        'lp-template-sample' => 'sample',
        'lp-template-Sample' => 'Sample'
    );
    return $template_array;
}

/*
* Save Template
*/
add_action( 'wp_ajax_auto_generated_save_template', 'auto_generated_lp_templates_save');
add_action( 'wp_ajax_nopriv_auto_generated_save_template', 'auto_generated_lp_templates_save');
function auto_generated_lp_templates_save(){

    $template_name      = $_POST['template_name'];
    $user_id            = $_SESSION['cusrid'];
   
    if($user_id){
        update_user_meta( $user_id, 'lp_selected_template', $template_name );
        $output['message'] = 'Template saved successfully.';
        $output['status_code'] = 1;
        $output['template_name'] = $template_name;
    } else {
        $output['message'] = 'Unable to save template. Try again later.';
        $output['status_code'] = 0;
    }

    return wp_send_json( $output );
}


// Front page live template selection using blogid 
function lp_template_select($blog_id){
    // Get user id 
   
    $user_id  = get_users_of_blog($blog_id)[0]->user_id; 
    // get template 
    $template_name = get_user_meta($user_id, 'lp_selected_template' , 'true' );
    $prevData = get_user_meta($user_id, 'lp_template_data', true);
    $network_id = 1; 
    $autolpSettings = unserialize(get_network_option($network_id, 'sample_network_options'));
    $_SESSION['prevData'] =  $prevData;
    $_SESSION['autolpSettings'] =  $autolpSettings;
    if($template_name == 'lp-template-Sample'){
        return get_template_part('template-parts/'.$template_name);
    }else {
        return get_template_part('template-parts/lp-template-sample');
    }

}

// preview image save
add_action('wp_ajax_preview_image', 'preview_image_call_back');
add_action('wp_ajax_nopriv_preview_image', 'preview_image_call_back');

function preview_image_call_back(){
    
    if(isset($_POST['file']))  {

        // Base 64 Image
        $imageBase64 = $_POST['file'];  
        
        // Replace
        $imageBase64 = trim($imageBase64);
        $imageBase64 = str_replace('data:image/png;base64,', '', $imageBase64);
        $imageBase64 = str_replace('data:image/jpg;base64,', '', $imageBase64);
        $imageBase64 = str_replace('data:image/jpeg;base64,', '', $imageBase64);
        $imageBase64 = str_replace('data:image/gif;base64,', '', $imageBase64);
        $imageBase64 = str_replace( ' ', '+', $imageBase64 );    

        // Original content
        $imageData = base64_decode($imageBase64);
        
        // Binary data
        // $binimage = imagecreatefromstring($imageData);
        
        // File Name
        if (isset($_POST['file_name'])) {
            $filename = $_POST['file_name'];  
            $filepart = explode('.', $filename);
            $ext = substr(strrchr($filename, '.'), 1);
            $imageName = $filepart['0'] . time() . '.' . $ext;
        } else {
            $imageName = time() . '.jpg';
        }
        
        $upload = wp_upload_bits($imageName, null, $imageData);

        echo json_encode($upload);
    }

    wp_die();  
}


function remove_site_preview_empty($prevData){
    $template_name = get_user_meta($currentUserId, 'lp_selected_template' , 'true' );
    $new_array = $prevData;
    $allEmpty = array();
    if($template_name == 'lp-template-Sample'){
        $remove_key = array(
            'user_name',
            'user_email',
            'email_generated_code',
            'user_everified',
            'counter_value',
            'user_password',
            'user_cpassword',
            'save_changes',
            'site_title',
            'site_path',
            'payment_status',
            'site_domain',
            'subscriber_status',
            'section2_repeater_images',
            'section3_repeater_images',
            'section4_image',
            'section5_bg_image',
            'section6_repeater_images',
            'section7_left_Image',
            'section8_repeater_images',
            'section9_bg_image',
            'section10_repeater_images',
            'section11_left_Image',
            'section13_bg_image',
            'section15_client_image',
            'section17_bg_image',
        );
    }else{
        $remove_key = array(
            'user_name',
            'user_email',
            'email_generated_code',
            'user_everified',
            'counter_value',
            'user_password',
            'user_cpassword',
            'save_changes',
            'site_title',
            'site_path',
            'payment_status',
            'site_domain',
            'subscriber_status',
            'group-customer',
            'group-insta',
            'group-why-sample',
            'group-three-steps',
            'group-ai-repeater',
            'group-automated-sample',
            'group-faq-left',
            'group-faq-right',
            'group-footer-site-map',
            'banner_heading_fs'
          );
        
    }
    
      foreach($remove_key AS $key ){
        unset($new_array[$key]);
      }
      
      // check if whole tab section empty
      if(isset($new_array)){
        foreach( $new_array as $key => $val ) {
              if(!empty($new_array[$key])){
                  if(is_array($new_array[$key][0])){
                      // check for repeater array value
                      foreach($new_array[$key][0] as $key => $value){
                          if( empty($value) || (trim($value) === '')){
                            array_push($allEmpty, 0);
                          }else{
                            array_push($allEmpty, 1);
                          }
                      }
                  }else{
                      if(empty($new_array[$key][0]) || ctype_space($new_array[$key][0]) === '' || (ctype_space($new_array[$key]) === '' ) || (trim($new_array[$key]) === '')) {
                        array_push($allEmpty, 0);
                      }else{
                        array_push($allEmpty, 1);
                      }
                  } 
                  
              }else{
                array_push($allEmpty,0); 
              }
        }
      }

      return $allEmpty; 
}



function wpse23007_redirect(){
    if( is_admin() && !defined('DOING_AJAX') && ( current_user_can('administrator') || current_user_can('subscriber') || current_user_can('contributor') ) ){
      wp_redirect(home_url().'/registration');
      exit;
    }
  }
  

function check_repeater_value($data,$repeater_keys){
      $repeater_empty = array();
    //   $remove_filter  = array_map('array_filter', $data);
    //   $data = array_filter($remove_filter);
      // check repeater field value
      if(isset($data)){
        foreach($data as $key => $value){
            foreach($repeater_keys as $repeater_key => $value){
                if(array_column($data, $repeater_keys[$repeater_key])[$key] == ''){
                    array_push($repeater_empty,0); 
                }else{
                    array_push($repeater_empty,$key); 
                }
            }
          } 
          return $repeater_empty;
      }
      
  }


/*
* Information for Description
*/
function lineBreakTags() {
    $string = '<br class="br-mb">, <br class="br-tb">, <br class="br-dt">';
    $flags = ENT_COMPAT;
    $result = htmlentities ($string, $flags);

    return "<i class='description f12'><span data-transkey=\"lineBreakMessage\">line break for mobile, tablet and desktop view.</span>
    <span class='text-dark'>$result</span>
    </i>";

}

// call transaltion functions file
require_once('inc/translations/translation-functions.php');  