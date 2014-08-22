<?php
namespace PHPErrorLog;

use PHPClienteAddr\PHPClienteAddr;

define("PEL_EMERGENCY",0);
define("PEL_ALERT",1);
define("PEL_CRITICAL",2);
define("PEL_ERROR",3);
define("PEL_WARNING",4);
define("PEL_NOTICE",5);
define("PEL_INFO",6);
define("PEL_DEBUG",7);

/**
* Clase para escribir logs desde php
*/
class PHPErrorLog
{
	/**
	 * Texto para los niveles del log
	 * @var array
	 */
	private static $types   = array('emergency','alert','critical','error','warning','notice','info','debug');

	/**
	 * Arreglo con los headers permitidos
	 * @var array
	 */
	private static $headers = array('Content-type','From','Cc','Bcc','Reply-To','Subject','Return-Path');

	/**
	 * Formato de la fecha del log
	 * @var string
	 */
	private static $date_format = 'D d M H:m:s Y';

	/**
	 * Funcion para escribir los logs
	 * @param  string  $message     Cadena de texto con el mensaje se desea mandar al log
	 * @param  integer $type        Nivel del error con el que queremos etiquetar el log
	 * @param  string  $destination Cadena de texto con el email destinatario del log o la ruta absoluta del archivo donde se desea almacenar el log
	 * @param  array   $headers     Arreglo asociativo con las cabeceras adicionales correspondientes a un email
	 * @return void
	 */
	public static function write($message='',$type = 3,$destination='',$headers=array())
	{
		$arguments   = array();

		$message     = self::validateMessage($message,$type);

		$emails      = self::validaEmails($destination);

		$headers     = self::validarHeaders($headers,$destination);

		if(!!$message)
		{
			array_push($arguments, $message);

			if(!!$emails)
			{
				array_push($arguments,1,$emails,$headers);
			}
			else if(self::isFile($destination))
			{
				array_push($arguments,3,$destination);
			}
			else
			{
				$arguments[0] = trim(preg_replace('/\s\s+/', ' ', array_pop(explode("]",$arguments[0]))));
			}

			return call_user_func_array('error_log',$arguments);
		}

		self::messageFalse();

		return FALSE;
	}

	/**
	 * Valida si una cadena de texto es un email
	 * @param  styring  $destination Cadena de texto a evaluar
	 * @return boolean               Devuelve TRUE si es email o FALSE en caso contrario
	 */
	private static function validaEmails($destination)
	{
		return implode(
			',', 
			array_filter(
				array_map(
					function($val){
						return !!filter_var(
								str_replace('>', '', array_pop(explode('<',$val)))
								,FILTER_VALIDATE_EMAIL
							)?$val:FALSE
						;
					},
					array_map('trim',explode(',',$destination))
				)
			)
		);
	}

	/**
	 * Valida si una cadena de texto es un archivo valido
	 * @param  string  $file Cadena de texto a evaluar
	 * @return boolean       Devuelve TRUE si es un archivo valido o FALSE en caso contrario
	 */
	private static function isFile($file)
	{
		if(!!file_exists($file))
		{
			if(!!is_writable($file))
			{
				return TRUE;
			}
			else
			{
				self::fileNotWritable($file);
			}
		}
		return FALSE;		
	}

	/**
	 * Devuelve la fecha para el log
	 * @return string
	 */
	private static function getDate()
	{
		return date(self::$date_format);
	}

	/**
	 * Devuelve una cadena de texto con el tipo de log
	 * @param  integer $type Numero de tipo de log que se desea
	 * @return string
	 */
	private static function getType($type)
	{
		return self::$types[$type<0||$type>7?3:$type];
	}

	/**
	 * Escribe un log para informarle al usuario que se invoco el metodo write sin mensaje
	 * @return boolean     Devuelve TRUE si se ejecuta la funcion errorLog exitosamente o FALSE en caso contrario
	 */
	private static function messageFalse()
	{
		return error_log("PHPErrorLog: Se necesita un mensaje valido");
	}

	/**
	 * Escribe un log para informarle al usuario que no se puede escribir en el archivo suministrado
	 * @param  string $file Archivo suministrado
	 * @return boolean      Devuelve TRUE si se ejecuta la funcion errorLog exitosamente o FALSE en caso contrario
	 */
	private static function fileNotWritable($file)
	{
		return error_log("PHPErrorLog: No se tienen permisos de escritura sobre el archivo ".$file);
	}

	/**
	 * Valida el mensaje suministrado por el usuario y devuelve un mensaje estructurado
	 * @param  string $msg    Cadena de texto con el mensaje a evaluar
	 * @param  string $type   Tipo de mensaje
	 * @return string|boolean Devuelve un mensaje estructurado o FALSE en caso de que no sea un mensaje valido
	 */
	private static function validateMessage($msg,$type)
	{
		$msg = trim($msg);

		if($msg!='')
		{
			return "[".self::getDate()."] [".self::getType($type)."] [client ".PHPClienteAddr::$IP."] $msg\n";
		}
		
		return FALSE;
	}

	/**
	 * Permite validar los headers que el usuario desea incorporar al correo electronico que se enviara
	 * @param  array  $headers     Arreglo asociativo con los headers
	 * @param  string $destination Caena de texto separada por comas de los destinatarios
	 * @return string              Cadena de texto de headersGuia
	 */
	private static function validarHeaders($headers,$destination)
	{
		
		foreach ($headers as $key => $header)
		{
			if(!in_array($key, self::$headers))
			{
				unset($headers[$key]);
			}
			else
			{
				$headers[$key] = "$key:$header";
			}
		}
		if(count($headers)>0)
		{
			$headers['To'] = $destination;
			array_unshift($headers, "MIME-Version: 1.0","X-Priority: 1");
			array_push($headers, "X-Mailer: PHP/".phpversion());	
		}
		return implode("\r\n", $headers);
	}
}
?>