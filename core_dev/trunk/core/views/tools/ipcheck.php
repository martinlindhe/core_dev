<?php
/**
 * show info of ip address
 */

//TODO: show all registered users associated with a IP
//TODO: allow to search for username and see all their used IP addresses
//TODO: ability to add IP ban from here

$session->requireSuperAdmin();

if (!$this->owner)
    $this->owner = 'default';

switch ($this->owner) {
case 'default':
    $ip = $session->ip; // _SERVER['REMOTE_ADDR'];
    echo 'Your current IP is '.$ip.'<br/>';

    echo '<form method="get" action="/t/ipcheck/ip">';
    echo 'Search IP: '.xhtmlInput('ip', $ip).'<br/><br/>';
    echo xhtmlSubmit('Search');
    echo '</form>';

    echo '<form method="get" action="/t/ipcheck/user">';
    echo 'Search by username: '.xhtmlInput('user', $session->username).'<br/><br/>';
    echo xhtmlSubmit('Search');
    echo '</form>';


    break;

case 'ip':
    // query ip info

    $ip = '';
    if (!empty($_GET['ip']))
        $ip = $_GET['ip'];

    if (!$ip)
        die('meh');

    $geoip = IPv4_to_GeoIP($ip);

    echo '<h1>'.$ip.' ('.gethostbyaddr($ip).')</h1>';
    echo '<br/><br/>';

/*
    $list = Users::byIP($geoip);

    echo 'This IP is associated with '.count($list).' registered users:<br/>';
    foreach ($list as $row) {
        echo Users::link($row['userId'], $row['userName']).'<br/>';
    }
*/
    echo '<hr/>';
    echo '<a href="http://www.dnsstuff.com/tools/whois.ch?ip='.$ip.'" target="_blank">Perform whois lookup</a><br/>';
    echo '<a href="http://www.dnsstuff.com/tools/tracert.ch?ip='.$ip.'" target="_blank">Perform traceroute</a><br/>';
    echo '<a href="http://www.dnsstuff.com/tools/ping.ch?ip='.$ip.'" target="_blank">Ping IP</a><br/>';
    echo '<a href="http://www.dnsstuff.com/tools/city.ch?ip='.$ip.'" target="_blank">Lookup city from IP</a><br/>';
    echo '<hr/>';

    //Admin notes
    echo CommentViewer::render(IP, $geoip);
    break;

case 'user':
    // query user name

    $user_name = 0;
    if (!empty($_GET['user']))
        $user_name = $_GET['user'];

    $user = User::getByName($user_name);
    if (!$user)
        die('no such user');

    echo '<h1>Query IP information of user '.$user->name.'</h1>';

    $ips = Users::getIPByUser($user->id);

    echo '<table>';
    echo '<tr>';
    echo '<th>IP</th>';
    echo '<th>Tid</th>';
    echo '<th>&nbsp;</th>';
    echo '</tr>';
    foreach ($ips as $ip) {
        echo '<tr>';
            echo '<td>'.GeoIP_to_IPv4($ip['IP']).'</td>';
            echo '<td>'.$ip['time'].'</td>';
            echo '<td><a href="'.$_SERVER['PHP_SELF'].'?block&ip='.GeoIP_to_IPv4($ip['IP']).'">Blockera</a></td>';
        echo '</tr>';
    }
    echo '</table>';
    break;

default:
    throw new Exception ('no such view: '.$this->owner);
}

?>
