<?php
/*
Plugin Name: ooVoo Video Chat
Plugin URI: http://www.oovoo.com
Description: ooVoo Web Room to video chat
Version: 1.0.0
Author: ooVoo
Author URI: http://www.oovoo.com/
*/

/* Define global variable to keep web room settings */
$room_settings = room_initialize_and_get_settings();

/* Define action handlers */
add_action('admin_menu', 'settings_web_room');
add_action('the_content', 'gen_oovoo_button');

/* Initialize web room settings with defaults */
function room_initialize_and_get_settings()
{
	$defaults = array(
		'id' => '',
		'width' => '850',
		'height' => '720',
		'caption' => 'My ooVoo web room',
		'image_type' => 'small_yellow',
    'automaticly' => true,
	);

	add_option('room_settings', $defaults, 'Options for ooVoo Video Chat Room');
	return get_option('room_settings');
}

function oovoo_button()
{
  global $post;

  if( !get_option('oovoo_uniq_id') )
    add_option('oovoo_uniq_id',uniqid());

  $arr = room_initialize_and_get_settings();
  $instance = $post->ID . '_' . get_option('oovoo_uniq_id');

  $onClick = "oovooOpenRoom('" . $instance . "');";

  $id = (empty( $arr['id'] )? 'wordpresswidget': $arr['id']);
  $height = (empty( $arr['height'] )? '720': $arr['height']);
  $width = (empty( $arr['width'] )? '850': $arr['width']);

  $output =
    "<script type=\"text/javascript\">".
      "function oovooOpenRoom(instance) {".
        "window.open('http://www2.oovoo.com/samples/webapi/default.aspx?oovooid=" . $id . "&instance=' + instance + '&caption=" . $arr['caption'] . "', '_blank', 'menubar=0, toolbar=0, width=" .$width . ", height=" . $height . "');".
      "}".
    "</script>";

  if($arr['image_type'] == 'link_ico')
  {
    $output .=
      '<table style="margin-top: 10px;">'.
        '<tr>'.
          '<td style="width: 40px">'.
            '<img alt="Discuss on ooVoo" src="http://www2.oovoo.com/samples/plugins/link_ico.png" />' .
          '</td>'.
          '<td>'.
            '<span onclick="' . $onClick . '" style="text-decoration: underline; color: blue; cursor: pointer;">Discuss on ooVoo</span>'.
          '</td>'.
        '</tr>'.
      '</table>';
  }
  else
    $output .=
      '<img style="cursor: pointer; margin-top: 10px;" onclick="' . $onClick . '" alt="Discuss on ooVoo" src="http://www2.oovoo.com/samples/plugins/' . $arr['image_type'] . '.png" />';

  return $output;
}

function gen_oovoo_button( $content )
{
  $sets = room_initialize_and_get_settings();

  if( $sets['automaticly'] )
    echo oovoo_button();

  $content = str_replace ('[oovoo-video-chat]', oovoo_button(), $content);

  return $content;
}

/* Generate plugin view at the page depend on settings and
   generate javascript to open unique web room (AddRoomJavascript) */
function add_oovoo_button()
{
  echo oovoo_button();
}

/* Create oovoo section in Setting menu to customize options */
function settings_web_room()
{
	if (function_exists('add_options_page'))
	{
		add_options_page('ooVoo Video Chat Room Settings', 'ooVoo', 8, basename(__FILE__), 'web_room_options_subpanel');
	}
}

