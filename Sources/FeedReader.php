<?php
#
#  Feed Reader Class
#  
#  This script is made by aldo of http://mschat.net/ and is free for you to use
#  in anyway shape or form, however if you want to redistribute it I ask that you
#  please give me credit where credit is due. Thanks!
#  
#  This script is provided "AS IS" with no warranty whatso ever, I (aldo or mschat.net)
#  and or everyone else is not responsible for anything that may occur while you use
#  this script.
#

class Reader
{
  private $feed_url = null;
  private $is_read = false;
  private $cache = null;
  private $cache_dir = null;
  private $cache_length = 3600;
  private $is_cached = false;

  public function Reader($feed_url = null, $cache_dir = null, $cache_length = 3600)
  {
    if(!empty($cache_dir))
      $this->set_cache($cache_dir);

    if(!empty($cache_length))
      $this->set_cache_length($cache_length);

    if(!empty($feed_url))
    {
      $this->set_feed($feed_url);
      $this->read_feed(true);
    }

  }

  public function set_feed($feed_url)
  {
    # The feed hasn't been read yet.
    # Maybe...
    if($this->feed_url != $feed_url)
      $this->is_read = false;

    # Is it cached..?
    if($this->cache_exists())
    {
      if(file_exists($this->cache_dir. '/'. sha1($feed_url). '.cache'))
      {
        # Get the feed out :)
        $cache_time = (int)substr(file_get_contents($this->cache_dir. '/'. sha1($feed_url). '.cache'), 0, 10);

        if(!empty($cache_time))
        {
          # Make sure it isn't too old.
          if(($cache_time + $this->cache_length) > time())
            $this->is_cached = true;
          else
            $this->is_cached = false;
        }
        else
          $this->is_cached = false;
      }
      else
        $this->is_cached = false;

      if($this->is_cached)
        $this->cache = substr(file_get_contents($this->cache_dir. '/'. sha1($feed_url). '.cache'), 10, strlen(file_get_contents($this->cache_dir. '/'. sha1($feed_url). '.cache')));
      else
        $this->cache = null;
    }

    # Finally set the new feed url.
    $this->feed_url = $feed_url;
  }

  public function set_cache($cache_dir)
  {
    if($this->cache_exists($cache_dir))
    {
      $this->cache_dir = $cache_dir;
      return true;
    }
    else
      return false;
  }

  private function cache_exists($cache_dir = null)
  {
    if(empty($cache_dir))
      $cache_dir = $this->cache_dir;

    # Still empty..?
    if(empty($cache_dir))
      return false;

    # Make sure it exists... :)
    if(is_dir($cache_dir))
      # It is ready to go!
      return true;
    elseif(is_file($cache_dir))
      # It's a file D:
      return false;
    else
      # Attempt to create it.
      return @mkdir($cache_dir);
  }

  public function set_cache_length($cache_length = 3600)
  {
    $this->cache_length = (int)$cache_length > 0 ? (int)$cache_length : $this->cache_length;

    return $this->cache_length;
  }

  public function read_feed()
  {
    if(!$this->is_read)
    {
      # We are getting a new result set :)
      $this->results = array();

      # Get our feed... If we can :D!
      $feed = $this->get_feed($this->feed_url);

      if(!empty($feed))
      {
        # Create our XML parser.
        $parser = xml_parser_create();

        # Now a couple options ;)
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

        # Let's give it a go!
        $parsed = xml_parse_into_struct($parser, $feed, $this->results);

        # Did it work..?
        if($parsed)
        {
          # Ya... Mark it as read.
          $this->is_read = true;

          # We may need to cache this :) And as long as this feed isn't cached! Lol...
          if($this->cache_exists() && empty($this->is_cached))
          {
            @file_put_contents($this->cache_dir. '/'. sha1($this->feed_url). '.cache', time(). $feed);
          }

          return true;
        }
        else
          # Oh noes! D:!
          return false;
      }
      else
        return false;
    }
    else
      return true;
  }

