<?php
require 'Docsismodem.php';

$pdo = new PDO(
    'mysql:host=localhost;dbname=dhcp',
    'user',
    'password'
);

if (array_key_exists("macaddr", $_GET)) {
    $mac = $_GET['macaddr'];
    $mac = str_replace(":", "", $mac);
    $mac = str_replace(".", "", $mac);
} else {
    $mac = "";
}

// This get's me the last ip commited to the cable modem
// I use it to know where the CM lives
// Also one can use reverse DNS if your networking is using such a thing
// check PEAR packages for DNS Queries
$sql = "SELECT macaddr, lastip, serialnum FROM docsismodem WHERE macaddr = ? ";
$pdo->beginTransaction();
$sth = $pdo->prepare($sql);
$sth->execute(array($mac));

$a = null;
foreach ( $sth->fetchAll(PDO::FETCH_CLASS) as $modem ) {
  $dm = new DM_Model_Docsismodem();
  $dm->ipaddr = $modem->lastip;
  $dm->macaddr = $modem->macaddr;
  $a = $dm->remoteQuery();
}

?>
<html>
<head>
    <title>TX Information page, <?php print $modem->macaddr ?></title>
    <LINK href="bluedream.css" rel="stylesheet" type="text/css">
</head>
<body>
<table>
<tr>
    <td><b>mac</b></td>
    <td><?php print $modem->macaddr ?> , IP: <?php print $a['ip'] ?></td>
</tr>
<tr>
    <td><b>tx</b></td>
    <td><?php print ($a['tx']/10) ?></td>
</tr>
<tr>
    <td><b>snr</b></td>
    <td><?php print ($a['snr']/10) ?></td>
</tr>
<tr>
    <td><b>rx</b></td>
    <td><?php print ($a['rx']/10) ?></td>
</tr>
<tr>
    <td><b>firmware version</b></td>
    <td><?php print $a['version'] ?></td>
</tr>
<tr>
    <td>
    <form action="" method="get" name="p">
        <input type="text" name="macaddr" value="<?php print $mac ?>" />
        <input type="submit" value="Ver" />
    </form>
    </td>
</tr>
</table>
</body>
</html>
