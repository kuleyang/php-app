<?php


namespace Resque\Resque\Job;
use Resque\Resque\Exception;
/**
 * Exception to be thrown if a job should not be performed/run.
 *
 * @package		Resque/Job

 * @license		http://www.opensource.org/licenses/mit-license.php
 */
class DontPerform extends Exception
{

}