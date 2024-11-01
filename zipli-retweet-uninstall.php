<!-- Uninstall Zip.li Retweet -->
<?php
    if( !current_user_can('install_plugins')):
        die('Access Denied');
    endif;
$base_name = plugin_basename('zipli-retweet/zipli-retweet.php');
$base_page = 'admin.php?page='.$base_name;
$mode = trim($_GET['mode']);
$zipli_settings = array('zipli_retweet_btnaut','zipli_retweet_user','zipli_retweet_password','zipli_retweet_message');

//Form Process
if( isset( $_POST['do'], $_POST['uninstall_zipli_yes'] ) ) :
    echo '<div class="wrap">';
    ?>
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'zipli-retweet/assets/images/zipli-retweet-32.png' )?>" alt="zipli-ico"/><?php _e('Uninstall Zip.li Retweet', 'zipli-retweet') ?></h2>
    <?php
    switch($_POST['do']) {
        //  Uninstall Zipli Retweet
        case __('UNINSTALL Zip.li Retweet', 'zipli-retweet') :
        if(trim($_POST['uninstall_zipli_yes']) == 'yes') :
        echo '<h3>'.__( 'Options', 'zipli-retweet').'</h3>';
        echo '<ol>';
        foreach($zipli_settings as $setting) :
            $delete_setting = delete_option($setting);
            if($delete_setting) {
            printf(__('<li>Option \'%s\' has been deleted.</li>', 'zipli-retweet'), "<strong><em>{$setting}</em></strong>");
            }
            else {
                printf(__('<li>Error deleting Option \'%s\'.</li>', 'zipli-retweet'), "<strong><em>{$setting}</em></strong>");
                }
        endforeach;
        echo '</ol>';
        echo '<br/>';
        $mode = 'end-UNINSTALL';
        endif;
        break;
    }
endif;
    switch($mode) {
    //  Deactivating Uninstall Zipli-Retweet
    case 'end-UNINSTALL':
        $deactivate_url = 'plugins.php?action=deactivate&amp;plugin=zipli-retweet/zipli-retweet.php';
        if(function_exists('wp_nonce_url')) {
            $deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_zipli-retweet/zipli-retweet.php');
        }
    echo sprintf(__('<a href="%s" class="button-primary">Deactivate Zipli Retweet</a> Disable that plugin to conclude the uninstalling.', 'zipli-retweet'), $deactivate_url);
    echo '</div>';
    break;
    default:
    ?>
    <!-- Uninstall Zipli Retweet -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>">
        <div class="wrap">
            <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'zipli-retweet/assets/images/zipli-retweet-32.png' )?>" alt="zipli-ico"/><?php _e('Uninstall Zip.li Retweet', 'zipli-retweet'); ?></h2>
            <p><?php _e('Uninstaling this plugin the options used by Zip.li Retweet will be removed.', 'zipli-retweet'); ?></p>
            <table>
                <tr>
                    <td>
                    <?php _e('The following WordPress Options will be deleted:', 'zipli-retweet'); ?>
                    </td>
                </tr>
            </table>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('WordPress Options', 'zipli-retweet'); ?></th>
                    </tr>
                </thead>
                <tr>
                    <td valign="top">
                        <ol>
                        <?php
                        foreach($zipli_settings as $settings)
                            printf( "<li>%s</li>\n", $settings );
                        ?>
                        </ol>
                    </td>
                </tr>
            </table>
            <p>
                <input type="checkbox" name="uninstall_zipli_yes" id="uninstall_zipli_yes" value="yes" />
                <label for="uninstall_zipli_yes"><?php _e('Yes. Uninstall Zip.li Retweet now', 'zipli-retweet'); ?></label>
            </p>
            <p>
                <input type="submit" name="do" value="<?php _e('UNINSTALL Zip.li Retweet', 'zipli-retweet'); ?>" class="button-primary" />
            </p>
        </div>
    </form>
<?php
}
?>