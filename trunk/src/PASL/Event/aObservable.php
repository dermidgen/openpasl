<?php
/**
 * OpenPASL
 *
 * Copyright (c) 2008, Danny Graham, Scott Thundercloud
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of the Danny Graham, Scott Thundercloud, nor the names of
 *     their contributors may be used to endorse or promote products derived from
 *     this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @copyright Copyright (c) 2008, Danny Graham, Scott Thundercloud
 */
namespace PASL\Event;

require_once('PASL/Event/Event.php');

use PASL\Event;

/**
 * PHP implementation of an event dispatcher
 *
 * @package Event
 */
abstract class aObservable
{
	/**
	 * Holds the event types
	 *
	 * @var array
	 */
	protected $events = Array();

	/**
	 * Method to add an event type
	 *
	 * @param string $strType
	 * @return void
	 */
	private function addEventType($strType)
	{
		if (!isset($this->events[$strType])) $this->events[$strType] = new Event($strType);
	}

	/**
	 * Internal method to dispatch events on a specific event type.
	 *
	 * @param object $Object
	 * @return void
	 */
	private function dispatchQueue($Object)
	{
		$eName = $Object->type;
		$Event = $this->events[$eName];

		if(empty($Event)) return;

		for($i=0; $i < count($Event->observers); $i++)
		{
			$callbackFunction = ($Event->observers[$i]->{$eName}) ? $Event->observers[$i]->{$eName} : $Event->observers[$i];

			if(is_array($callbackFunction))
			{
				$Obj = $callbackFunction[0];
				$MethodName = $callbackFunction[1];

				call_user_method($MethodName, $Obj, $Object);
			}
			else call_user_func($callbackFunction, $Object);
		}
	}

	/**
	 * Checks to see if an observer exists
	 *
	 * @param string $strEvent
	 * @param object $observer
	 * @return boolean
	 */
	private function checkObserver($strEvent, $observer)
	{
		$observers = $this->events[$strEvent]->observers;

		for($i=0; $i < count($observers); $i++)
		{
			if($observers[$i] == $observer) return true;
		}

		return false;
	}

	/**
	 * Dispatch an event type
	 *
	 *
	 * @param object $Object
	 * @return void
	 * @example
	 *{{
	 * $eventObject = new stdClass;
	 * $eventObject->type = 'event_name';
	 * $eventObject->example = 'data'
	 *
	 * $object->dispatch($eventObject);
	 *}}
	 */
	public function dispatch($Object)
	{
		$this->dispatchQueue($Object);
	}

	/**
	 * Add an observer to an event type
	 *
	 * @param string $strEvent
	 * @param object $observer
	 * @return void
	 *
	 * @example
	 * {{
	 * $observer = new stdClass;
	 * $observer->click = Array($object, 'method name'); Wishing to set the callback to a method in a object
	 * $observer->mouseup = 'function_name'; Wishing to set the callback to a function
	 *
	 * $object->addObserver('click', $eventObject);
	 * $object->addObserver('mouseup', $eventObject);
	 * }}
	 */
	public function addObserver($strEvent, $observer)
	{
		$this->addEventType($strEvent);

		if(!$this->checkObserver($strEvent, $observer)) $this->events[$strEvent]->observers[] = $observer;
	}

	/**
	 * Remove an observer from a event type
	 *
	 * @param string $strEvent
	 * @param object $observer
	 * @return void
	 */
	public function removeObserver($strEvent, $observer)
	{
		$Event = $this->events[$strEvent];

		if($Event)
		{
			for($i=0; $i < count($Event->observers); $i++)
			{
				if($Event->observers[$i] == $observer)
				{
					unset($Event->observers[$i]);
				}
			}
		}
	}
}
?>