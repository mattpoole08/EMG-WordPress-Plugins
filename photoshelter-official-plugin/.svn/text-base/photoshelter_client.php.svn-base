<?php
	class Photoshelter_Client {
	const BASE = 'http://www.photoshelter.com/';
	const BASE_URL = 'https://www.photoshelter.com/bsapi/1.1/';
		
	var $uri;
	var $username;
	var $password;
	var $first_name;
	var $last_name;
	var $account_type;
	var $user_id;
	var $logged_in;
	var $requst_attempts;
						
	function __construct() {
		$this->logged_in = false;
	}
	
	function __destruct() {}
	
	function auth() {
		$options = get_option('photoshelter');
		$url = Photoshelter_Client::BASE_URL . 'auth?U_EMAIL=' . $this->good_encode($options['username']) . "&U_PASSWORD=" . $this->good_encode($options['password']);
		$data = $this->make_request('GET', $url, false);

		if( is_wp_error( $data ) )
		{
			update_option('photoshelter_logged_in', __('Invalid Login', 'photoshelter') );
			$this->logged_in = false;
			return false;
		}
		
		if( isset( $data->response->success) && is_object( $data->response->success ) ) {
			$this->user_id = (string) $data->session->U_ID;
			
			$new_options = array(
				'user_id'		=> $this->user_id
			);
						
			if (isset($data->return->org)) {
				$r = (array) $data->return;
				$o = $r['org'];

				if (is_array($o)) {
					$orgs = array();
					foreach($o as $org) {
						array_push($orgs, (array) $org);
					}
					$new_options = array_merge( array('orgs' => $orgs), $new_options);
				} else {
					$new_options = array_merge( array('orgs' => array((array) $o)), $new_options);
				}
				
				
			}
			
			update_option('photoshelter', array_merge( $options, $new_options ) );
			$this->logged_in = true;
			update_option('photoshelter_logged_in', true);
			return true;
		} else {
			$this->logged_in = false;
			$error = strip_tags((string) $data->response->error->message);
			update_option('photoshelter_logged_in', $error );
			delete_option('ps_cookies');
			return true;
		}
		
		//return false;
	}
	
	function org_auth($O_ID) {
		$options = get_option('photoshelter');

		if ($O_ID == '-1') {

			$new_options = array(
				'auth_org_name'		=> NULL,
				'auth_org_id' => NULL
			);
			
			
			update_option('photoshelter', array_merge( $options, $new_options ) );
			$this->auth();
			return;
		}
				
		$url = Photoshelter_Client::BASE_URL . 'org-auth?O_ID=' . $O_ID;
		
		$data = $this->make_request('GET', $url);
		
		if( isset( $data->response->success) && is_object( $data->response->success ) ) {
			$orgs = $options['orgs'];
			
			foreach($orgs as $org) {
				if ($O_ID == $org['O_ID']) {
					$org_name = $org['O_NAME'];
				}
			}
			
			$new_options = array(
				'auth_org_name'		=> $org_name,
				'auth_org_id' => $O_ID
			);
			
			
			update_option('photoshelter', array_merge( $options, $new_options ) );
		} else {
			$error = strip_tags((string) $data->response->error->message);
			update_option('photoshelter_org', $error );
		}
		
		return $data;
	}
	
	function idle(){
	}
	
	function gal_qry($offset = null, $hash = null){
		$url = Photoshelter_Client::BASE_URL . 'gal-qry-pag?G_SORT=G_MTIME&G_SORT_DIR=DSC&G_STATUS=1';

		if($offset) {
			$url .= '&_bqO=' . $offset . '&_bqH=' . $hash;
		}

		$data = $this->make_request('GET', $url);
		$galleries = array();
		
		foreach($data->return->gal as $gallery) {
			array_push($galleries, (array) $gallery);
		}
		
		return array("galleries" => $galleries, "pag" => $data->return->pag);
	}
	
	function img_qry($g_id = '', $offset = null, $hash = null){
		$url = Photoshelter_Client::BASE_URL . 'img-qry-pag?I_SORT=I_FILE_NAME';
		
		if(!empty($g_id)) {
			$url .= '&G_ID=' . rawurlencode($g_id);
		}
		
		if($offset) {
			$url .= '&_bqO=' . $offset . '&_bqH=' . $hash;
		}
		
		$data = $this->make_request('GET', $url);
		
		$images = array();
		
		foreach($data->return->img as $img) {
			array_push($images, (array) $img);
		}
		
		return array('images' => $images, 'pag' => $data->return->pag);
		
	}
	
	function img_get($i_id) {
		$url = Photoshelter_Client::BASE_URL . 'img-get?I_ID=' . $i_id. '?mcSet=t';
		$data = $this->make_request('GET', $url);
		return (array) $data->return;
	}
	
	function img_widget_get($i_id) {
		$url = Photoshelter_Client::BASE . '/rss/imgWidget/' .$i_id. '?mcSet=t';
		$data = $this->make_request('GET', $url);
		return (array) $data;
	}
	
	function make_request($method, $url, $use_cookie = true) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true );
		

		switch ($method) {
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
				break;
			case 'PUT':
				curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				break;
		}

		if ( $use_cookie ) {
			$cookies = get_option('ps_cookies');
			$last = count( $cookies ) - 1;
			if( $last >= 0 ) {
				curl_setopt( $ch, CURLOPT_COOKIE, $cookies['ch_' . $last]);
			}
		}
		
		$r = curl_exec($ch);
		curl_close($ch);
		
		$cookie = $this->handle_cookie($r);
		update_option('ps_cookies',$cookie);
		
		$return = $this->parse_response($r);
		
		//check for session
		if ($return->response->error && (string) $return->response->error->class == 'SessionRequiredErr') {
			$this->logged_in = false;
			update_option('photoshelter_logged_in', false);
			delete_option('ps_cookies');
			throw new Exception('session error');
		}

						
		return $return;
	}
	
	function parse_response($response) {
		$response = preg_split('/\r\n\r\n/', $response, 2);
		if (count($response)) {
			unset($response[0]);
			$body = implode($response);
		} else {
			$body = $response;
		}
		return simplexml_load_string( $body );
	}
	
	function handle_cookie( $response, $delimiter ="\r\n" )	{
		$response_headers = explode( $delimiter, $response );
		$ps_cookie = array();
		preg_match_all( '/^Set-Cookie:\ (.+)/m', implode("\r\n",$response_headers), $cookies );
		foreach( $cookies[1] as $ck => $cv )
		{
			$ps_cookie['ch_' . $ck] = trim($cv);
		}
		if( empty( $ps_cookie ) )
			return false;
			
	 	return $ps_cookie;
	}
	
	function ss_preset_qry() {
		$url = Photoshelter_Client::BASE_URL . 'ss-preset-qry';

		$data = $this->make_request('GET', $url);
		$return = (array) $data->return;

		$psets = (array) $return['preset'];

		$presets = array();
		if (is_array($psets) && !empty($psets[0])) {
			foreach($psets as $preset) {
				array_push($presets, (array) $preset);
			}
		} else if (isset($psets)) {
			array_push($presets, (array) $psets);
		}
		return $presets;
	}
	
	function ss_preset_get($d_id, $g_id) {
		$url = Photoshelter_Client::BASE_URL . 'ss-preset-get?D_ID=' . $d_id . '&G_ID=' . $g_id;

		$data = $this->make_request('GET', $url);
		
		return $data->return->preset;
	}
	
	function img_search($term = null, $offset = null, $hash = null) {
		$url = Photoshelter_Client::BASE_URL . 'img-search?I_SORT=MTIME&I_SORT_DIR=DSC';
		
		if(!empty($term) && !$offset) {
			$url .= '&I_DSC=' . urlencode($term);
		}
		
		if($offset) {
			$url .= '&_bqO=' . $offset . '&_bqH=' . $hash;
		}
		
		$options = get_option('photoshelter');
		if (isset($options['auth_org_id'])) {
			$url .= '&I_ORG_ID='.$options['auth_org_id'];
		} else {
			$url .= '&I_USER_ID='.$options['user_id'];
		}

		$data = $this->make_request('GET', $url);

		$imgs = $data->return->img;

		$images = array();
		
		if (count($imgs) > 0) {
		
			foreach($imgs as $img) {
				array_push($images, (array) $img);
			}
		} else if (!empty($imgs)) {
			array_push($images, $imgs);
		}
		
		return array(
			'images' => $images, 
			'pag' => $data->return->pag
		);
	}
	
	function good_encode($str) {
		return urlencode(html_entity_decode($str));
	}
}
?>
