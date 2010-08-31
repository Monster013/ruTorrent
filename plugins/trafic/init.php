<?php
eval(getPluginConf('trafic'));
require_once( '../plugins/trafic/ratios.php' );

$st = getSettingsPath();
@mkdir($st.'/trafic');
@mkdir($st.'/trafic/trackers');
@mkdir($st.'/trafic/torrents');

$tm = getdate();
$startAt = mktime($tm["hours"],
	((integer)($tm["minutes"]/$updateInterval))*$updateInterval+$updateInterval-1,
	0,$tm["mon"],$tm["mday"],$tm["year"])-$tm[0];
if($startAt<0)
	$startAt = 0;
$interval = $updateInterval*60;
$req = new rXMLRPCRequest( new rXMLRPCCommand("schedule", 
	array( "trafic".getUser(), $startAt."", $interval."", 
		getCmd('execute').'={sh,-c,'.escapeshellarg(getPHP()).' '.escapeshellarg($rootPath.'/plugins/trafic/update.php').' '.escapeshellarg(getUser()).' & exit 0}' ) ) );
if($req->run() && !$req->fault)
       	$theSettings->registerPlugin("trafic");
else
       	$jResult .= "plugin.disable(); log('trafic: '+theUILang.pluginCantStart);";
$jResult .= "plugin.collectStatForTorrents = ".($collectStatForTorrents ? "true;" : "false;");
$jResult .= "plugin.updateInterval = ".$updateInterval.";";
$jResult .= getRatiosStat();

?>