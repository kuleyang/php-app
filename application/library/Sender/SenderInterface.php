<?php


namespace Sender;


interface SenderInterface
{
	/**
	 * @param $content string|resource|callable
	 * @return $this
	 */
	public function setContent($content);

	/**
	 * @return string|resource|callable
	 */
	public function getContent();

	/**
	 * Send response
	 */
	public function send($exit = false);
}