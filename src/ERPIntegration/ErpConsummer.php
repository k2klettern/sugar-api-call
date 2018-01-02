<?php
/**
 * Created by PhpStorm.
 * User: javi
 * Date: 20/12/17
 * Time: 12:57
 */



namespace ERPIntegration;

use ERPConsummer\src\ErpApiController as ErpApiController;
use ERPConsummer\helpers\ErpIntegrationHelpers as ErpHelpers;

/**
 * Class ErpConsummer
 * @package ErpConsummer
 */
class ErpConsummer
{
    public $queue;
    public $msg = array();
    public $normalized = array();

    /**
     * ErpConsummer constructor.
     */
    public function __construct($queue)
    {
        if ( $queue ){

            $this->queue = str_replace('ha-','',$queue);

        }else{

            throw new Exception( 'No Queue!!' );
        }

    }

    /**
     * @return mixed
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @return array
     */
    public function getNormalized()
    {
        return $this->normalized;
    }

    /**
     * @param array $msg
     */
    public function setMessage( $msg )
    {
        $this->msg = json_decode( $msg, true );
    }


    public function normalizeFromQueue (){
        $method_name = $this->queue.'_normalizer';
        $this->{$method_name}();

    }


    public function ReservaCreada_normalizer (){

        $ErpController = new ErpApiController( 'ObtenerAlumnoPoridAlumno' );

        $ErpController->ObtenerAlumnoPoridAlumno( $this->msg['ClienteIdIntegracion'] );

        if (DEBUG){
            echo "\n\r\n\r cliente \n\r\n\r";
            print_r($ErpController->object);
            echo "\n\r-----------------------\n\r";
        }

        $this->msg = array_merge( $this->msg,$ErpController->object );

        $array_loc = ErpHelpers::getLocalizacion($this->msg['Facturacion']['TerritorioIdIntegracion']);
        //Oportunidad
        $this->normalized = array();
        $this->normalized['opportunity']['id']                      = $this->msg['CuponIdIntegracion'];
        $this->normalized['opportunity']['date_pago_reserva_c']     = $this->msg['Vencimiento']['FechaHora'];
        $this->normalized['opportunity']['bi_medio_pago_c']         = $this->msg['FormaPagoIdIntegracion'];
        $this->normalized['opportunity']['medio_pago_c']         	  = ErpHelpers::normalizeMedioPago($this->msg['FormaPagoIdIntegracion']);
        $this->normalized['opportunity']['type_currency_ c']        = $this->msg['MonedaPago'];
        $this->normalized['opportunity']['currency_id']             = $this->msg['MonedaPago'];
        $this->normalized['opportunity']['forma_pago_c']            = ErpHelpers::normalizeFormaPago($this->msg['TerminoPagoIdIntegracion']);
        $this->normalized['opportunity']['client_email_c']          = $this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['Email'];
        $this->normalized['opportunity']['phone_cliente_c']         = $this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['TlfMovilPrefijo'].$this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['TlfMovilNumero'];
        $this->normalized['opportunity']['id_reserva']              = $this->msg['IdReserva'];
        $this->normalized['opportunity']['ConvocatoriaIdIntegracion'] = $this->msg['ConvocatoriaIdIntegracion'];
        $this->normalized['opportunity']['provincia_c']             = $array_loc['provincia']['iso'];
        $this->normalized['opportunity']['reserva_c']               = $this->msg['Vencimiento']['Importe'];
        $this->normalized['opportunity']['id_pais_c']               = $array_loc['pais']['IsoCode'];
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
        $this->normalized['opportunity']['campo_gordo_1']           = json_encode($this->msg);

        //Cliente
        $this->normalized['client']['first_name']                   = $this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['sNombreAlumno'];
        $this->normalized['client']['last_name']                    = $this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['Apellido_1'] . ' ' . $this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['Apellido_2'];
        $this->normalized['client']['lugar_nacimiento_c']           = $array_loc['pais']['Name'];
        $this->normalized['client']['birthdate']                    = $this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['FechaNacimiento'];
        $this->normalized['client']['document_type_c']              = ErpHelpers::normalizeTipoDoc($this->msg['Facturacion']['TipoDocumentoIdentidadIdIntegracion']);
        $this->normalized['client']['document_number_c']            = $this->msg['Facturacion']['DocumentoIdentidad'];
        $this->normalized['client']['phone_mobile']                 = $this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['TlfMovilPrefijo'].' '.$this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['TlfMovilNumero'];
        $this->normalized['client']['client_address_c']             = $this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['Direccion'];
        $this->normalized['client']['postal_code_c']                = $this->msg['ObtenerAlumnoPoridAlumnoResult']['Respuesta']['datosAlumno']['CP'];
        $this->normalized['client']['prkt_ciudades_id_c']           = $array_loc['provincia']['iso'];
        $this->normalized['client']['pais_de_residencia_c']         = $array_loc['pais']['IsoCode'];




        if (DEBUG) {
            echo "\n\r\n\rnormalized\n\r\n\r";
            print_r($normalized);
            echo "\n\r-----------------------\n\r";
        }

    }
    public function ReservaAnulada_normalizer (){
        echo "entro a ReservaAnulada\n\r";
        $this->normalized['opportunity']['campo_gordo_11']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
    }

    public function PreMatriculaCompletada_normalizer (){
        echo "entro a PreMatriculaCompletada\n\r";

        $this->normalized['opportunity']['id']                      = $this->msg['CuponIdIntegracion'];
        $this->normalized['opportunity']['id_prematricula']         = $this->msg['IdPreMatricula'];
        $this->normalized['opportunity']['campo_gordo_2']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;

        $this->normalized['client']['titulacion_acceso_c']          = $this->msg['TitulacionAcceso']['Titulo'];
        $this->normalized['client']['universidad_acceso_c']         = $this->msg['TitulacionAcceso']['InstitucionDocente'];

    }
    public function PreMatriculaModificada_normalizer (){
        echo "entro a PreMatriculaModificada\n\r";
        $this->normalized['opportunity']['campo_gordo_3']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
    }
    public function PreMatriculaAnulada_normalizer (){
        echo "entro a PreMatriculaAnulada\n\r";
        $this->normalized['opportunity']['campo_gordo_4']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
    }
    public function MatriculaRealizada_normalizer (){
        echo "entro a AdmisionMasterCompletada\n\r";
        $this->normalized['opportunity']['campo_gordo_5']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
    }

    public function MatriculaDesestimada_normalizer (){
        echo "entro a ConvocatoriaReservaCambiada\n\r";
        $this->normalized['opportunity']['campo_gordo_6']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
    }

    public function MatriculaRecibida_normalizer (){
        echo "entro a ConvocatoriaReservaCambiada\n\r";
        $this->normalized['opportunity']['campo_gordo_7']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
    }

    public function MatriculaAnulada_normalizer (){
        echo "entro a ConvocatoriaReservaCambiada\n\r";
        $this->normalized['opportunity']['campo_gordo_8']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
    }

    public function MatriculaRecuperada_normalizer (){
        echo "entro a ConvocatoriaReservaCambiada\n\r";
        $this->normalized['opportunity']['campo_gordo_9']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
    }

    public function MatriculaReiniciada_normalizer (){
        echo "entro a ConvocatoriaReservaCambiada\n\r";
        $this->normalized['opportunity']['campo_gordo_10']           = json_encode($this->msg);
        $this->normalized['opportunity']['fases_c']                 = '';
        $this->normalized['opportunity']['estado_c']                = $this->queue;
    }



}