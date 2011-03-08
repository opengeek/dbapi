<?php
/**
 * Build the setup options form.
 *
 * @package dbapi
 * @subpackage build
 */

$output = '
<label for="dbapi-activatePlugin">Activate DBAPI Plugin:</label>
<input type="checkbox" name="activatePlugin" id="dbapi-activatePlugin" value="1" />
';

return $output;