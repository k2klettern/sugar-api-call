<?php


namespace ERPConsummer\helpers;
include_once ('/var/www/ERPConsummer/src/SugarApiCall.class.php');

use ERPConsummer\src\SugarApiCall as SugarCall ;

/**
 * Class ErpIntegrationHelpers
 * @package ERPConsummer\helpers
 */
class ErpIntegrationHelpers{


    /**
     * @param $medio_pago
     * @return string
     */
    public static function normalizeMedioPago($medio_pago){
        switch ($medio_pago){
            case 'TRANSF':
            case 'PROVTRANSF':
                $medio_pago = '01';
                break;
            case 'TARJETA':
                $medio_pago = '02';
                break;
            case 'PAYPAL':
                $medio_pago = '03';
                break;
            case 'DEBITO AUT':
                $medio_pago = '04';
                break;
            case 'PSE':
                $medio_pago = '05';
                break;
            default:
                break;

        }
        return $medio_pago;
    }


    /**
     * @param $forma_pago
     * @return string
     */
    public static function normalizeFormaPago($forma_pago){

        switch ($forma_pago){
            case 'CONTADO':
            case 'RVA 1 PLAZ':
                $forma_pago = '01';
                break;
            default:
                break;

        }
        return $forma_pago;
    }


    /**
     * @param $tipo_doc
     * @return array|string
     */
    public static function normalizeTipoDoc($tipo_doc ){

        $tipo_doc = explode( '-', $tipo_doc );
        $iso_doc = strtolower( $tipo_doc[0] );

        switch ( $iso_doc ) {
            case 'ced':
                $tipo_doc = 'cedula';
                break;
            case 'undef':
            case 'pas':
                $tipo_doc = 'Pasaporte';
                break;
            default:
                $tipo_doc = $iso_doc;
                break;

        }
        return $tipo_doc;

    }


    /*
    Método que normaliza para sugar el pais y provincia nos traemos de la cola del ERP
    ERP nos entrega un unico dato con la estructura: ES-123-456-789
    */
    /**
     * @param null $idLoc
     * @return mixed
     */
    public static function getLocalizacion($idLoc=null){

        $sugarCall = new SugarCall();

        $headers = array(
            "Accept: application/json"
        );

        $loc = explode ('-',$idLoc);

        /*Obtenemos de Comunes la info del país*/
        $url_1 = "https://commons.unir.net/api/v1/countries/".$loc[0];
        $curl_1 = curl_init($url_1);
        $local['pais'] = $sugarCall->getCurl($url_1, $curl_1, $headers);
        if ($loc[0]=='ES'){
            $nivel = $loc[0].'-LEVEL-2';
            $code = $loc[0].'-'.$loc[1].'-'.$loc[2];
        }else{
            $nivel = $loc[0].'-LEVEL-1';
            $code = $loc[0].'-'.$loc[1];
        }

        /*Obtenemos de Comunes la info de la provincia*/
        $url_2 = "https://commons.unir.net/api/v1/countries/".$loc[0]."/divisions/".$nivel."/entities";
        $curl_2 = curl_init($url_2);
        $tmp = $sugarCall->getCurl($url_2, $curl_2, $headers);

        foreach($tmp as $item){

            if ($item['Code'] == $code){

                /*Procesamos la info de la provincia para poder guardarla en sugar
                que espera una id compuesta por la iso del pais y la iso de la provincia (ej: ES-CO)
                */

                $local['provincia']['nombre'] = $item['Name'];

                $tmp_object = $sugarCall->callRestAPI( 'GET',SUGAR_API_URL.'/prkt_Ciudades/','?filter[0][name]='.$local['provincia']['nombre'] );
                if (DEBUG){
                    echo "\n\r------PRKT CIUDADES------\n\r";
                    print_r($tmp_object);
                    echo "\n\r------------\n\r\n\r\n\r";
                }
                if ( !isset( $tmp_object['error'] ) ){

                    $local['provincia']['iso'] =  $tmp_object['records'][0]['id'];
                }
            }
        }
        return $local;

    }



}

