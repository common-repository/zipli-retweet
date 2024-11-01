<?php
    //Verify if current user can access the settings from plugin
    if(!current_user_can('manage_zipli')){
        die('denied access!');
    }

$show_btn = get_option('zipli_retweet_btnaut');

if ( isset( $_POST['submit'] ) ) :
    update_option( 'zipli_retweet_user', $_POST[ 'zipli_retweet_user' ] );
    update_option( 'zipli_retweet_password', str_rot13( $_POST[ 'zipli_retweet_password' ] ) );
    update_option( 'zipli_retweet_btnaut', $_POST[ 'zipli_retweet_btnaut' ] );
?>
<div class="updated"><p><strong><?php _e('Settings Updated', 'zipli-retweet' ); ?></strong></p></div>
<?php endif; ?>

<div class="wrap">
    <form name="zipli_twitter_form" method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
	<h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'zipli-retweet/assets/images/zipli-retweet-32.png' )?>" alt="zipli-ico"/><?php _e('Settings', 'zipli-retweet'); ?></h2>
	<table class="form-table">
            <tbody>
             <tr valign="top">
                <th scope="row">
                    <label for="zipli_retweet_btnaut"><?php _e('Retweet Button:', 'zipli-retweet'); ?></label>
                </th>
                <td>
                    <select name="zipli_retweet_btnaut" id="zipli_retweet_btnaut">
                        <option value="0"<?php selected('0', $show_btn); ?>><?php _e('No', 'zipli-retweet'); ?></option>
                        <option value="1"<?php selected('1', $show_btn); ?>><?php _e('Yes', 'zipli-retweet'); ?></option>
                    </select>
                    <span class="description"><?php _e('Retweet button under the post that sends short post url to twitter','zipli-retweet'); ?>.</span>
                </td>
             </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="zipli_retweet_user"><?php _e('Twitter Username','zipli-retweet'); ?> </label>
                    </th>
                    <td>
                        <input type="text" name="zipli_retweet_user" id="zipli_retweet_user" value="<?php echo ( get_option( 'zipli_retweet_user' ) ); ?>" />
                        <br/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="zipli_retweet_password"><?php _e('Twitter Password','zipli-retweet'); ?> </label>
                    </th>
                    <td>
                        <input id="zipli_retweet_password" type="password" name="zipli_retweet_password" value="<?php echo str_rot13( get_option( 'zipli_retweet_password' ) ); ?>" />
                        <span class="description"><?php _e('(For send twitter message when publish a new post)','zipli-retweet'); ?>.</span>
                        <br/>
                    </td>
                </tr>
            </tbody>
        </table>
	<p class="submit">
            <input class="button-primary" type="submit" name="submit" value="<?php _e( 'Save', 'zipli-retweet' ) ?>" />
	</p>
    </form>
</div>