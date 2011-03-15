<?php

global $valid_sensor;

if ($device['os'] == "areca")
{
  $oids = snmp_walk($device, "1.3.6.1.4.1.18928.1.1.2.14.1.2", "-Osqn", "");
  if ($debug) { echo($oids."\n"); }
  $oids = trim($oids);
  if ($oids) echo("Areca Harddisk ");
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $temperature_id = $split_oid[count($split_oid)-1];
      $temperature_oid  = "1.3.6.1.4.1.18928.1.1.2.14.1.2.$temperature_id";
      $temperature  = snmp_get($device, $temperature_oid, "-Oqv", "");
      $descr = "Hard disk $temperature_id";
      if ($temperature != -128) # -128 = not measured/present
      {
        discover_sensor($valid_sensor, 'temperature', $device, $temperature_oid, zeropad($temperature_id), 'areca', $descr, '1', '1', NULL, NULL, NULL, NULL, $temperature);
      }
    }
  }

  $oids = snmp_walk($device, "1.3.6.1.4.1.18928.1.2.2.1.10.1.2", "-OsqnU", "");
  if ($debug) { echo($oids."\n"); }
  if ($oids) echo("Areca Controller ");
  $precision = 1;
  $type = "areca";
  foreach (explode("\n", $oids) as $data)
  {
    $data = trim($data);
    if ($data)
    {
      list($oid,$descr) = explode(" ", $data,2);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      $oid  = "1.3.6.1.4.1.18928.1.2.2.1.10.1.3." . $index;
      $current = snmp_get($device, $oid, "-Oqv", "");

      discover_sensor($valid_sensor, 'temperature', $device, $oid, $index, 'areca', trim($descr,'"'), '1', '1', NULL, NULL, NULL, NULL, $current);
    }
  }
}

?>