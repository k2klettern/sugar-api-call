<?php

namespace SugarApiCall;
use Exception;
use CURLFile;

class SugarCall {

    public $token;

	public function __construct() {
		require_once dirname(__DIR__) . '/../inc/SugarConstants.php';
		if(!$this->token)
		$this->token = $this->getAuthToken();
	}

	public function getCliente( $email = null ) {
            $object_tmp = $this->getObjectSugar(SUGAR_ENPOINT_C, 'filter?filter[0][email]=' . $email);

            return $object_tmp['records'][0];
	}

	public function updateCliente( $id = null, $fields = null ) {

	        $result = $this->callRestAPI( SUGAR_METHOD_U, SUGAR_API_URL . SUGAR_ENPOINT_C . $id, $this->token, $fields);

            return $result;
	}

	public function uploadAnImageFile ($id = null, $path = null) {

		$url = SUGAR_API_URL . "/Contacts/$id/file/perfil_c";

		$file_arguments = array(
			"format" => "sugar-html-json",
			"delete_if_fails" => true,
			"oauth_token" => $this->token,
		);

		$file_arguments['perfil_c'] = new \CURLFile($path);

		$curl_request = curl_init($url);
		curl_setopt($curl_request, CURLOPT_POST, 1);
		curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($curl_request, CURLOPT_HEADER, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, array(
			"oauth-token: {$this->token}"
		));

		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $file_arguments);

		$curl_response = curl_exec($curl_request);

		curl_close($curl_request);

		return $curl_response;

	}

	public function sanitize_endpoint($endpoint = null) {
			if(!preg_match_all('/^\/(.*?)\/$/', $endpoint, $matches, PREG_SET_ORDER, 0)) {
				$endpoint = str_replace('/', '', $endpoint);
				$endpoint = "/" . $endpoint . "/";
			}

			return $endpoint;
	}

	public function sanitize_varurls($id = null) {
			if(!preg_match_all('/^\?(.*?)$/', $id, $matches, PREG_SET_ORDER, 0)) {
				$id = str_replace('?', '', $id);
				$id = "?" . $id;
			}

		return $id;
	}

	public function getObjectSugar( $endpoint = false, $id = false ) {
		$endpoint = $this->sanitize_endpoint($endpoint);
		$id = $this->sanitize_varurls($id);
		$filter       = "";
		$sugar_object = false;
		if ( ! $this->token ) {
			$this->getAuthToken();
		}


		if ( $this->token ) {

			switch ( $endpoint ) {
				case SUGAR_ENPOINT_O:
					$endpoint     .= $id;
					$sugar_object = $this->callRestAPI( SUGAR_METHOD_G, SUGAR_API_URL . $endpoint, $this->token, $filter );
					break;

				case SUGAR_ENPOINT_C:
					$endpoint     .= $id;
					$sugar_object = $this->callRestAPI( SUGAR_METHOD_G, SUGAR_API_URL . $endpoint, $this->token, $filter );

					break;

				case SUGAR_ENPOINT_U:
					$filter       = '/filter?filter[0][id_asesor_c]=' . $id;
					$sugar_object = $this->callRestAPI( SUGAR_METHOD_G, SUGAR_API_URL . $endpoint, $this->token, $filter );
					break;
				case SUGAR_ENPOINT_T:
				case SUGAR_ENPOINT_N:
				case SUGAR_ENPOINT_E:
				case SUGAR_ENPOINT_CL:
					$filter       = '/filter?filter[0][parent_id]=' . $id;
					$sugar_object = $this->callRestAPI( SUGAR_METHOD_G, SUGAR_API_URL . $endpoint, $this->token, $filter );
					break;
				default:
					$filter       = $id;

					$sugar_object = $this->callRestAPI( SUGAR_METHOD_G, SUGAR_API_URL . $endpoint, $this->token, $filter );
			}
		} else {

			throw new \Exception( 'Error en la generación del token  ' );

		}

		return $sugar_object;

	}


	/*
		Método que monta las llamadas a la api
		*/
	public function callRestAPI( $method, $url, $token, $data = false ) {

		$headers = array(
			"OAuth-Token: " . $this->token,
		);

		$curl = curl_init( $url );

		switch ( $method ) {

			case 'POST':

				curl_setopt( $curl, CURLOPT_POST, 1 );

				if ( $data ) {

					curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $data ) );
				}

				break;

			case 'PUT':

				curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "PUT" );

				if ( $data ) {
					$data = json_encode( $data );

					curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
				}

				break;

			case 'GET':

				if ( $data ) {
					$url .= $data;
					print_r( $url );
				}

				break;
			case 'DELETE':

				curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "DELETE" );
				if ( $data ) {
					$url .= $data;

				}

				break;
			default:

				if ( $data ) {
					$url = sprintf( '%s?%s', $url, http_build_query( $data ) );
				}
		}


		return $this->getCurl( $url, $curl, $headers );

	}

	/*
	Método para obtener el token de sugar
	*/

	private function getAuthToken() {

		$url = SUGAR_API_URL . "/oauth2/token";
		$ch  = curl_init( $url );

		$curl_parms = http_build_query( array(
			'grant_type' => 'password',
			'client_id'  => SUGAR_CLIENT_ID,
			'username'   => SUGAR_USERNAME,
			'password'   => SUGAR_PASSWORD,
			'platform'   => 'api'
		) );

		$token = $this->getCurl( $url, $ch, false, $curl_parms );

		if ( isset( $token['access_token'] ) ) {
			return $token['access_token'];
		} else {
			return false;
		}
	}


	/*
	Ejecuta llamadas curl
	*/
	public function getCurl( $url, $curl, $headers, $params = false ) {

		curl_setopt( $curl, CURLOPT_URL, $url );

		// receive server response ...
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_VERBOSE, true );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		if ( $params ) {
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $params );
		}
		if ( $headers ) {
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		}

		$result = curl_exec( $curl );
		curl_close( $curl );

		$resultado = json_decode( $result, true );

		return $resultado;
	}

}
