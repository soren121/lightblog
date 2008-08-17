<?php
/**
 * License
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **/

/**
 * Class for BBCode translation 
 * (comments and errors translated from Polish by soren121)
 * @author Mateusz Charytoniuk (Louner)
 * @copyright 2008 Mateusz Charytoniuk
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 **/
class parser
{
	private $config = array
		(
			'open_string' => array('[',']'),
			'close_string' => array('[/',']')
		);
	
	private $tags = array
		(
			'b' => '<strong>{value}</strong>',
			'i' => '<em>{value}</em>',
			'u' => '<del>{value}</del>',
			'img' => '<img src="{value}" alt="Picture" title="{parameter}" />',
			'url' => '<a href="{value}">{parameter}</a>',
			'size' => '<span style="font-size:{parameter}pt;>{value}</span>',
			'ul' => '<ul>{value}</ul>',
			'ol' => '<ol>{value}</ol>',
			'li' => '<li>{value}</li>'
		);
		
	private $tags_list = array();
	
	function parser()
	{
		$this -> config['open_string_preg'][0] = $this -> escape($this -> config['open_string'][0]);
		$this -> config['open_string_preg'][1] = $this -> escape($this -> config['open_string'][1]);
		
		$this -> config['close_string_preg'][0] = $this -> escape($this -> config['close_string'][0]);
		$this -> config['close_string_preg'][1] = $this -> escape($this -> config['close_string'][1]);
	}
	
	function parse( $text )
	{
		$this -> tags_list = array();
	
		if( !empty($text) && $text != '' )
		{
			if( !is_array($text) )
			{
				$text = $this -> special_chars( $text );
				$text = $this -> bbcode( $text );
			} else
			{
				// Rercurrent table
				foreach( $text as $key => &$value )
				{
					$value = $this -> parse($value);
				}
			}
		}

		return $text;
	}
		private function bbcode( $text )
		{
			$text_tmp = $text;
			$counter = 0;
				
			$regex = '#(('.$this -> config['open_string_preg'][0].')([a-zA-Z]+)(="([^"]+)")?('.$this -> config['open_string_preg'][1].')|('.$this -> config['close_string_preg'][0].')([a-zA-Z]+)('.$this -> config['close_string_preg'][1].'))#s';
			$stack = array();

			if( preg_match_all($regex,$text,$matches,PREG_SET_ORDER) )
			{
				foreach( $matches as $key => $value )
				{
					
					if( $value[2] == $this -> config['open_string'][0] && $value[6] == $this -> config['open_string'][1] )
					{
						if( array_key_exists($value[3],$this -> tags) )
						{
							$text = preg_replace('#'.$this -> config['open_string_preg'][0].$value[3].$this -> escape($value[4]).$this -> config['open_string_preg'][1].'#s',$this -> config['open_string'][0].(++$counter).':'.$value[3].$value[4].$this -> config['open_string'][1],$text,1);
							array_push($stack,array($value[3],$counter));
							array_push($this -> tags_list,array($value[3],true,$counter));
						}
					} else
					
					{
						if( $value[7] == $this -> config['close_string'][0] && $value[9] == $this -> config['close_string'][1] )
						{
							if( array_key_exists($value[8],$this -> tags) )
							{
								$last = end($stack);
															
								if( $last[0] == $value[8] && $last[1] == true )
								{
									$counter_change = end($stack);
									$counter_change = $counter_change[1];
									$text = preg_replace('#'.$this -> config['close_string_preg'][0].$value[8].$this -> config['close_string_preg'][1].'#s',$this -> config['close_string'][0].$counter_change.':'.$value[8].$this -> config['close_string'][1],$text,1);
									array_pop($stack);
									array_push($this -> tags_list,array($value[8],false,$counter_change));
									continue;
								} else
								{
									// into something
									return false;
								}
							}
						} else
						{
							$this -> error('The BBCode you have used was not recognized. Please try again.',__LINE__);
						}
					}
				}
			}
			
			if( !count($stack) && count($this -> tags_list) )
			{
				foreach( $this -> tags_list as $key => $value )
				{
					if( $value[1] == true )
					{
						$number = $value[2];
						$value = $value[0];
						
						if(preg_match('#'.$this -> config['open_string_preg'][0].$number.':'.$value.'(="([^"]+)")?'.$this -> config['open_string_preg'][1].'(.*?)'.$this -> config['close_string_preg'][0].$number.':'.$value.$this -> config['close_string_preg'][1].'#s',$text,$match) )
						{
							if( $tag = $this -> check_tag($match[3],$value,$number,$match[2]) )
							{
								$text = preg_replace('#'.$this -> config['open_string_preg'][0].$number.':'.$value.$match[1].$this -> config['open_string_preg'][1].'(.*?)'.$this -> config['close_string_preg'][0].$number.':'.$value.$this -> config['close_string_preg'][1].'#s',$tag,$text,1);
							} else
							{
								$text = preg_replace('#'.$this -> config['open_string_preg'][0].$number.':'.$value.$match[1].$this -> config['open_string_preg'][1].'(.*?)'.$this -> config['close_string_preg'][0].$number.':'.$value.$this -> config['close_string_preg'][1].'#s',$this -> config['open_string'][0].$value.htmlspecialchars($match[1],ENT_QUOTES).$this -> config['open_string'][1].$match[3].$this -> config['close_string'][0].$value.$this -> config['close_string'][1],$text,1);
							}
						}
					} else
					{
						continue;
					}
				}
			}
			
			if( count($stack) && count($this -> tags_list) )
			{
				return false;
			}

			return $text;
		}
		
