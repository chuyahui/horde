<?php
/**
 * Skeleton AJAX application API.
 *
 * This file defines the AJAX actions provided by this module. The primary AJAX
 * endpoint is represented by horde/services/ajax.php but that handler will call
 * the module specific actions via the class defined here.
 *
 * Copyright 2012 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 *
 * @package Skeleton
 */
class Skeleton_Ajax_Application extends Horde_Core_Ajax_Application
{
    /**
     * Store an element.
     *
     * @return NULL
     */
    public function store()
    {
        $driver = $this->_getDriver();
        $driver->store($this->_vars->data);
    }

    /**
     * Return all items.
     *
     * @return array Returns a list of all foos.
     */
    public function listElements()
    {
        $driver = $this->_getDriver();
        $driver->retrieve();
        return $driver->listFoos();
    }

    /**
     * Fetch the driver instance.
     *
     * @return Sms_Driver The driver.
     */
    private function _getDriver()
    {
        return $GLOBALS['injector']->getInstance('Sms_Driver');
    }
}