  private function get_feed($url, $num_redirects = 0)
  {
    # Cached..?
    if(file_exists($this->cache_dir. '/'. sha1($url). '.cache'))
    {
      $feed = @file_get_contents($this->cache_dir. '/'. sha1($url). '.cache');

      # Expired..?
      $cache_time = (int)substr($feed, 0, 10);

      if(($cache_time + $this->cache_length) > time())
      {
        $this->is_cached = true;
        return substr($feed, 10, strlen($feed));
      }
    }

    # Let's try to get it with cURL
    if(function_exists('curl_init'))
    {
      $ch = curl_init();
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'RSS Feed Reader by mschat.net');
      $feed = curl_exec($ch);
      curl_close($ch);

      return $feed;
    }
    else
    {
      # Parse the url.
      $parsed = parse_url($url);

      # Ugh, fsockopen ¬.¬ *Shakes fist*
      $fp = fsockopen(($parsed['scheme'] == 'https' ? 'ssl://' : ''). $parsed['host'], ($parsed['scheme'] == 'https' ? 443 : ($parsed['scheme'] == 'ftp' ? 21 : 80)), $errno, $errstr, 5);

      # The GET request
      fwrite($fp, 'GET '. (!empty($parsed['path']) ? $parsed['path'] : '/'). (!empty($parsed['query']) ? '?'. $parsed['query'] : ''). " HTTP/1.1 \r\n");
      fwrite($fp, 'Host: '. $parsed['host']. "\r\n");
      fwrite($fp, 'Connection: close'."\r\n");
      fwrite($fp, 'User-Agent: RSS Feed Reader by mschat.net'."\r\n\r\n");

      # How do you respond to my request, Mr. Server.
      $response = fgets($fp, 768);

      # So we need to check if we need to redirect >-> Only 5 max!
      if($num_redirects < 5 && (strpos($response, '301') !== false || strpos($response, '302') !== false || strpos($response, '307') !== false))
      {
        $data = '';
        $location = '';
	      while(!feof($fp) && trim($data = fgets($fp, 4096)) != '')
	        if(strpos($data, 'Location:') !== false)
		        $location = trim(substr($data, strpos($data, ':') + 1));

        # Any location at all? o.O
        if(empty($location))
          return false;

        # So redirect...
        return $this->get_feed($location, $num_redirects + 1);
      }
      # Not a success? :(
      elseif(substr_count($response, 'OK') == 0 || !substr_count($response, 'Created') == 0)
        return false;
      else
      {
        # Now lets get that data!
        $data = '';
        while(!feof($fp))
          $data .= fgets($fp, 4096);
        @fclose($fp);

        # Remove the headers...
        $data = @explode("\r\n\r\n", $data);
        if(isset($data[0]) && isset($data[1]))
          unset($data[0]);
        $data = implode("\r\n\r\n", $data);

        # Need to remove the dang weird things on the first line
        # and the weird 0 at the end?
        $data = trim($data);
        $data = explode("\n", $data);
        if($data[count($data) - 1] == 0)
          unset($data[count($data) - 1], $data[0]);
         $data = implode("\n", $data);

        # Return it, and yeah.
        return $data;
      }
    }
  }

  public function return_items($num_items = 0)
  {
    # Number of items 0? That means all! =D
    $items_loaded = 0;

    $items = array();
    $item = null;

    # Lets get those items loaded :)
    foreach($this->results as $result)
    {
      if($items_loaded > ($num_items - 1) && $num_items > 0)
        break;

      if(strtolower($result['tag']) == 'item' && $result['type'] == 'open')
        $item = array();
      elseif(is_array($item) && in_array(strtolower($result['tag']), array('title', 'link', 'description', 'author', 'category', 'comments', 'guid', 'pubdate')))
      {
        $item[strtolower($result['tag'])] = $result['value'];
      }
      elseif(strtolower($result['tag']) == 'item' && $result['type'] == 'close')
      {
        $items[] = $item;
        $items_loaded++;

        $item = null;
      }
    }

    return $items;
  }

  public function is_cached()
  {
    return $this->is_cached;
  }

  public function is_read()
  {
    return $this->is_read;
  }

  public function cache_length()
  {
    return $this->cache_length;
  }
}
?>