<?php 

/*
Plugin Name: Flairbees - Post Word Filter and Replace
Description:    To filter and replace post words.
Version: 1.0.0
Author: Greatkhanjoy
Author URI: https://studio.envato.com/users/Greatkhanjoy
Text Domain: flairbees-post-word-filter-and-replace
Domain Path: /languages
*/


if(! defined('ABSPATH')) exit ;

define( 'FLBWF_URI', __FILE__ );

    class flairbeesWordFilter{
        function __construct()
        {
            add_action( 'admin_menu', array($this, 'adminMenu'));
            add_action( 'admin_init', array($this, 'wordFilterSettings') );

            if(get_option( 'flb_word_to_filter')) add_filter( 'the_content', array($this, 'filterLogic') ); 

            register_deactivation_hook( FLBWF_URI, array( $this, 'flbwf_deactivate_plugin' ) );
            register_uninstall_hook( FLBWF_URI, array( $this, 'flbwf_uninstall') );

        }
        



        function wordFilterSettings(){
            add_settings_section( 'wordReplacementSection', null, null, 'flb-word-filter-options' );
            register_setting( 'wordReplacementGroup', 'replaceText');
            add_settings_field( 'replacement-text-field', esc_html__('Replace with filtered text', 'flairbees-post-word-filter-and-replace'), array($this, 'replaceTextHTML'), 'flb-word-filter-options', 'wordReplacementSection' );
        }

        function replaceTextHTML(){
            ?>
            <input type="text" name="replaceText" value="<?php echo esc_attr(get_option('replaceText')) ?>">
            <p class="descriptio"><?php echo esc_html__('Leave blank to simply remove the filtered words', 'flairbees-post-word-filter-and-replace') ?></p>
            <?php
        }
        function filterLogic($content){
            $badWords = explode(',', get_option('flb_word_to_filter'));
            $badWordsTrim = array_map('trim', $badWords);
            return str_ireplace($badWordsTrim, get_option('replaceText'), $content);
        }
        

        function adminMenu(){
            

            $mainPageHook = add_menu_page( 
                esc_html__('FlairBees Word Filter', 'flairbees-post-word-filter-and-replace'),
                esc_html__('Word Filter', 'flairbees-post-word-filter-and-replace'), 
                'manage_options', 'flbWordFilter', 
                array($this, 'adminMenuPage'), 
                'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDMyIDMyIiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAzMiAzMiIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGcgaWQ9IkxheWVyXzIiLz48ZyBpZD0iTGF5ZXJfMyIvPjxnIGlkPSJMYXllcl80Ii8+PGcgaWQ9IkxheWVyXzUiLz48ZyBpZD0iTGF5ZXJfNiIvPjxnIGlkPSJMYXllcl83Ii8+PGcgaWQ9IkxheWVyXzgiLz48ZyBpZD0iTGF5ZXJfOSIvPjxnIGlkPSJMYXllcl8xMCIvPjxnIGlkPSJMYXllcl8xMSIvPjxnIGlkPSJMYXllcl8xMiIvPjxnIGlkPSJMYXllcl8xMyIvPjxnIGlkPSJMYXllcl8xNCIvPjxnIGlkPSJMYXllcl8xNSIvPjxnIGlkPSJMYXllcl8xNiIvPjxnIGlkPSJMYXllcl8xNyIvPjxnIGlkPSJMYXllcl8xOCIvPjxnIGlkPSJMYXllcl8xOSIvPjxnIGlkPSJMYXllcl8yMCIvPjxnIGlkPSJMYXllcl8yMSI+PGc+PHBhdGggZD0iTTIxLjcwOSwxNGMtMC4xNjk5LDAtMC4zNDIzLTAuMDQzLTAuNS0wLjEzNDNjLTAuNDc4LTAuMjc2OS0wLjY0MTEtMC44ODgyLTAuMzY0Ny0xLjM2NjdsMC43ODgxLTEuMzYyMyAgICBsLTEuMjUxNS0wLjcyMDJjLTAuMzQyOC0wLjE5NzMtMC41MzcxLTAuNTc4MS0wLjQ5NTYtMC45NzE3bDAuMDMwOC0wLjIzMzlDMTkuOTI2OCw5LjE0MTEsMTkuOTM5OSw5LjA3MzIsMTkuOTM5OSw5ICAgIHMtMC4wMTMyLTAuMTQxMS0wLjAyMzktMC4yMTA5bC0wLjAzMDgtMC4yMzM5Yy0wLjA0MTUtMC4zOTM2LDAuMTUyOC0wLjc3NDQsMC40OTU2LTAuOTcxN2wxLjI1Mi0wLjcyMDJsLTEtMS43M2wtMS4yNDUxLDAuNzE0NCAgICBDMTkuMDQ1NCw2LjA0MjUsMTguNjIyMSw2LjAyLDE4LjMwMzcsNS43OWMtMC4yNzczLTAuMjAxMi0wLjUyODMtMC4zNDk2LTAuNzY3Ni0wLjQ1NTFjLTAuMzYyMy0wLjE2MDItMC41OTYyLTAuNTE5LTAuNTk2Mi0wLjkxNSAgICBWM2gtMnYxLjQxOTljMCwwLjM5Ni0wLjIzMzksMC43NTQ5LTAuNTk2MiwwLjkxNWMtMC4yMzQ0LDAuMTAzLTAuNDg1NCwwLjI1MDUtMC43OSwwLjQ2NDQgICAgQzEzLjIzNDksNi4wMjIsMTIuODE3NCw2LjA0LDEyLjQ4MSw1Ljg0NjdsLTEuMjM0OS0wLjcxMTlsLTEsMS43M2wxLjI0NDYsMC43MTk3YzAuMzQxOCwwLjE5NzgsMC41MzUyLDAuNTc4MSwwLjQ5NDEsMC45NzA3ICAgIEwxMS45NjQ0LDguNzI5QzExLjk1MzEsOC44MTkzLDExLjkzOTksOC45MDU4LDExLjkzOTksOXMwLjAxMzIsMC4xODA3LDAuMDI0NCwwLjI3MWwwLjAyMDUsMC4xNzM4ICAgIGMwLjA0MSwwLjM5MjYtMC4xNTIzLDAuNzcyOS0wLjQ5NDEsMC45NzA3bC0xLjI0NDYsMC43MTk3bDAuNzg5NiwxLjM2MzhjMC4yNzY0LDAuNDc4NSwwLjExMzMsMS4wODk4LTAuMzY0NywxLjM2NjcgICAgYy0wLjQ3ODUsMC4yNzczLTEuMDg5OCwwLjExMzMtMS4zNjY3LTAuMzY0N2wtMS4yOS0yLjIzYy0wLjEzMjgtMC4yMy0wLjE2ODktMC41MDI5LTAuMTAwNi0wLjc1OTMgICAgYzAuMDY4OC0wLjI1NTksMC4yMzYzLTAuNDc0NiwwLjQ2NTgtMC42MDc0TDkuOTM5OSw5LjAwMmMwLTAuMDAxLDAtMC4wMDI5LDAtMC4wMDM5TDguMzc5NCw4LjA5NTcgICAgYy0wLjQ3OC0wLjI3NjQtMC42NDE2LTAuODg4Mi0wLjM2NTItMS4zNjYybDItMy40NmMwLjI3NTQtMC40NzgsMC44ODYyLTAuNjQyMSwxLjM2NDctMC4zNjYybDEuNTYxLDAuODk5NFYyICAgIGMwLTAuNTUyMiwwLjQ0NzgtMSwxLTFoNGMwLjU1MjIsMCwxLDAuNDQ3OCwxLDF2MS43OTg4bDEuNTYyNS0wLjg5NjVjMC40Nzg1LTAuMjcyOSwxLjA4ODQtMC4xMDk0LDEuMzYzMywwLjM2NzJsMiwzLjQ2ICAgIGMwLjEzMjgsMC4yMywwLjE2ODksMC41MDM0LDAuMTAwMSwwLjc1OThTMjMuNzI5LDcuOTY0NCwyMy40OTksOC4wOTY3bC0xLjU1OTEsMC44OTdjMCwwLjAwMjQsMCwwLjAwNDQsMCwwLjAwNjMgICAgczAsMC4wMDM5LDAsMC4wMDYzbDEuNTU5MSwwLjg5N2MwLjIzLDAuMTMyMywwLjM5NzksMC4zNTExLDAuNDY2OCwwLjYwNzRjMC4wNjg4LDAuMjU2OCwwLjAzMjcsMC41MzAzLTAuMTAwMSwwLjc2MDNsLTEuMjksMi4yMyAgICBDMjIuMzkwMSwxMy44MjEzLDIyLjA1NDIsMTQsMjEuNzA5LDE0eiIgZmlsbD0iI0Y1RDgwMyIvPjwvZz48Zz48cGF0aCBkPSJNMTUuOTM4LDEyYy0xLjY1NDMsMC0zLTEuMzQ1Ny0zLTNzMS4zNDU3LTMsMy0zczMsMS4zNDU3LDMsM1MxNy41OTIzLDEyLDE1LjkzOCwxMnogTTE1LjkzOCw4ICAgIGMtMC41NTEzLDAtMSwwLjQ0ODctMSwxczAuNDQ4NywxLDEsMXMxLTAuNDQ4NywxLTFTMTYuNDg5Myw4LDE1LjkzOCw4eiIgZmlsbD0iIzAwQUNCQSIvPjwvZz48Zz48cGF0aCBkPSJNMTMsMzFjLTAuMjA3LDAtMC40MTIxLTAuMDY0NS0wLjU4NDUtMC4xODlDMTIuMTU0MywzMC42MjMsMTIsMzAuMzIxMywxMiwzMHYtOC4zODE4bC01LjQ0NzMtMi43MjM2ICAgIEM2LjIxMzksMTguNzI1MSw2LDE4LjM3ODksNiwxOHYtNWMwLTAuNTUyMiwwLjQ0NzgtMSwxLTFoMThjMC41NTIyLDAsMSwwLjQ0NzgsMSwxdjVjMCwwLjM3ODktMC4yMTM5LDAuNzI1MS0wLjU1MjcsMC44OTQ1ICAgIEwyMCwyMS42MTgyVjI4YzAsMC40MzA3LTAuMjc1NCwwLjgxMjUtMC42ODM2LDAuOTQ4N2wtNiwyQzEzLjIxMjksMzAuOTgyOSwxMy4xMDY0LDMxLDEzLDMxeiBNOCwxNy4zODE4bDUuNDQ3MywyLjcyMzYgICAgQzEzLjc4NjEsMjAuMjc0OSwxNCwyMC42MjExLDE0LDIxdjcuNjEyOGw0LTEuMzMzNVYyMWMwLTAuMzc4OSwwLjIxMzktMC43MjUxLDAuNTUyNy0wLjg5NDVMMjQsMTcuMzgxOFYxNEg4VjE3LjM4MTh6IiBmaWxsPSIjMDE4MUIwIi8+PC9nPjwvZz48ZyBpZD0iTGF5ZXJfMjIiLz48ZyBpZD0iTGF5ZXJfMjMiLz48ZyBpZD0iTGF5ZXJfMjQiLz48ZyBpZD0iTGF5ZXJfMjUiLz48ZyBpZD0iTGF5ZXJfMjYiLz48L3N2Zz4=', 100
            );

            add_submenu_page( 
                'flbWordFilter', esc_html__('FlairBees Word Filter', 'flairbees-post-word-filter-and-replace'), esc_html__('Words', 'flairbees-post-word-filter-and-replace'), 'manage_options', 'flbWordFilter',array($this, 'adminMenuPage') 
            );
            
            add_submenu_page( 'flbWordFilter', esc_html__('Word Filter Options', 'flairbees-post-word-filter-and-replace'), esc_html__('Options', 'flairbees-post-word-filter-and-replace'), 'manage_options', 'flb-word-filter-options', array($this, 'optionsPage') );

            add_action( "load-{$mainPageHook}", array($this, 'mainPageAsset') ); 
        }

        function mainPageAsset(){
            wp_enqueue_style( 'filterAdminCss', plugin_dir_url( __FILE__ ) . '/assets/css/style.css');
        }

        function handleForm(){
            if(wp_verify_nonce( $_POST['flbNonce'], 'saveFilterWords') AND current_user_can('manage_options')){
                update_option( 'flb_word_to_filter', sanitize_text_field($_POST['word_to_filter']));
                ?>
                <div class="notice notice-success is-dismissible">
                    <p> <?php echo esc_html__('Your filtered words were saved.', 'flairbees-post-word-filter-and-replace') ?> </p>
                </div>
                <?php  
            }else{
                ?>
                <div class="notice settings-error is-dismissible">
                    <p> <?php echo esc_html__('Sorry! you don\'thave permission to perform that action.', 'flairbees-post-word-filter-and-replace') ?></p>
                </div>
                <?php
            }
        }

        function adminMenuPage(){
        ?>
            <div class="wrap">
                <h3>Word Filter</h3>
                <?php if($_POST['justSubmitted'] == 'true') $this->handleForm() ?>
                <form method="POST">
                    
                    <?php wp_nonce_field( 'saveFilterWords', 'flbNonce') ?>
                    <input type="hidden" name="justSubmitted" value="true">
                    <label for="word_to_filter"><?php echo esc_html('Enter a', 'flairbees-post-word-filter-and-replace') ?> <strong><?php echo esc_html('comma-separated', 'flairbees-post-word-filter-and-replace') ?></strong> <?php echo esc_html('list of words to filter from your site content', 'flairbees-post-word-filter-and-replace') ?></label>
                    <div class="word-filter_flex-container">
                        <textarea name="word_to_filter" id="word_to_filter" > <?php echo esc_textarea( get_option('flb_word_to_filter') ) ?> </textarea>
                    </div>
                    <input type="submit" id="submit" class="button button-primary" value="<?php echo esc_html__('Save Changes', 'flairbees-post-word-filter-and-replace') ?>">
                </form>
                
            </div>
        <?php
        }

        
        function optionsPage(){
        ?>
           <div class="wrap">
               <h3><?php echo esc_html__('Word filter options', 'flairbees-post-word-filter-and-replace') ?></h3>

               <form action="options.php" method="POST">
                   <?php
                    settings_errors(); 
                    settings_fields( 'wordReplacementGroup' ); 
                    do_settings_sections( 'flb-word-filter-options');  
                    submit_button();
                   ?>
               </form>
           </div>
        <?php
        }


        function flbwf_uninstall() {
            //
        }

        function flbwf_deactivate_plugin()
            {
                if( ! get_option( 'flb_word_to_filter' ) ) return;
         
                global $wpdb;
                delete_option( "replaceText" );
                delete_option( "flb_word_to_filter" );
            }
    }


    $flairbeesWordFilter = new flairbeesWordFilter();