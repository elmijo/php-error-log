PHP Error Log
=============

Una pequeña libreria para manejar los logs de tu aplicación de forma simple.. rapida.. y directa!!. En lineas lo que se hizo fue crear una capa de abstracción entre el programador y la función **error_log()** de PHP.

## Entonces.. ¿Que tiene de especial?

* La gran ventaja es que puedes escribir logs de las tres formas posibles usando **error_log()** con una sola función solo cambiando los parametros.
* **SIEMPRE!!** va ha escribir el log, a menos que el mensaje sea *empty*.

## Guia Rapida

### Instalación
Lo podemos hacer a travéz de [composer](https://getcomposer.org/doc/00-intro.md):
```php
"require":{
  ...
  "elmijo/php-error-log": "dev-master"
  ...
}
```
### Uso Rapido
```php
require '../vendor/autoload.php';

use PHPErrorLog\PHPErrorLog;

PHPErrorLog::write('probando...');

```

## ¿Cuantas maneras de escribir logs tengo disponibles?

> **erro_log** te permite escribir o manejar los logs de tres maneras, como lo son: en el archivo error.log por defecto, enviandola por correo electronico o escribiendo los en un archivo definido por el usuario

## Conociendo a la función magica..

Esta libreria esta compuesta por una unica clase, la cual contiene un metodo *pulic static* llamado ***write***, el cual nos sirve para manejar los logs en cualquiera de las formas posibles.

* Descripción: Función para escribir logs
* Parametros:
  * message: *string* **(requerida)** Cadena de texto con el mensaje.
  * type: *integer* **(opcional,default 3)** Nivel del error con el que queremos etiquetar el log, acepta valores del 0 - 7.
  * destination: *string* **(opcional)** puede ser una lista de correos electronicos o un path absoluto del archivo donde queremos que se escriban los logs.
  * headers: *array* **(opcional)** Arreglo asociativo con las cabeceras adicionales que deseamos agregar al correo electronico, este parametro solo tendra valides si vamos a enviar el log por email.

### Nivel del error 

La clase provee al desarrollador de 8 constantes para facilitar el uso de este parametro

| constatnte    | valor | texto     |
| ------------- |:-----:| ---------:|
| PEL_EMERGENCY | 0     | emergency |
| PEL_ALERT     | 1     | alert     |
| PEL_CRITICAL  | 2     | critical  |
| PEL_ERROR     | 3     | error     |
| PEL_WARNING   | 4     | warning   |
| PEL_NOTICE    | 5     | notice    |
| PEL_INFO      | 6     | info      |
| PEL_DEBUG     | 7     | debug     |


### Cabeceras Adicionales

**error_log()** utiliza la funcion **mail()** cuando se pasa un email como destinatario del log, eso quiere decir que las mismas cabeceras que podemos definir en **mail()** al momento de enviar un correo las vamos a poder definir el la función **error_log()**. Sin embargo, **PHPLorError** solo soportara una pequeña lista de cabeceras y aqui se la presentamos:

* From
* Reply-To
* Content-type
* Cc
* Bcc
* Subject
* Return-Path

### Enviando Logs al archivo por defecto
```php
PHPErrorLog::write('PHPErrorLog: probando... logs');

PHPErrorLog::write('MiSistema: probando... logs\n\t\notra forma de hacer logs');
```

### Enviando Logs por Email
Para poder enviar 
```php
```

### Enviando Logs a un Archivo Definifo por el Usuario
```php
PHPErrorLog::write('probando...',1,realpath('dev.log'));
```