			private function check_tag( $value, $tag, $number, $parameter )
			{
				$parameter = htmlspecialchars($parameter,ENT_QUOTES);

				if( empty($parameter) )
				{
					$parameter = $value;
				}

				$new_value = str_replace('{value}',$value,$this -> tags[$tag]);
				$new_value = str_replace('{parameter}',$parameter,$new_value);

				switch( $tag )
				{
					case 'img':
						// http://regexlib.com/REDetails.aspx?regexp_id=1856
						if( preg_match('#((http|https)\:\/\/([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}|localhost)(?:\/\S*)?(?:[a-zA-Z0-9_])+\.(?:jpg|jpeg|gif|png|JPG|JPEG|GIF|PNG))#s',$value) )
						{
							return $new_value;
						}
						
						return false;
					break;
					case 'url':
						if( preg_match('#(http|ftp|https):\/\/([\w\-_]+)(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+\#]*[\w\-\@?^=%&amp;/~\+\#])?#s',$value) )
						{
							return $new_value;
						}
						
						return false;
					break;
					case 'size':
						$parameter = intval($parameter);
						if( $parameter > 0 )
						{
							return $new_value;
						}
					break;
					case 'ol':
					case 'ul':
						$tags_between = array();
						$error = false;
					
						foreach($this -> tags_list as $key => $subvalue)
						{
							if( $subvalue[2] >= $number )
							{
								if( $subvalue[0] == 'li' )
								{
									
									if( $subvalue[1] == true )
									{									
										if( !( ( $this -> tags_list[$key-1][0] == 'ul' || $this -> tags_list[$key-1][0] == 'ol' ) && $this -> tags_list[$key-1][1] == true ) )
										{
											if( !( $this -> tags_list[$key-1][0] == 'li' && $this -> tags_list[$key-1][1] == false ) )
											{
												$error = true;
											}
										}
									} else
									
									{
										if( !( ( $this -> tags_list[$key+1][0] == 'ul' || $this -> tags_list[$key+1][0] == 'ol' ) && $this -> tags_list[$key+1][1] == false ) )
										{
											if( !( $this -> tags_list[$key+1][0] == 'li' && $this -> tags_list[$key+1][1] == true ) )
											{
												$error = true;
											}
										}
									}
								}
							
								if( $subvalue[0] == $tag && $subvalue[1] == false )
								{
									if( !( $this -> tags_list[$key-1][0] == 'li' && $this -> tags_list[$key-1][1] == false ) )
									{
										$error = true;
									}
								}
							
								if( $error == true )
								{
									$new_value  = $this -> config['open_string'][0].$tag.$this -> config['open_string'][1];
									$new_value .= preg_replace('#('.$this -> config['open_string_preg'][0].')([0-9]+):([a-zA-Z]+)('.$this -> config['open_string_preg'][1].')#s','$1$3$4',$value);
									$new_value  = preg_replace('#('.$this -> config['close_string_preg'][0].')([0-9]+):([a-zA-Z]+)('.$this -> config['close_string_preg'][1].')#s','$1$3$4',$new_value);
									$new_value .= $this -> config['close_string'][0].$tag.$this -> config['close_string'][1];

									return $new_value;
								}
							}
							if( $subvalue[0] == $tag && $subvalue[1] == false && $subvalue[2] == $number )
							{
								break;
							}
							
							$tags_between[$key] = $subvalue;
						}

						if( preg_match('#<'.$tag.'([^>]*)>(.*?)'.$this -> config['open_string_preg'][0].'([0-9]+):li'.$this -> config['open_string_preg'][1].'#s',$new_value,$match) )
						{
							$new_value = str_replace($match[2],preg_replace('#<br([^>]*)>#s','',$match[2]),$new_value);
						}
						
						foreach( $tags_between as $key => $subvalue )
						{
							if( $subvalue[0] == 'li' && $subvalue[1] == false && @$tags_between[$key+1][0] == 'li' )
							{
								if( preg_match('#'.$this -> config['close_string_preg'][0].$subvalue[2].':li'.$this -> config['close_string_preg'][1].'(.*?)'.$this -> config['open_string_preg'][0].($subvalue[2]+1).':li'.$this -> config['open_string_preg'][1].'#s',$new_value,$matches) )
								{
									$new_value = str_replace($matches[1],preg_replace('#<br([^>]*)>#s','',$matches[1]),$new_value);
								}
							}
						}
												
						$last = end($tags_between);
						if( preg_match('#'.$this -> config['close_string_preg'][0].$last[2].':li'.$this -> config['close_string_preg'][1].'(.*?)</'.$tag.'>#s',$new_value,$match) )
						{
							$new_value = str_replace($match[1],preg_replace('#<br([^>]*)>#s','',$match[1]),$new_value);
						}
						
						return $new_value;
					break;
					case 'li':
						foreach( $this -> tags_list as $key => $subvalue )
						{
							if( $subvalue[0] == $tag && $subvalue[2] == $number )
							{
								
								if( $subvalue[1] == true )
								{
									if( isset($this -> tags_list[$key-1]) )
									{						
										if( !( ( $this -> tags_list[$key-1][0] == 'ul' || $this -> tags_list[$key-1][0] == 'ol' ) && $this -> tags_list[$key-1][1] == true ) )
										{
											if( !( $this -> tags_list[$key-1][0] == 'li' && $this -> tags_list[$key-1][1] == false ) )
											{
												return false;
											}
										}
									} else
									{
										return false;
									}
								} else
								
								{
									if( isset($this -> tags_list[$key+1]) )
									{
										if( !( ( $this -> tags_list[$key+1][0] == 'ul' || $this -> tags_list[$key+1][0] == 'ol' )  && $this -> tags_list[$key+1][1] == false ) )
										{
											if( !( $this -> tags_list[$key+1][0] == 'li' && $this -> tags_list[$key+1][1] == true ) )
											{
												return false;
											}
										}
									} else
									{
										return false;
									}
								}
							}
						}
						
						return $new_value;
					break;
					default: return $new_value; break;
				}
				
				return false;
			}

