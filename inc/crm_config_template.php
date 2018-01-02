<?php


/**
 *  Conexion a Sugar
 */

//define ('SUGAR_API_URL', 'http://360.unir.net/rest/v10');
//define ('SUGAR_API_URL', 'http://sugarcrm.desunir.net/dev/rest/v10');
//define ('SUGAR_API_URL', 'http://sugarcrm.preunir.net/pre/rest/v10');
define ('SUGAR_API_URL', 'http://www.prakton-crm.com/pruebas/unir/dev-1/rest/v10');

define ('SUGAR_LOGGING', 1);
define ('SUGAR_USERNAME', 'api_user');
define ('SUGAR_PASSWORD', 'Unir2016@');
define ('SUGAR_ENDPOINT' , '/unir/import');
define ('SUGAR_CLIENT_ID' , 'sugar');
define ('SUGAR_METHOD_I'  , 'POST');
define ('SUGAR_METHOD_U'  , 'PUT');
define ('SUGAR_METHOD_G'  , 'GET');
define ('SUGAR_METHOD_D'  , 'DELETE');
define ('SUGAR_ENPOINT_O' , '/Opportunities/');
define ('SUGAR_ENPOINT_C' , '/Contacts/');
define ('SUGAR_ENPOINT_A' , '/Activity/');
define ('SUGAR_ENPOINT_U' , '/Users/');
define ('SUGAR_ENPOINT_T' , '/Tasks/');
define ('SUGAR_ENPOINT_N' , '/Notes/');
define ('SUGAR_ENPOINT_E' , '/Emails/');
define ('SUGAR_ENPOINT_CL' , '/Calls/');
define ('SUGAR_ENPOINT_PR' , '/prod_Productos/');

/**
 * Conexion RabbitMq
 */

//DES
define ('RABBIT_HOST', 'ovd-ha.unir.net'); 
define ('RABBIT_PORT', '5672');
define ('RABBIT_USERNAME', 'desarrollo_web');
define ('RABBIT_PASSWORD', 'r4bbi7Mo14!');
define ('RABBIT_VHOST', 'des');
define ('RABBIT_EXCHANGE', 'exchange');
define ('RABBIT_CONSUMMER_TAG', 'consumer');

//LOCAL
define ('RABBIT_HOST_LOCAL', 'localhost'); 
define ('RABBIT_PORT_LOCAL', '5672');
define ('RABBIT_USERNAME_LOCAL', 'admin');
define ('RABBIT_PASSWORD_LOCAL', 'jfajardj');
define ('RABBIT_VHOST_LOCAL', 'servicios');
define ('RABBIT_EXCHANGE_LOCAL', 'exchange');
define ('RABBIT_CONSUMMER_LOCAL', 'consumer');
/*
//PRO
define ('RABBIT_HOST', 'acm-rabbitmq01.unir.cloud');
define ('RABBIT_PORT', '5672');
define ('RABBIT_USERNAME', 'servicios');
define ('RABBIT_PASSWORD', 'qKPOk27ll');
define ('RABBIT_VHOST', 'servicios');
define ('RABBIT_EXCHANGE', 'exchange');
*/

/*Conexión a BD Gestor*/
define ('GESTOR_CUSTOM_HOST', 'acm-sql04.unir.cloud');
define ('GESTOR_CUSTOM_USERNAME', 'web_services@unir');
define ('GESTOR_CUSTOM_PASSWORD', '6oZq708A');
define ('GESTOR_CUSTOM_DATABASE', 'db_gestor');

/*BI*/
define ('BI_HOST', 'ovbi-ha.unir.net');
define ('BI_USERNAME', 'CRM@sugar');
define ('BI_PASSWORD', 'Almansa101CRM');
define ('BI_DATABASE', 'db_BI');


/*
* Servicio del ERP PRO
*/

define ('QUEUE_ERP_RESERVA','CrmIntegracion.IntegrationServices.Bus.Subscribers.CrmSubscriber');
define ('URL_ERP_DES','https://servicios.desunir.net/WS_Integracion/unir.servicios.integracion.integracionerp.svc?wsdl');
define ('URL_ERP','https://servicios.unir.net/WS_Integracion/unir.servicios.integracion.integracionerp.svc?wsdl');
define ('ERP_PASS','T#02*234ftrw');
define ('ERP_DEVKEY','Un1r#0214*');
define ('ERP_ACCOUNT', 'AR701441');
define ('ERP_TIPO', 'PAS-ES');
define ('ERP_PAIS','CO');

/*Conexion a Redis*/
define ('REDIS_SCHEME', 'tcp'); 
define ('REDIS_HOST_PRO', 'acm-redis.unir.cloud');
define ('REDIS_HOST_DES', 'ovd-ha.unir.net');
define ('REDIS_PORT', 6379);
define ('REDIS_DB_PRO', 13);
define ('REDIS_DB_DES', 4);

/*Otras constantes*/
define ('QUEUE', 'ha-redis-crm');
//define ('QUEUE', 'ha-sincronizacion-diaria');
define ('DEBUG', true);


