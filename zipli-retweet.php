<?php
/*
Plugin Name: Zip.li Retweet
Plugin URI: http://wordpress.org/extend/plugins/zipli-retweet/
Description: Adds a retweet button to all WordPress posts in your blog and retweet the wordpress new post to twitter.
Author: Apiki
Version: 0.2.6
Author URI: http://apiki.com/
*/

/*  Copyright 2009  Apiki (email : opensource@apiki.com)

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class ZIPLI_Retweet {

    function ZIPLI_Retweet()
    {
        //install the options in DB
        add_action( 'activate_zipli-retweet/zipli-retweet.php', array( &$this, 'install' ) );
        //add the menu zipli-retweet in admin
        add_action( 'admin_menu', array( &$this, 'menu' ) );
        //add Polls Header
        add_action( 'wp_head', array( &$this, 'zipli_header' ) );
        //add new post in twitter
        add_action( 'publish_post', array( &$this, 'post_to_twitter' ) );
        //Define translation for admin
        add_action( 'init', array( &$this, 'textdomain' ) );
        //insert the retweet button on the content of blog
        add_filter( 'the_content', array( &$this,'zipli_button' ) );
        //Call the function to insert the JavaScript for admin
        add_action( 'wp_print_scripts', array( &$this, 'scripts' ) );
        // Start this plugin once all other files and plugins are fully loaded
        add_action( 'plugins_loaded', array(&$this, 'after_plugins_loaded'));

    }

    function install()
    {
        add_option( 'zipli_retweet_user', 'ZipliRetweet', 'yes' );
        add_option( 'zipli_retweet_password', 'insertyourpass', 'yes' );
        add_option( 'zipli_retweet_message', 'Post: [title] [shorturl]', 'yes' );
        add_option( 'zipli_retweet_btnaut' , 1 );
    }

    /**
     * Create menu in Wordpress admin sidebar
     */
    function menu()
    {
        add_menu_page( 'Zip.li Retweet', 'Zip.li Retweet', 'manage_zipli', 'zipli-retweet/zipli-retweet-settings.php', '' , plugins_url( 'zipli-retweet/assets/images/zipli.png' ) );
        add_submenu_page( 'zipli-retweet/zipli-retweet-settings.php', __( 'Settings', 'zipli-retweet' ), __( 'Settings', 'zipli-retweet' ), 'manage_zipli', 'zipli-retweet/zipli-retweet-settings.php' );
        add_submenu_page( 'zipli-retweet/zipli-retweet-settings.php', __( 'Uninstall', 'zipli-retweet' ), __( 'Uninstall', 'zipli-retweet' ), 'manage_zipli', 'zipli-retweet/zipli-retweet-uninstall.php' );
    }

    function zipli_header()
    {
    $zipli_btn = plugins_url( 'zipli-retweet/assets/images/zipli_btn.png' );

    echo '<!--Begin style design by zip.li-retweet  -->'."\n";
        global $text_direction;
        echo '<style type="text/css">'."\n";
            echo '.zipli-retweet-button span { display: block; text-indent: -2000em; }' . "\n";
            echo '.zipli-retweet-button { outline: none; display: block; width: 110px; height: 30px; background:url(' . $zipli_btn .') no-repeat 0 0 ; }' . "\n";
            echo '.zipli-retweet-button:hover, .zipli-retweet-button:focus { background:url(' . $zipli_btn . ') no-repeat 0 -30px; }' . "\n";
        echo '</style>'."\n";
    echo '<!-- End style design by zip.li-retweet  -->'."\n";
    }
    /**
     *
     * @global native wordpress variable $post referer to permanlink from post
     * @param <type> $content
     * @return String Insert in content the retweet button
     */
    function zipli_button($content)
    {
        global $post;
    
        $longUrl = get_permalink($post->ID);
        $twitterUserName = get_option( 'zipli_retweet_user' );
        $button = sprintf( '<a href="http://zip.li/api?method=retweet&amp;longUrl=%s&amp;twitterUsername=%s" class="zipli-retweet-button"><span>%s</span></a>',
            $longUrl,
            $twitterUserName,
            __( 'Retweet this post', 'zipli-retweet' )
        );

        if( get_option( 'zipli_retweet_btnaut' ) ) :
            return $content . $button;
        else :
            return $content;
        endif;       
    }

    /**
     *
     *Create the textdomain for translation language
     */
    function textdomain()
    {
        load_plugin_textdomain('zipli-retweet','wp-content/plugins/zipli-retweet/assets/languages');
    }

    function post_to_twitter( $postID )
    {
	$post = get_post( $postID );

        $message = $this->retweet_get_message( $postID );
        $this->post_retweet( get_option( 'zipli_retweet_user' ), get_option( 'zipli_retweet_password' ), $message );
    }

    function retweet_get_message( $postID )
    {
            require_once( ABSPATH . 'wp-includes/class-snoopy.php');

            global $post;

            $snoopy = new Snoopy();
            $url_post = get_permalink( $postID );
            $result = $snoopy->fetch('http://zip.li/api?longUrl='. $url_post .'');

            $proto = get_option( 'zipli_retweet_message' );
            $post = get_post( $postID );
            $proto = str_replace( "[title]", $post->post_title, $proto );
            $proto = str_replace( "[shorturl]", $snoopy->results, $proto );
            return $proto;
    }

    //Standard curl function, handles actual submission of message to twitter
    function post_retweet( $twitter_username, $twitter_password, $message )
    {
            $url = 'http://twitter.com/statuses/update.xml';
            $curl_handle = curl_init();
            curl_setopt( $curl_handle, CURLOPT_URL, "$url" );
            curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 2 );
            curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $curl_handle, CURLOPT_POST, 1 );
            curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, "status=$message&source=twitpress" );
            curl_setopt( $curl_handle, CURLOPT_USERPWD, "$twitter_username:".str_rot13( $twitter_password ) );
            $buffer = curl_exec( $curl_handle );
            curl_close( $curl_handle );
    }

    /**
     * Show message alert from user about user name in plugin page
     */
    function check_user_zr()
    {
        //Show message only plugins page
        if ( get_option( 'zipli_retweet_user' ) == 'ZipliRetweet' ) :
            if ( strpos( $_SERVER['REQUEST_URI'], 'plugins.php' ) !== false ) :
            //echo 'merda';
            echo sprintf( "<div class = 'error'><p>%s <strong>Zip.li Retweet</strong> %s <a href = 'admin.php?page=zipli-retweet/zipli-retweet-settings.php'>%s</a></p></div>" , __( 'Warning:', 'zipli-retweet' ), __( 'Change the User Name', 'zipli-retweet' ), __( 'Plugin Settings', 'zipli-retweet' ) );
            endif;
        endif;
    }

    /**
     * Call the function after all plugins are loaded
     */
    function after_plugins_loaded()
    {
        // hook the admin notices action
        add_action( 'admin_notices', array(&$this,'check_user_zr') );
    }

    /**
    * This function insert JS in admin plugin
    */
    function scripts()
    {
        if ( strpos( $_GET['page'], 'zipli-retweet' ) !== false ) :
            $zipli_scripts_ver = filemtime( dirname( __FILE__ ) . '/assets/javascript/zipli-backend-scripts.js' );
            wp_enqueue_script( 'zipli.scripts', WP_PLUGIN_URL . '/zipli-retweet/assets/javascript/zipli-backend-scripts.js', array( 'jquery' ), $zipli_scripts_ver );
        endif;
    }

}

$role = get_role('administrator');
	if(!$role->has_cap('manage_zipli')) {
		$role->add_cap('manage_zipli');
        }

$zipli_retweet = new ZIPLI_Retweet();
?>