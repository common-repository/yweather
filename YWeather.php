<?php
/*
Plugin Name: Y!Weather
Plugin URI: http://www.pasunclou.com/
Description: YWeather gives you the weather in English of in French
Version: 0.1
Author: Kevin Etienne
Author URI: http://www.ethnoinfo.com
*/

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
  define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
  define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
  define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
  define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


require_once 'php/class.weather.php';

class WidgetYWeather
{
  public function control()
  {
    $data = get_option('WidgetYWeather');
    if (!$data) {
      add_option('WidgetYWeather', array( 'location' => 'code',
        'unit (c or f)' => 'unit'));
    }
    $data = get_option('WidgetYWeather');
    
?>
    <p><label>Location<input name="location" type="text" value="<?php echo $data['location']; ?>" /> </label></p>
    <p><label>Unit<input name="unit" type="text" value="<?php echo $data['unit']; ?>" /> </label></p>
<?php
    if (isset($_POST['location'])){
      $data['location'] = attribute_escape($_POST['location']);
      $data['unit'] = attribute_escape($_POST['unit']);
      update_option('WidgetYWeather', $data);
    }
  }

  public function widget($args)
  {
    $data = get_option('WidgetYWeather');
    
    echo $args['before_widget'];

    $weather = new YWeather($data['location'], $data['unit']);
    $item = $weather->Item();
    $channel = $weather->Channel();

    $code = $item->condition->code;
    $temp = $item->condition->temp;
    $unit = $channel->units->temperature;
    $sunrise = $channel->astronomy->sunrise;
    $sunset = $channel->astronomy->sunset;
    $city = $channel->location->city;
    $image = "http://l.yimg.com/a/i/us/we/52/" . $code . ".gif";

    echo "<div class='YWeather'>";
    echo "<h3 class='YWeather_title'>{$city}<span> ({$sunrise} - {$sunset})</span></h3>" . "\n";
    echo "<img src='{$image}' alt='' class='YWeather_image' />" . "\n";
    echo "<p class='YWeather_temp'>{$temp}°{$unit}</p>\n";
    echo "</div>\n";

    echo $args['after_widget'];
  }

  function add_my_stylesheet() {
    $myStyleFile = WP_PLUGIN_URL . '/YWeather/css/YWeather.css';
    wp_enqueue_style('YWeather', $myStyleFile);
  }

  function register()
  {
    register_sidebar_widget('Y!Weather', array('WidgetYWeather', 'widget'));
    register_widget_control('Y!Weather', array('WidgetYWeather', 'control'));
  }
}


add_action("wp_print_styles", array('WidgetYWeather', 'add_my_stylesheet'));
add_action("widgets_init", array('WidgetYWeather', 'register'));