		private function special_chars( $text )
		{
			$text = htmlspecialchars($text,ENT_NOQUOTES);
			$text = nl2br($text);
			// http://bbcode.strefaphp.net/ 
			// convert signs
			$text = str_replace('&amp;plusmn;', '&plusmn;', $text);
			$text = str_replace('&amp;trade;', '&trade;', $text);
			$text = str_replace('&amp;bull;', '&bull;', $text);
			$text = str_replace('&amp;deg;', '&deg;', $text);
			$text = str_replace('&amp;copy;', '&copy;', $text);
			$text = str_replace('&amp;reg;', '&reg;', $text);
			$text = str_replace('&amp;hellip;', '&hellip;', $text);

			// erroneous encoding from PHPMyAdmin
			$text = str_replace('&amp;#261;', 'ą', $text);
			$text = str_replace('&amp;#263;', 'ć', $text);
			$text = str_replace('&amp;#281;', 'ę', $text);
			$text = str_replace('&amp;#322;', 'ł', $text);
			$text = str_replace('&amp;#347;', 'ś', $text);
			$text = str_replace('&amp;#378;', 'ź', $text);
			$text = str_replace('&amp;#380;', 'ż', $text);

			// special signs from m$ word
			$text = str_replace('&amp;#177;', 'ą', $text);
			$text = str_replace('&amp;#8217;', '\'', $text);
			$text = str_replace('&amp;#8222;', '"', $text);
			$text = str_replace('&amp;#8221;', '"', $text);
			$text = str_replace('&amp;#8220;', '"', $text);
			$text = str_replace('&amp;#8211;', '-', $text);
			$text = str_replace('&amp;#8230;', '&hellip;', $text);
		
			return $text;
		}
		
	private function escape( $text )
	{
		$what = array('^',']','[','.','$','{','}','*','(',')','/','+','|','?','<','>');
		$what_for = array();
		
		foreach( $what as $key => &$value )
		{
			$what_for[$key] = '\\'.$value;
		}
		
		return str_replace($what,$what_for,$text);
	}
		
	private function error( $value, $line )
	{
		exit ('<pre><strong>error, line:'.$line.', file: '.basename(__FILE__).'</strong>'."\n".$value.'</pre>');
	}
}
?>
