#!/usr/bin/php
<?php

include( __DIR__.'/config.php' );


function usage( $err=null ) {
  echo 'Usage: '.$_SERVER['argv'][0]." <ip> <port>\n";
  if( $err ) {
    echo 'Error: '.$err."!\n";
  }
  exit();
}

if( $_SERVER['argc'] != 3 ) {
  usage();
}


$ip = $_SERVER['argv'][1];
$ip = long2ip( ip2long($ip) );
if( $ip == 0 ) {
	usage( 'wrong ip' );
}

$port = (int)$_SERVER['argv'][2];
if( $port == 0 ) {
	usage( 'wrong port' );
}



$wtitle = createWindowTitle( $ip, $port );
$img = createImageName( $ip, $port );


//system( 'clear' );
$output = '';
$fp = @fsockopen( $ip, $port, $errno, $errstr, 3 );
echo "Trying to connect to ".$ip." on port ".$port."\n\n";

if( $fp )
{
	echo "Connection open ".$ip." ".$port."\n\n";
	fwrite( $fp, SEND_STRING );
    stream_set_timeout( $fp, DELAY_SOCKET );
    $output .= fread( $fp, RESULT_LENGTH );
    $info = stream_get_meta_data( $fp );
    fclose($fp);

    if( $info['timed_out'] ) {
        $output .= "\nConnection timed out!\n";
    }
    
	$t_result[$ip][$port] = $output;
    echo $output."\n";
}
else
{
	echo $errstr."\n";
}

usleep( DELAY_SHOT );
$shoot = createShotCommand( $wtitle, $img );
//echo $shoot."\n";
system( $shoot );

if( is_file($img) )
{
	$service = interp( $output );
	//echo $service."\n";
	$args = [ 'ip'=>$ip, 'port'=>$port, 'service'=>$service, 'img'=>createImageName($ip,$port,false), 'output'=>$output ];
	render( $args );
}

exit();

?>