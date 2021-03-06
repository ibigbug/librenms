<?php
/**
 * Dlinkap.php
 *
 * -Description-
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    LibreNMS
 * @link       http://librenms.org
 * @copyright  2018 Tony Murray
 * @author     Tony Murray <murraytony@gmail.com>
 */

namespace LibreNMS\OS;

use App\Models\Device;
use LibreNMS\Device\Processor;
use LibreNMS\Interfaces\Discovery\ProcessorDiscovery;
use LibreNMS\OS;

class Dlinkap extends OS implements ProcessorDiscovery
{
    public function discoverOS(Device $device): void
    {
        $firmware_oid = $device->sysObjectID . '.5.1.1.0';
        $hardware_oid = $device->sysObjectID . '.5.1.5.0';

        $device->version = snmp_get($device, $firmware_oid, '-Oqv') ?: null;
        $device->hardware = trim($device->sysDescr . ' ' . snmp_get($device, $hardware_oid, '-Oqv'));
    }

    /**
     * Discover processors.
     * Returns an array of LibreNMS\Device\Processor objects that have been discovered
     *
     * @return array Processors
     */
    public function discoverProcessors()
    {
        return [
            Processor::discover(
                'dlinkap-cpu',
                $this->getDeviceId(),
                $this->getDeviceArray()['sysObjectID'] . '.5.1.3.0',  // different OID for each model
                0,
                'Processor',
                100
            )
        ];
    }
}
