<?php

if( !class_exists('CVM_Vimeo') ){
	require_once CVM_PATH.'includes/libs/vimeo.class.php';
}

class CVM_Video_Import extends CVM_Vimeo{
	
	private $results;
	private $total_items;
	private $page;
	private $errors;
	
	public function __construct( $args ){
		
		$defaults = array(
			'source' 		=> 'vimeo', // video source
			'feed'			=> 'search', // type of feed to retrieve ( search, album, channel, user or group )
			'query'			=> false, // feed query - can contain username, playlist ID or serach query
			'results' 		=> 20, // number of results to retrieve
			'page'			=> 0,
			'response' 		=> 'json', // Vimeo response type
			'order'			=> 'new', // order
			'search_results' => ''
		);
		
		$data = wp_parse_args($args, $defaults);
		
		// if no query is specified, bail out
		if( !$data['query'] ){
			return false;
		}
		
		$request_args = array(
			'feed' 		=> $data['feed'],
			'feed_id' 	=> $data['query'],
			/*'feed_type' => '',*/
			'page' 		=> $data['page'],
			'response' 	=> $data['response'],
			'sort' 		=> $data['order'],
			'search_results' => $data['search_results']
		);
		parent::__construct( $request_args );
		$content = parent::request_feed();
		
		if( is_wp_error( $content ) || 200 != $content['response']['code'] ){
			if( is_wp_error( $content ) ){
				$this->errors = new WP_Error();
				$this->errors->add( 'cvm_wp_error', $content->get_error_message(), $content->get_error_data() );
			}			
			return false;
		}
		
		$result = json_decode( $content['body'], true );
		
		// set up Vimeo query errors if any
		if( isset( $result['error'] ) ){
			$this->errors = new WP_Error();
			$this->errors->add( 'cvm_vimeo_query_error', __('Query to Vimeo failed.', 'codeflavors-vimeo-video-post-lite'), $result['error_description']);
		}
		
		/* single video entry */
		if( 'video' == $request_args['feed'] ){
			if( isset( $result['uri'] ) ){
				$this->results = $this->format_video_entry( $result );
			}else{
				$this->results = array();
			}
			return;
		}
		
		// processign multi videos playlists
		if( isset( $result['data'] ) ){
			$raw_entries = $result['data'];
		}else{
			$raw_entries = array();
		}	
		
		$entries =	array();
		foreach ( $raw_entries as $entry ){			
			$entries[] = $this->format_video_entry( $entry );		
		}		
		
		$this->results = $entries;
		$this->total_items = isset( $result['total'] ) ? $result['total'] : 0;
		$this->page = isset( $result['page'] ) ? $result['page'] : 0;
	}
	
/**
	 * Formats the response from the feed for a single entry
	 * @param array $entry
	 */
	private function format_video_entry( $raw_entry ){
		$entry = cvm_format_video_entry( $raw_entry );
		return $entry;
	}
	
	public function get_feed(){
		return $this->results;
	}
	
	public function get_total_items(){
		return $this->total_items;
	}

	public function get_page(){
		return $this->page;
	}
	
	public function get_errors(){
		return $this->errors;
	}
}