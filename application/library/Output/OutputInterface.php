<?php


namespace Output;

use Sender\SenderInterface;

interface OutputInterface
{
	/**
	 * Output the content
	 */
	public function __invoke(SenderInterface $sender);

}