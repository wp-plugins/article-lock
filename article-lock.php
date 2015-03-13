<?php

	/* 
		Plugin Name: Article Lock
		Plugin URI: http://bostjan.gets-it.net/plugins
		Description: A simple plugin that locks a public post, shows only its preview and unlocks the entire content on the desired date.
		Version: 1.1
		Author: Bostjan Cigan
		Author URI: http://bostjan.gets-it.net
		License: GPL v2
	*/ 


	register_activation_hook(__FILE__, 'article_lock_install');
	register_deactivation_hook(__FILE__, 'article_lock_uninstall');
	add_action('admin_menu', 'article_lock_admin_menu_create');
	add_action('save_post', 'article_lock_save');
	add_filter('the_content', 'article_lock_content');
	add_action('add_meta_boxes', 'article_lock_metabox_add');
	
	function article_lock_install() {
		$options = array(
			'preview_size' => 3000,
			'version' => '1.2',
			'position' => 'top',
			'enabled' => true,
			'show_code_count' => true,
			'show_image_count' => true,
			'show_word_count' => true,
			'show_powered_by' => false,
			'border_color' => '#CCC',
			'html_before' => '',
			'html_after' => ''
		);
		add_option('article_lock_settings', $options);
	}
	
	function article_lock_uninstall() {
		delete_option('article_lock_settings');
	}

	function article_lock_admin_menu_create() {
		add_options_page('Article Lock Settings', 'Article Lock', 'administrator', __FILE__, 'article_lock_settings');	
	}
	
	function delete_article_lock_data() {

		$args = array('numberposts' => -1);
		$posts = get_posts($args);
		foreach($posts as $post) {
			delete_post_meta($post->ID, 'article_lock_data');
		}
		
	}
	
	function article_lock_settings() {

		$message = "";
		
		if(isset($_POST['article_lock_delete']) && isset($_POST['Submit2'])) {
			delete_article_lock_data();
			$message .= "All Article Lock data was deleted.";
		}
		
		if(isset($_POST['Submit1'])) {
			$article_lock_settings = get_option('article_lock_settings', true);
			
			$activated = $_POST['article_lock_activated'];
			$length = (int) $_POST['article_lock_preview_length'];
			$position = $_POST['article_lock_position'];
			$word_count = $_POST['article_lock_show_word_count'];
			$image_count = $_POST['article_lock_show_image_count'];
			$code_count = $_POST['article_lock_show_code_count'];
			$powered_by = $_POST['article_lock_show_powered_by'];
			$color = $_POST['article_lock_border_color'];
			$html_before = $_POST['article_lock_html_before'];
			$html_after = $_POST['article_lock_html_after'];
			
			$article_lock_settings['preview_size'] = (isset($length)) ? $length : $article_lock_settings['preview_size'];
			$article_lock_settings['enabled'] = (isset($activated)) ? true : false;
			$article_lock_settings['show_code_count'] = (isset($code_count)) ? true : false;
			$article_lock_settings['show_image_count'] = (isset($image_count)) ? true : false;
			$article_lock_settings['show_word_count'] = (isset($word_count)) ? true : false;
			$article_lock_settings['show_powered_by'] = (isset($powered_by)) ? true : false;
			$article_lock_settings['position'] = (isset($position)) ? $position : $article_lock_settings['position'];
			$article_lock_settings['border_color'] = (isset($color)) ? stripslashes($color) : $article_lock_settings['border_color'];			
			$article_lock_settings['html_before'] = stripslashes($html_before);
			$article_lock_settings['html_after'] = stripslashes($html_after);			
			
			update_option('article_lock_settings', $article_lock_settings);
			
			$message .= "Settings updated.";
			
		}
		
		$article_lock_settings = get_option('article_lock_settings', true);
				
?>

		<div id="icon-options-general" class="icon32"></div><h2>Article Lock Settings</h2>
<?php

		if(strlen($message) > 0) {
		
?>

			<div id="message" class="updated">
				<p><strong><?php echo $message; ?></strong></p>
			</div>

<?php
			
		}

?>
        
                <form method="post" action="">
				<table class="form-table">
					<tr>
						<th scope="row"><img src="<?php echo plugin_dir_url(__FILE__).'lock.png'; ?>" height="96px" width="96px" /></th>
						<td>
							<p>Thank you for using this plugin. If you like the plugin, you can <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SKMW3BAC8KE52">buy me a cup of coffee</a> :)</p> 
							<p>Visit the official website @ <a href="http://bostjan.gets-it.net/plugins/article-lock">wpPlugz</a>.</p>
                        </td>
					</tr>		
					<tr>
						<th scope="row"><label for="article_lock_activated">Activated?</label></th>
						<td>
		    	            <input type="checkbox" name="article_lock_activated" id="article_lock_activated" value="on" <?php checked($article_lock_settings['enabled'], true); ?> />
							<br />
            				<span class="description">Check if you want the plugin to be activated. When disabled, all article locks will be disabled.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="article_lock_preview_length">Default preview length</label></th>
						<td>
							<input type="text" name="article_lock_preview_length" id="article_lock_preview_length" value="<?php echo esc_attr($article_lock_settings['preview_size']); ?>" />
							<br />
            				<span class="description">The default preview length (in number of characters).</span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="article_lock_position">Position of message</label></th>
						<td>
							<select id="article_lock_position" name="article_lock_position">
								<option value="top" <?php selected($article_lock_settings['position'], 'top'); ?>>Top (before content)</option>
								<option value="bottom" <?php selected($article_lock_settings['position'], 'bottom'); ?>>Bottom (after content)</option>
							</select>
							<br />
            				<span class="description">The position of the message that is outputted to the user.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="article_lock_show_word_count">Show word count</label></th>
						<td>
		    	            <input type="checkbox" name="article_lock_show_word_count" id="article_lock_show_word_count" value="on" <?php checked($article_lock_settings['show_word_count'], true); ?> />
							<br />
            				<span class="description">Check this if you want to output how many words there are in your full post.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="article_lock_show_code_count">Show code count</label></th>
						<td>
		    	            <input type="checkbox" name="article_lock_show_code_count" id="article_lock_show_code_count" value="on" <?php checked($article_lock_settings['show_code_count'], true); ?> />
							<br />
            				<span class="description">Check this if you want to output how many code snippets there are in your full post.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="article_lock_show_image_count">Show image count</label></th>
						<td>
		    	            <input type="checkbox" name="article_lock_show_image_count" id="article_lock_show_image_count" value="on" <?php checked($article_lock_settings['show_image_count'], true); ?> />
							<br />
            				<span class="description">Check this if you want to output how many images there are in your full post.</span>
						</td>
					</tr>		
					<tr>
						<th scope="row"><label for="article_lock_border_color">Color of box border</label></th>
						<td>
							<input type="text" name="article_lock_border_color" id="article_lock_border_color" value="<?php echo esc_attr($article_lock_settings['border_color']); ?>" />
							<br />
            				<span class="description">The color of the border around the textbox.</span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="article_lock_html_before">HTML outputted before box</label></th>
						<td>
							<input type="text" name="article_lock_html_before" id="article_lock_html_before" value="<?php echo esc_attr($article_lock_settings['html_before']); ?>" />
							<br />
            				<span class="description">HTML that is outputted before box.</span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="article_lock_html_after">HTML outputted after box</label></th>
						<td>
							<input type="text" name="article_lock_html_after" id="article_lock_html_after" value="<?php echo esc_attr($article_lock_settings['html_after']); ?>" />
							<br />
            				<span class="description">HTML that is outputted after box.</span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="article_lock_show_powered_by">Show powered by message</label></th>
						<td>
		    	            <input type="checkbox" name="article_lock_show_powered_by" id="article_lock_show_powered_by" value="on" <?php checked($article_lock_settings['show_powered_by'], true); ?> />
							<br />
            				<span class="description">Show powered by message, if you decide not to show it, please consider a <a href="http://gum.co/article-lock">donation</a><script type="text/javascript" src="https://gumroad.com/js/gumroad-button.js"></script><script type="text/javascript" src="https://gumroad.com/js/gumroad.js"></script>.</span>
						</td>
					</tr>		
				</table>					
				<p><input type="submit" name="Submit1" class="button-primary" value="<?php esc_attr_e('Update options') ?>" /></p>
				</form>

				<form method="post" action="">
				<table class="form-table">
					<tr>
						<th scope="row"><label for="article_lock_delete">Delete all plugin data?</label></th>
						<td>
		    	            <input type="checkbox" name="article_lock_delete" id="article_lock_delete" value="on" />
							<br />
            				<span class="description">Check this if you want to delete all the plugin data (all post locks and metadata). This action is <font color="red"><strong>UNDOABLE</strong></font>.</span>
						</td>
					</tr>		
				</table>					
				<p><input type="submit" name="Submit2" class="button-primary" value="<?php esc_attr_e('Delete plugin data') ?>" /></p>
				</form>


<?php

	}
	
	function article_lock_content($content) {
	
		$options = get_option('article_lock_settings');
		
		if(!$options['enabled']) {
			return $content;
		}

		global $post;

		$article_lock_data = get_post_meta($post->ID, 'article_lock_data', true);
		
		$all_set = true;
		foreach($article_lock_data as $key => $value) {
			if(!isset($value)) {
				$all_set = false;
			}
		}
		
		if($article_lock_data['on'] == "on" && $all_set && $options['enabled']) {

			$img_count = substr_count($content, '<img');
			$word_count = str_word_count($content, 0);
			$code_count = substr_count($content, '<pre');
			
			$day = $article_lock_data['day'];
			$month = $article_lock_data['month'];
			$year = $article_lock_data['year'];
			$hour = $article_lock_data['hour'];
			$minute = $article_lock_data['minute'];
			
			$article_lock_timestamp = strtotime(date("F d, Y g:i a", mktime($hour, $minute, 0, $month, $day, $year)));

			$time_diff = time() - $article_lock_timestamp;

			if($time_diff > 0) {
				return $content;
			}
			else {
				$added = 0;
				$url = plugin_dir_url(__FILE__).'images/lock.png';
				$article = $options['html_before'];
				$article .= '<br /><div id="article_lock" style="border-bottom: 1px solid '.$options['border_color'].'; border-top: 1px solid '.$options['border_color'].'; background-image: url(\''.$url.'\'); background-position: left center; background-repeat: no-repeat; padding-left: 64px; padding-top: 10px; padding-bottom: 10px;">';
				$article .= "This is a <strong>preview</strong>.";
				$full_article_contains = " The full article contains";
				if($options['show_word_count'] || $options['show_image_count'] || $options['show_code_count']) $article .= $full_article_contains;
				if($options['show_word_count']) {
					$article .= " <strong>{$word_count}</strong> words";
					$added = $added + 1;
				}
				if($img_count > 0 && $options['show_image_count']) {
					if($added > 0) $article .= ",";
					$article .= " <strong>{$img_count}</strong> image(s)";
					$added = $added + 1;
				}
				if($code_count > 0 && $options['show_code_count']) {
					if($added > 0) $article .= ",";
					$article .= " <strong>{$code_count}</strong> code snippet(s)";
					$added = $added + 1;
				}
				$article .= " and it will be completely unlocked on <strong>{$day}.{$month}.{$year} {$hour}:{$minute}</strong>.";

				if($options['show_powered_by']) {
					$article .= ' - Powered by <a href="http://wpplugz.is-leet.com">wpPlugz</a>.';
				}

				$article .= "</div>";

				$article .= $options['html_after'];
				
				$truncated_article = truncate($content, $article_lock_data['length'], "...", true, true);
				
				if($options['position'] == "top") {
					if(current_user_can('manage_options')) {
						return $article.$truncated_article."<div align=\"center\" style=\"border-bottom: 1px solid #000; border-top: 1px solid #000;\"><p><strong>WHOLE ARTICLE</strong></p></div>".$content;
					}
					else {
						return $article.$truncated_article;
					}
				}
				else {
					if(current_user_can('manage_options')) {
						return $truncated_article.$article."<div align=\"center\" style=\"border-bottom: 1px solid #000; border-top: 1px solid #000;\"><p><strong>WHOLE ARTICLE</strong></p></div>".$content;
					}
					else {
						return $truncated_article.$article;
					}
				}
				
			}			
			
		}
		
		return $content;
	
	}
	
	function article_lock_metabox_add() {
		add_meta_box('article-lock', 'Article Lock', 'article_lock_do', 'post', 'normal', 'high');
		add_meta_box('article-lock', 'Article Lock', 'article_lock_do', 'page', 'normal', 'high');
	}

	function article_lock_do($post) {

		$options = get_option('article_lock_settings');
		$article_lock_data = get_post_meta($post->ID, 'article_lock_data', true);
	
		$month = isset($article_lock_data['month']) ? esc_attr($article_lock_data['month']) : '';
		$day = isset($article_lock_data['day']) ? esc_attr($article_lock_data['day']) : '';
		$year = isset($article_lock_data['year']) ? esc_attr($article_lock_data['year']) : '';
		$hour = isset($article_lock_data['hour']) ? esc_attr($article_lock_data['hour']) : '';
		$minute = isset($article_lock_data['minute']) ? esc_attr($article_lock_data['minute']) : '';
		$length = isset($article_lock_data['length']) ? esc_attr($article_lock_data['length']) : $options['preview_size'];
		$on = isset($article_lock_data['on']) ? esc_attr($article_lock_data['on']) : 'off';
		wp_nonce_field('my_meta_box_article_lock', 'meta_box_article_lock');

		$url = plugin_dir_url(__FILE__).'images/date.png';
		$url2 = plugin_dir_url(__FILE__).'images/preview.png';
		$url3 = plugin_dir_url(__FILE__).'images/activate.png';
		$url4 = plugin_dir_url(__FILE__).'images/delete.png';
	
?>

		<div style="background-image: url('<?php echo $url; ?>'); background-repeat: no-repeat; text-indent: 20px;">Date to unlock <strong>whole article</strong></div>
		<p><select id="article_lock_mm_pdraft" name="article_lock_mm_pdraft">
			<option value="01" <?php selected($month, '01'); ?>>01-Jan</option>
			<option value="02" <?php selected($month, '02'); ?>>02-Feb</option>
			<option value="03" <?php selected($month, '03'); ?>>03-Mar</option>
			<option value="04" <?php selected($month, '04'); ?>>04-Apr</option>
			<option value="05" <?php selected($month, '05'); ?>>05-May</option>
			<option value="06" <?php selected($month, '06'); ?>>06-Jun</option>
			<option value="07" <?php selected($month, '07'); ?>>07-Jul</option>
			<option value="08" <?php selected($month, '08'); ?>>08-Aug</option>
			<option value="09" <?php selected($month, '09'); ?>>09-Sep</option>
			<option value="10" <?php selected($month, '10'); ?>>10-Oct</option>
			<option value="11" <?php selected($month, '11'); ?>>11-Nov</option>
			<option value="12" <?php selected($month, '12'); ?>>12-Dec</option>
		</select>
		<input style="width: 2em;" type="text" id="article_lock_jj_pdraft" name="article_lock_jj_pdraft" size="2" maxlength="2" autocomplete="off" value="<?php echo $day; ?>" />, 
		<input style="width: 3.4em;" type="text" id="article_lock_aa_pdraft" name="article_lock_aa_pdraft" size="4" maxlength="4" autocomplete="off" value="<?php echo $year; ?>" /> @ 
		<input style="width: 2em;" type="text" id="article_lock_hh_pdraft" name="article_lock_hh_pdraft" size="2" maxlength="2" autocomplete="off" value="<?php echo $hour; ?>" /> : 
		<input style="width: 2em;" type="text" id="article_lock_mn_pdraft" name="article_lock_mn_pdraft" size="2" maxlength="2" autocomplete="off" value="<?php echo $minute; ?>" /></p>
		<div style="background-image: url('<?php echo $url2; ?>'); background-repeat: no-repeat; text-indent: 20px;"><strong>Preview size</strong> (in number of characters)</div>
		<p><input type="text" id="article_lock_preview_size" name="article_lock_preview_size" maxlength="5" size="5" autocomplete="off" value="<?php echo $length; ?>" /></p>
		<div style="background-image: url('<?php echo $url3; ?>'); background-repeat: no-repeat; text-indent: 20px;"><strong>Activate</strong> lock
		<input type="checkbox" name="article_lock_on" id="article_lock_on" value="on" <?php checked($on, 'on'); ?> /></div>
		<p><div style="background-image: url('<?php echo $url4; ?>'); background-repeat: no-repeat; text-indent: 20px;"><strong>Delete</strong> lock data
		<input type="checkbox" name="article_lock_delete" id="article_lock_delete" value="on" /></div></p>
	
<?php	

	}

	function date_check($year, $month, $day, $hour, $minute) {
		
		if(!is_numeric($year) || !is_numeric($day) || !is_numeric($hour) || !is_numeric($minute) || !is_numeric((int) ($month))) {
			return false;
		}

		if($minute > -1 && $minute < 60 && $hour > -1 && $hour < 24 && $month > 0 && $month < 13 && $day > 0 && $day < 32) {
			return true;		
		}

		return false;

	}

	function article_lock_save($post_id) {

		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;	
		if(!isset($_POST['meta_box_article_lock']) || !wp_verify_nonce($_POST['meta_box_article_lock'], 'my_meta_box_article_lock')) return;
	
		if(!current_user_can('edit_post')) return;
	
		$month = $_POST['article_lock_mm_pdraft'];
		$day = $_POST['article_lock_jj_pdraft'];
		$year = $_POST['article_lock_aa_pdraft'];
		$hour = $_POST['article_lock_hh_pdraft'];
		$minute = $_POST['article_lock_mn_pdraft'];
		$length = $_POST['article_lock_preview_size'];
		$on = (isset($_POST['article_lock_on']) && $_POST['article_lock_on']) ? 'on' : 'off';
		
		$delete = $_POST['article_lock_delete'];
		
		if(isset($delete)) {
			delete_post_meta($post_id, 'article_lock_data');
		}
		else {
			if(date_check($year, $month, $day, $hour, $minute)) {

				$article_lock = array(
					'month' => $month,
					'year' => $year,
					'day' => $day,
					'hour' => $hour,
					'minute' => $minute,
					'length' => $length,
					'on' => $on
				);

				update_post_meta($post_id, 'article_lock_data', $article_lock);
			}
			
		}

	}

	/**
	* Truncates text.
	*
	* Cuts a string to the length of $length and replaces the last characters
	* with the ending if the text is longer than length.
	*
	* @param string  $text String to truncate.
	* @param integer $length Length of returned string, including ellipsis.
	* @param string  $ending Ending to be appended to the trimmed string.
	* @param boolean $exact If false, $text will not be cut mid-word
	* @param boolean $considerHtml If true, HTML tags would be handled correctly
	* @return string Trimmed string.
	*/
    function truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
           
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
   
            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';
           
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                    // if tag is a closing tag (f.e. </b>)
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                    // if tag is an opening tag (f.e. <b>)
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
               
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length+$content_length> $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1]+1-$entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
               
                // if the maximum length is reached, get off the loop
                if($total_length>= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }
       
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
       
        // add the defined ending to the text
        $truncate .= $ending;
       
        if($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
       
        return $truncate;
       
    }

?>
