<?php

session_start();

require_once 'priv/errorhandler.php';
require_once 'priv/pdo.php';
require_once 'priv/twig.php';

$sql = '
SELECT *
FROM qr_events
WHERE display_date > CURRENT_DATE LIMIT 1
';
$event =  DB::prepare($sql)->texecute()->fetch();
$model['event'] = $event;

if($event)
{
	$sql = '
	SELECT 
		SUM(1) AS score,
		qr_users.name,
		qr_players.alive,
		qr_users.class
	FROM qr_players
	JOIN qr_kills ON qr_kills.killer = qr_players.qr_users_id
	JOIN qr_users ON qr_players.qr_users_id = qr_users.id
	WHERE qr_players.qr_events_id = ?
	GROUP BY qr_kills.killer
	ORDER BY score DESC, alive DESC
	LIMIT 10
	';
	$users = DB::prepare($sql)->texecute([$event['id']])->fetchAll();
	$model['users'] = $users;
}

if(isset($_GET['screen']))
{
	echo $twig->render('scoreboard.screen.html', $model);
	die();
}
echo $twig->render('scoreboard.html', $model);
