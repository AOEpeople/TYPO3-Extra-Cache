<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 AOE media GmbH <dev@aoemedia.de>
*  All rights reserved
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Dispatcher to register an post events.
 *
 * An eventdispatcher dispatches events (that can be thrown on any place) to many registered targetobjects (the EventHandlers).
 * the targetobjects need to be regsistered in the Dispatcher for certain events.
 * 
 * @package extracache
 * @subpackage System_Event
 */
class Tx_Extracache_System_Event_Dispatcher implements t3lib_Singleton {
	/**
	 * All registered event handler
	 * @var array
	 */
	private $handlers = array();

	/**
	 * Prevent cloning
	 */
	protected function __clone() {}

	/**
	 * Register a Handler
	 *
	 * @param	string		$eventName Event Name
	 * @param	Object		$handlerObject Object thats registered as handler for this event
	 * @param	string		$handlerObjectMethod
	 * @return	void
	 */
	public function addHandler($eventName, $handlerObject, $handlerObjectMethod) {
		$handler = array(
			'object' => $handlerObject,
			'method' => $handlerObjectMethod,
		);
		if(!isset($this->handlers[$eventName])) {
			$this->handlers[$eventName] = array();
		}
		$this->handlers[$eventName][] = $handler;
	}

	/**
	 * Register a Handler that Lazy Loads - only singletons are supported!
	 *
	 * @param	string		$eventName Event Name
	 * @param	string		$handlerObjectName Object thats registered as handler for this event
	 * @param	string		$handlerObjectMethod
	 * @return	void
	 */
	public function addLazyLoadingHandler($eventName, $handlerObjectName, $handlerObjectMethod) {
		$handler = array(
			'classname' => $handlerObjectName,
			'method' => $handlerObjectMethod,
		);
		if(!isset($this->handlers[$eventName])) {
			$this->handlers[$eventName] = array();
		}
		$this->handlers[$eventName][] = $handler;
	}

	/**
	 * trigger a event. The dispatcher will call all registered handlers for this event. But stops dispatching if the event is canceled
	 *
	 * @param	mixed		$eventOrEventName Event Name (string) or tx_mvc_system_event_event Object
	 * @param 	Object		$contextObject	The Object where the event is triggered (If first parameter is a string this is required to build correct event instance)
	 * @param 	array		$infoData additional informations for the event (If first parameter is a string this is required to build correct event instance)
	 * @return	tx_mvc_system_event_event
	 */
	public function triggerEvent($eventOrEventName, $contextObject=null, $infoData=array()) {
		if (!$eventOrEventName instanceof Tx_Extracache_System_Event_Events_Event) {
			if (is_string($eventOrEventName)) {
				$event = t3lib_div::makeInstance('Tx_Extracache_System_Event_Events_Event', $eventOrEventName, $contextObject, $infoData);
			} else {
				throw new InvalidArgumentException("no supported type as $eventOrEventName given");
			}
		} else {
			$event = $eventOrEventName;
		}

		$eventName = $event->getName();

		if (!isset($this->handlers[$eventName])) {
			return $event;
		}
		$i=0;
		foreach ($this->handlers[$eventName] as $handlerData) {
			if (!isset($handlerData['object']) && isset ($handlerData['classname'])) {
				$this->handlers[$eventName][$i]['object'] = $handlerData['object'] = t3lib_div::makeInstance( $handlerData['classname'] );
			}
			call_user_func_array(
				array($handlerData['object'], $handlerData['method']),
				array($event)
			);
			if ($event->isCanceled()) {
				break;
			}
			$i++;
		}

		return $event;
	}

	/**
	 * reset array with handlers
	 */
	public function resetHandlers() {
		$this->handlers = array();
	}
}