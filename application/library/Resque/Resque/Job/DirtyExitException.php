<?php


namespace Resque\Resque\Job;
use RuntimeException as RuntimeException;
/**
 * Runtime exception class for a job that does not exit cleanly.
 *
 * @package		Resque/Job

 * @license		http://www.opensource.org/licenses/mit-license.php
 */
class DirtyExitException extends RuntimeException
{

}