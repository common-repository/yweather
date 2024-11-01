<?php

/**
 * This class gets Weather RSS from Yahoo!Weather and parses
 * it into a weather object with usable attributes.
 *
 * @author Kevin Etienne <etienne.kevin@gmail.com>
 * @version 0.1
 * @package YWeather
 */

/**
 * Class to retrieve and parse the weather
 */
class YWeather {
  
  /**
   * Store the location's code of the city
   */
  protected $_locationCode;
  /**
   * Store the url of the API
   */
  protected $_urlComplete;
  /**
   * Store the unit's preference of the user and force him to retrieve the unit
   * throught the API (if the unit is not valid)
   */
  protected $_unit;

  /**
   * create an instance of simpleXML
   * 
   * @param string $location code belong to the location (ex: UKXX0106)
   * @param char $unit unit for temperature (c or f) 
   */  
  public function __construct($location, $unit = 'c')
  {
    $this->_locationCode = $location;
    $this->_unit = $unit;

    $this->_urlComplete = $this->_buildURI($this->_locationCode, $this->_unit);
    $this->weather = simplexml_load_file($this->_urlComplete);
  }

  /**
   * call a protected method
   *
   * @param string $methodName name of the method
   * @param string $args N/A
   * @return an object for the Yahoo!Weather's namespace
   */
  public function __call($methodName, $args)
  {
    $method = "_get" . ucfirst( strtolower($methodName) );
    if (method_exists($this, $method))
    {
      return $this->$method();
    }
  }

  /**
   * return the complete URL to retrieve the Yahoo API
   * 
   * @param string $location code belongs to the location
   * @param char $unit unit in (c)elcius or (f)ahraneit
   * @return url composed with $location and $unit
   */
  protected function _buildURI($location, $unit = 'c')
  {
    $urlComplete = 'http://weather.yahooapis.com/forecastrss?';
    $urlComplete .= 'p=' . $location;
    $urlComplete .= $unit ? '&u=' . $unit : '';
    return $urlComplete;
  }

  /**
   * Largely inspired by the snippet originately written by
   * {@link http://pkarl.com Pete Karl} (2009).
   *
   * return an object with the location, units, wind, atmosphere and
   * astronomy parameters
   * 
   * @return object from the API namespace
   */
  protected function _getChannel()
  {
    $yWeather = $this->weather->channel->children("http://xml.weather.yahoo.com/ns/rss/1.0");
    foreach ($yWeather as $x => $channel_item)
      foreach ($channel_item->attributes() as $k => $attr)
        $yw_channel->$x->$k = $attr;
    return $yw_channel;
  }

  /**
   * Largely inspired by the snippet originately written by
   * {@link http://pkarl.com Pete Karl} (2009).
   * 
   * return an object with the geolocalisation, condition and forecast
   * 
   * return object from the API namespace
   */
  protected function _getItem()
  {
    $yWeather = $this->weather->channel->item->children("http://xml.weather.yahoo.com/ns/rss/1.0");
    foreach ($yWeather as $x => $yw_item)
    {
      foreach ($yw_item->attributes() as $k => $attr)
      {
        if($k == 'day') $day = $attr;
        if($x == 'forecast')
        {
          $yw_forecast->$x->$day->$k = $attr;
        }
        else $yw_forecast->$x->$k = $attr;
      }
    }
    return $yw_forecast;
  }

  /**
   * return the URL
   */
  public function __toString() {
    return $this->_urlComplete;
  }

}
