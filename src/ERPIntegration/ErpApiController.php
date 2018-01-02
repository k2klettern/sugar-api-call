<?php


/*
Clase que controla las llamadas a la api de gestor, carga una conexión u otra dependiendo del endpoint al que ataquemos.
Por cada nuevo endpoint, se generará una conexión (si es necesario) y un método

*/


namespace ERPIntegration\ErpApiController;

class ErpApiController {


    public $soapClient;
    private $method;
    public $object;
    

    function __construct($method){
        $this->method = $method;
        $this->conexion();

    }

    private function conexion(){
        switch ($this->method) {
            case 'ObtenerAlumnoPoridAlumno':
                 $this->soapClient = new \SoapClient(URL_ERP, array("soap_version"=> SOAP_1_1,
        "trace"=>1,
        "exceptions"=>0));
                 $this->soapClient->__setLocation("https://servicios.unir.net/WS_Integracion/unir.servicios.integracion.integracionerp.svc/basicHttpTransport");
                break;
            
            default:
                $this->soapClient = new \SoapClient(URL_ERP, array("soap_version"=> SOAP_1_1,
                                                                    "trace"=>1,
                                                                    "exceptions"=>0));
                 $this->soapClient->__setLocation("https://servicios.unir.net/WS_Integracion/unir.servicios.integracion.integracionerp.svc/basicHttpTransport");
                break;
        }
    }


    /*
    Método para hacer la llamada al endpoint concreto homónimo.
    */
    public function ObtenerAlumnoPoridAlumno($idCliente = null){
             $request_param = array(
        "devKey" => ERP_DEVKEY,
        "password" => ERP_PASS,
        "idAlumno" => $idCliente,
        );

             try
        {
            $this->object = json_decode(json_encode($this->soapClient->ObtenerAlumnoPoridAlumno($request_param)),true);
        }
        catch (Exception $e)
        {
            $this->object = false;
            echo "<h2>Exception Error!</h2>";
            echo $e->getMessage();

        }
    }
}