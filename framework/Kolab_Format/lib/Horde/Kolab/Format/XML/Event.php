<?php
/**
 * Implementation for events in the Kolab XML format.
 *
 * PHP version 5
 *
 * @category Kolab
 * @package  Kolab_Format
 * @author   Thomas Jarosch <thomas.jarosch@intra2net.com>
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_Server
 */

/**
 * Kolab XML handler for event groupware objects.
 *
 * Copyright 2007-2009 Klarälvdalens Datakonsult AB
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html.
 *
 * @category Kolab
 * @package  Kolab_Format
 * @author   Thomas Jarosch <thomas.jarosch@intra2net.com>
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.fsf.org/copyleft/lgpl.html LGPL
 * @link     http://pear.horde.org/index.php?package=Kolab_Server
 * @since    Horde 3.2
 */
class Horde_Kolab_Format_XML_Event extends Horde_Kolab_Format_XML
{
    /**
     * Specific data fields for the contact object
     *
     * @var array
     */
    protected $_fields_specific;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_root_name = 'event';

        /** Specific event fields, in kolab format specification order
         */
        $this->_fields_specific = array(
            'summary' => array (
                'type'    => self::TYPE_STRING,
                'value'   => self::VALUE_MAYBE_MISSING,
            ),
            'location' => array (
                'type'    => self::TYPE_STRING,
                'value'   => self::VALUE_MAYBE_MISSING,
            ),
            'organizer' => $this->_fields_simple_person,
            'start-date' => array(
                'type'    => self::TYPE_STRING,
                'value'   => self::VALUE_NOT_EMPTY,
            ),
            'alarm' => array(
                'type'    => self::TYPE_INTEGER,
                'value'   => self::VALUE_MAYBE_MISSING,
            ),
            'recurrence' => array(
                'type'    => self::TYPE_COMPOSITE,
                'value'   => self::VALUE_CALCULATED,
                'load'    => 'Recurrence',
                'save'    => 'Recurrence',
            ),
            'attendee' => $this->_fields_attendee,
            'show-time-as' => array (
                'type'    => self::TYPE_STRING,
                'value'   => self::VALUE_MAYBE_MISSING,
            ),
            'color-label' => array (
                'type'    => self::TYPE_STRING,
                'value'   => self::VALUE_MAYBE_MISSING,
            ),
            'end-date' => array(
                'type'    => self::TYPE_STRING,
                'value'   => self::VALUE_NOT_EMPTY,
            ),
        );

        parent::__construct();
    }

    /**
     * Load event XML values and translate start/end date.
     *
     * @param array &$children An array of XML nodes.
     *
     * @return array Array with the object data.
     *
     * @throws Horde_Exception If parsing the XML data failed.
     */
    protected function _load(&$children)
    {
        $object = parent::_load($children);
        if (is_a($object, 'PEAR_Error')) {
            return $object;
        }

        // Translate start/end date including full day events
        if (strlen($object['start-date']) == 10) {
            $object['start-date'] = Horde_Kolab_Format_Date::decodeDate($object['start-date']);
            $object['end-date']   = Horde_Kolab_Format_Date::decodeDate($object['end-date']) + 24*60*60;
        } else {
            $object['start-date'] = Horde_Kolab_Format_Date::decodeDateTime($object['start-date']);
            $object['end-date']   = Horde_Kolab_Format_Date::decodeDateTime($object['end-date']);
        }

        return $object;
    }

    /**
     * Save event XML values and translate start/end date.
     *
     * @param array $root   The XML document root.
     * @param array $object The resulting data array.
     *
     * @return boolean True on success.
     *
     * @throws Horde_Exception If converting the data to XML failed.
     */
    function _save($root, $object)
    {
        // Translate start/end date including full day events
        if (!empty($object['_is_all_day'])) {
            $object['start-date'] = Horde_Kolab_Format_Date::encodeDate($object['start-date']);
            $object['end-date']   = Horde_Kolab_Format_Date::encodeDate($object['end-date'] - 24*60*60);
        } else {
            $object['start-date'] = Horde_Kolab_Format_Date::encodeDateTime($object['start-date']);
            $object['end-date']   = Horde_Kolab_Format_Date::encodeDateTime($object['end-date']);
        }

        return parent::_save($root, $object);
    }
}