/* Generate oovoo settings page and Save action */
function web_room_options_subpanel()
{
	global $room_settings;

        /* Retrieve entered values and save them into global variable */
	if (isset($_POST['room_save_settings']))
	{
		check_admin_referer('room_update_options');

		$room_settings['id'] = stripslashes($_POST['oovoo_id']);
    $room_settings['width'] = stripslashes($_POST['room_width']);
		$room_settings['height'] = stripslashes($_POST['room_height']);
		$room_settings['caption'] = stripslashes($_POST['room_caption']);
    $room_settings['image_type'] = stripslashes($_POST['room_image_type']);
    $room_settings['automaticly'] = stripslashes($_POST['room_automaticly']);

		update_option('room_settings', $room_settings);
	}
        /* HTML of the settings page */
	?>

          <script type="text/javascript">
var Handled;

function OnKeyPress(ev)
{
    if (Handled) return false;
}

function OnKeyDown(ev)
{
    Handled = false;

    if ((ev.keyCode >= 65 && ev.keyCode <= 90) ||
        (ev.keyCode >= 48 && ev.keyCode <= 57 && !ev.shiftKey) ||
        ev.keyCode == 8 || ev.keyCode == 32 || ev.keyCode == 37 ||
        ev.keyCode == 39 || ev.keyCode == 46 || ev.keyCode == 189 ||
        ev.keyCode == 109 || (ev.keyCode == 188 && !ev.shiftKey) ||
        (ev.keyCode == 190 && !ev.shiftKey) )
    {
        return true;
    }
    else if (ev.shiftKey && (ev.keyCode == 59 || ev.keyCode == 191 || ev.keyCode == 49 || ev.keyCode == 186))
    {
        return true;
    }
    else
    {
        Handled = true;
        ev.returnValue = false;
        if (ev.stopPropagation)
           ev.stopPropagation();
        if (ev.preventDefault)
           ev.preventDefault();
        return false;
     }
}
          </script>

	<div class="wrap">
		<div id="icon-options-general" class="icon32">
      <br/>
    </div>

		<h2>ooVoo Plugin Settings</h2>

		<form action="" method="post">
			<input type="hidden" name="room_save_settings" value="true" />

      <h3>ooVoo Video Chat Room Settings:</h3>
      <table>
        <tr>
          <td>ooVoo Id</td>
          <td style="padding-bottom: 5px;">
            <input type="text" class="regular-text code" name="oovoo_id" value="<?php echo attribute_escape($room_settings['id']); ?>" onkeypress="OnKeyPress(event);" onkeydown="OnKeyDown(event);" />
          </td>
          <td>
            <p class='description'>If you leave this field blank generic ooVoo Id will be used.</p>
          </td>
        </tr>
        <tr>
          <td>Room width</td>
          <td style="padding-bottom: 5px;"><input type="text" class="regular-text code" name="room_width" value="<?php echo attribute_escape($room_settings['width']); ?>" onkeypress="OnKeyPress(event);" onkeydown="OnKeyDown(event);" /></td>
        </tr>
        <tr>
          <td>Room height</td>
          <td style="padding-bottom: 5px;"><input type="text" class="regular-text code" name="room_height" value="<?php echo attribute_escape($room_settings['height']); ?>" onkeypress="OnKeyPress(event);" onkeydown="OnKeyDown(event);" /></td>
        </tr>
        <tr>
          <td>Room caption</td>
          <td style="padding-bottom: 5px;"><input type="text" class="regular-text code" name="room_caption" value="<?php echo attribute_escape($room_settings['caption']); ?>" onkeypress="OnKeyPress(event);" onkeydown="OnKeyDown(event);" /></td>
        </tr>
        </tr>
      </table>

      <h3>Select video chat button view:</h3>
      <table cellpadding="6" cellspacing="6">
        <tr>
          <td>
            <input type="radio" id="big_gray" name="room_image_type" value="big_gray" <?php if ($room_settings['image_type'] == 'big_gray') echo 'checked="checked"'; ?> />
          </td>
          <td>
            <img onclick="document.getElementById('big_gray').checked=true;" style="vertical-align: middle;" alt="Big gray button" src="http://www2.oovoo.com/samples/plugins/big_gray.png" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" id="big_yellow" name="room_image_type" value="big_yellow" <?php if ($room_settings['image_type'] == 'big_yellow') echo 'checked="checked"'; ?> />
          </td>
          <td>
            <img onclick="document.getElementById('big_yellow').checked=true;" style="vertical-align: middle;" alt="Big yellow button" src="http://www2.oovoo.com/samples/plugins/big_yellow.png" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" id="small_gray" name="room_image_type" value="small_gray" <?php if ($room_settings['image_type'] == 'small_gray') echo 'checked="checked"'; ?> />
          </td>
          <td>
            <img onclick="document.getElementById('small_gray').checked=true;" style="vertical-align: middle;" alt="Small gray button" src="http://www2.oovoo.com/samples/plugins/small_gray.png" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" id="small_yellow" name="room_image_type" value="small_yellow" <?php if ($room_settings['image_type'] == 'small_yellow') echo 'checked="checked"'; ?> />

          </td>
          <td>
            <img onclick="document.getElementById('small_yellow').checked=true;" style="vertical-align: middle;" alt="Small yellow button" src="http://www2.oovoo.com/samples/plugins/small_yellow.png" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="radio" id="link_ico" name="room_image_type" value="link_ico" <?php if ($room_settings['image_type'] == 'link_ico') echo 'checked="checked"'; ?> />
          </td>
          <td>
            <img onclick="document.getElementById('link_ico').checked=true;" style="vertical-align: middle;" alt="Link icon" src="http://www2.oovoo.com/samples/plugins/link_ico.png" /><span onclick="document.getElementById('link_ico').checked=true;" style="text-decoration: underline; color: blue; cursor: pointer;">Discuss in ooVoo</span>
          </td>
        </tr>
      </table>


      <h3>Additional Settings:</h3>

      <input type="hidden" name="room_automaticly" value="0" />
      <input id="room-automaticly" type="checkbox" name="room_automaticly" value="1" <?php if ($room_settings['automaticly']) echo 'checked="checked"'; ?> />
      <label for="room-automaticly">Add ooVoo video chat button automatically.</label>
      <p class='description'>
        Button will be added for each post and page.
      </p>


      <h3>Note:</h3>
      <p>
        You can also add ooVoo video chat button manually wherever you want. <br />Just simply add <code>&lt;?php add_oovoo_button();?&gt;</code> code into your Wordpress theme in a place where you want to display ooVoo video chat button.

      </p>


			<p><input type="submit" class="button-primary" name="submit" value="Save Changes" /></p>
			<?php
			if (function_exists('wp_nonce_field'))
				wp_nonce_field('room_update_options');
			?>
		</form>
	</div>
	<?php
}
?>