#!/usr/bin/perl
print "Content-type:text/html\r\n\r\n";
use Net::Telnet;
use Data::Dumper;

$ENV{'QUERY_STRING'}=~/host=(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/;
my $host=$1;
my $sortby='bssid';
$ENV{'QUERY_STRING'}=~/host=(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})&ajax=(0|1)&sortby=(\w+)&filter=(.*)&frequency=(.*)/;
my $ajax=$2;
if($3){$sortby=$3;}
my $filter=$4;
my $frequency=$5;
my @lines=();
my $t = new Net::Telnet (Timeout => 10,Errmode=>"return",Prompt => '/# $/');
if($t->open($host)){
	if($host eq "192.168.30.220"){ #TP Link Ac1750
		my $lockFile="/var/www/html/validator-web/cgi-bin/file.lock";
		my @lines_aux = ();
		$t->cmd(STRING => 'ifconfig wlan0 up',timeout=>2);
		@lines_aux=$t->cmd(STRING => 'iwlist wlan0 scan',timeout=>5);#5GHz
		$t->cmd(STRING => 'ifconfig wlan1 up',timeout=>2);
		push(@lines_aux,$t->cmd(STRING => 'iwlist wlan1 scan',timeout=>5));#2.4GHz
		$t->close;	
		my $var="";
		my $i=0;		
		foreach (@lines_aux){#cada equipo va en un índice del array
			if ($_ =~ /Cell [0-9][0-9] -/){
				@lines[$i]=$var;
				 $i++;
				 $var="";
			}
			$var.=$_;
		}
		@lines[$i]=$var; #añadimos el último elemento
	}
	$t->close;
}
if (!$ajax){
	print "
	<html>
	<head><title>WIFI MONITOR</title></head>
	<script>
	function update()
	{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
	xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
	}
	xmlhttp.onreadystatechange=function()
	{
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
	//alert(xmlhttp.responseText);
	if (xmlhttp.responseText!=''){
	document.getElementById('wifis').innerHTML=xmlhttp.responseText;
	}
	setTimeout('update()',1000);
	}
	}
	var sortby = 'bssid';
	var radios = document.getElementsByName('sortby');
	for (var i = 0, length = radios.length; i < length; i++) {
	if (radios[i].checked) {
                var sortby=radios[i].value;
        	break;
        	}
        }
        xmlhttp.open('GET','wifi-survey.pl?host=$host&ajax=1&sortby='+sortby+'&filter='+document.getElementById('filter').value+'&frequency='+document.getElementById('frequency').value,true);
	xmlhttp.send();
	}
	window.onload=function(){
	setTimeout('update()',1000);
	}
	</script>
	<body>
	<b>FILTER SSID: </b><input id='filter' type='text'/>
	<b>FREQUENCY:</b>
	<select id='frequency'>
	<option value='2.4GHz' selected>2.4GHz</option>
	<option value='5GHz'>5GHz</option>
	</select>
	<div id='wifis'>
	";
}
my $comando=0;
my %wifis;
my ($ssid,$channel,$rssi,$wifi_n);
my $wifi_n=0;
foreach my $line (@lines) {
	if ($line=~ /Cell\s([0-9]+)\s-\sAddress:\s([A-F0-9\:]+)\s+Channel:([0-9]+)\s+Frequency:([0-9.]+)\sGHz.*\s+Quality=([0-9]+)\/([0-9]+)\s+Signal\slevel=([0-9-]+)\sdBm\s+Encryption\skey:(on|off)\s+ESSID:\"(.*)\"/){
		$wifi_n++;
		$wifis{$wifi_n}{bssid}=$2;
		$wifis{$wifi_n}{essid}=$9;
		$wifis{$wifi_n}{channel}=$3;
		$wifis{$wifi_n}{frequency}=$4;
		$wifis{$wifi_n}{rssi}=$7;
		$wifis{$wifi_n}{quality}=sprintf("%.2f",$5/$6*100);	
		if ($8 eq "on"){		
			if ($line=~ /IE:\sIEEE\s802.11i\/([A-z0-9]+).*\s+Group\sCipher\s:\s(.*)\s+.*\s+Authentication\sSuites.*:\s(.*)/){$wifis{$wifi_n}{security}="$1 $2 $3";}
			else{$wifis{$wifi_n}{security}= "on";}
		}else{
			$wifis{$wifi_n}{security}="none";
		}
		if(!$comando){ #el comando tuvo exito, empezamos la tabla
			$comando=1;
			my ($checkedESSID, $checkedCHANNEL, $checkedRSSI, $checkedQUALITY, $checkedSECURITY, $checkedBSSID, $checkedFREQUENCY)='';
			if($sortby eq "essid"){$checkedESSID='checked';}
			elsif($sortby eq "channel"){$checkedCHANNEL='checked';}
			elsif($sortby eq "rssi"){$checkedRSSI='checked';}
			elsif($sortby eq "quality"){$checkedQUALITY='checked';}
			elsif($sortby eq "security"){$checkedSECURITY='checked';}
			elsif($sortby eq "bssid"){$checkedBSSID='checked';}
			elsif($sortby eq "frequency"){$checkedFREQUENCY='checked';}
			print "
			<table>
			<tr align=left>
			<th width=\"20%\"><input type=radio name='sortby' value='essid' $checkedESSID>ESSID</th>
			<th width=\"10%\"><input type=radio name='sortby' value='channel' $checkedCHANNEL>CHANNEL</th>
			<th width=\"10%\"><input type=radio name='sortby' value='rssi' $checkedRSSI>RSSI</th>
			<th width=\"10%\"><input type=radio name='sortby' value='quality' $checkedQUALITY>QUALITY</th>
			<th width=\"20%\"><input type=radio name='sortby' value='security' $checkedSECURITY>SECURITY</th>
			<th width=\"20%\"><input type=radio name='sortby' value='bssid' $checkedBSSID>BSSID</th>
			<th width=\"10%\"><input type=radio name='sortby' value='frequency' $checkedFREQUENCY>FREQUENCY</th>
			</tr>
			";
		}
	}
}
my $wifis_n = keys %wifis;
for (my $i=1;$i<$wifis_n;$i++){
	my $minimum=$i;
	for(my $j=$i+1;$j<$wifis_n+1;$j++){
		if($wifis{$j}{$sortby} =~ /^[+-]?\d+\.?\d*$/){
			if($wifis{$j}{$sortby} > $wifis{$minimum}{$sortby}){
			$minimum=$j;
			}
		}else{
			if(lc($wifis{$j}{$sortby}) lt lc($wifis{$minimum}{$sortby})){
				$minimum=$j;
			}
		}
	}
	my %aux=%{$wifis{$minimum}};
	%{$wifis{$minimum}}= %{$wifis{$i}};
	%{$wifis{$i}}=%aux;
}
foreach my $wifi_sorted (sort keys %wifis){
	my %wifi= %{$wifis{$wifi_sorted}};
	if(($filter eq "")||($wifi{'essid'}=~/$filter/)){
		if (int($wifi{'frequency'}) eq int($frequency)){
			print "<tr>";
			print "<td>".$wifi{'essid'}."</td>";
			print "<td>".$wifi{'channel'}."</td><td>".$wifi{'rssi'}." dBm</td><td>".$wifi{'quality'}."\%</td><td>".$wifi{'security'}."</td><td>".$wifi{'bssid'}."</td>";
			print "<td>".$wifi{'frequency'}."</td>";
			print "</tr>";
		}
	}
}
if($comando){ print "</table>"};
if (!$ajax){
	print "</div>
	</body>
	</html>";
}

1;
