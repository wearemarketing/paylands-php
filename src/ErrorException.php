<?php

namespace WAM\Paylands;

use Http\Client\Exception as HttpException;

/**
 * Class ErrorException.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class ErrorException extends \Exception implements HttpException
{
}
