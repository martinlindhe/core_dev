<?php
/**
 * show info of ip address
 */

//TODO: ability to add IP ban from here

namespace cd;

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

    $list = LoginEntry::getUsersByIP($ip);

    echo 'This IP is associated with '.count($list).' registered users:<br/>';
    foreach ($list as $user_ip)
        echo UserLink::render($user_ip).'<br/>';

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

    $ips = LoginEntry::getIPsByUser($user->id);

    echo '<table>';
    echo '<tr>';
    echo '<th>IP</th>';
    echo '</tr>';
    foreach ($ips as $ip) {
        echo '<tr>';
            echo '<td>'.$ip.'</td>';
        echo '</tr>';
    }
    echo '</table>';
    break;

default:
    throw new \Exception ('no such view: '.$this->owner);
}

?>
