<?php

namespace cd;

if (!($temp instanceof TempStoreRedis))
    return;

$config = $temp->getServerConfig();
//d($config);

$info = $temp->getServerInfo();
//d($info);
echo 'Redis '.$info['redis_version'].', '.$info['redis_mode'].' mode<br/>';
echo 'at <b>'.$config['bind'].':'.$config['port'].'</b><br/>';
echo 'Uptime <b>'.elapsed_seconds($info['uptime_in_seconds']).'</b><br/>';
echo 'Connected clients: <b>'.$info['connected_clients'].'</b><br/>';
echo 'Used memory: <b>'.byte_count($info['used_memory']).'</b><br/>';
