<?php
$id=$_SERVER["argv"][1];
if (!isset($id)) {
	exit("No ID");
} else if (!is_numeric($id)) {
	exit("ID not a number");
} else {
	echo "Download ID ".$id."\n";
}
$t=file_get_contents("http://www.6comic.com/comic/readmanga_".$id.".html?ch=1-1");
$t=iconv("BIG5", "UTF-8", $t);
preg_match("/var chs=(\d+)/",$t, $m);
$ch1=1;
$ch2=$m[1];
if (isset($_SERVER["argv"][2])) {
	if (preg_match("/^(\d+)-(\d+)$/", $_SERVER["argv"][2], $m)) {
		$ch1=$m[1];
		$ch2=$m[2];
	} else if (is_numeric($_SERVER["argv"][2])) {
		$ch1=$ch2=$_SERVER["argv"][2];
	} else if (!is_numeric($id)) {
		exit("Chapter not a number");
	}
	echo "Download Chapter ".$ch1."-".$ch2."\n";
} else {
	echo "Download All Chapters\n";
}
$pg1=1;
$pg2=PHP_INT_MAX;
if (isset($_SERVER["argv"][3])) {
	if (preg_match("/^(\d+)-(\d+)$/", $_SERVER["argv"][3], $m)) {
		$pg1=$m[1];
		$pg2=$m[2];
	} else if (is_numeric($_SERVER["argv"][3])) {
		$pg1=$pg2=$_SERVER["argv"][3];
	} else if (!is_numeric($id)) {
		exit("Page not a number");
	}
	echo "Download Page ".$pg1."-".$pg2."\n";
} else {
	echo "Download All Pages\n";
}
preg_match("/var cs='(.+?)';/", $t, $m);
$code=$m[1];
$hash=substr($code, 0, 3);
@mkdir("downloads");
@mkdir("downloads/".$id);
for ($i=$ch1; $i <= $ch2; $i++) {
	$prestr=$hash.$i;
	$prestr=substr($prestr, strlen($prestr)-4, 4);
	$startid=strpos($code, $prestr);
	if ($startid === false) {
		echo "Chapter ".$i." not found.\n";
		continue;
	}
	@mkdir("downloads/".$id."/".$i);
	$domain=substr($code, $startid+5, 1);
	$folder=substr($code, $startid+6, 1);
	preg_match("/(\d+)$/", substr($code, $startid+7, 3), $m);
	$page=$m[1];
	echo "Download Chapter ".$i." Page ".max($pg1,1)."-".min($pg2,$page)."\n";
	for ($j=max($pg1,1); $j <= min($pg2,$page); $j++) {
		echo "Downloading Chapter ".$i." Page ".$j."\n";
		$k=($j-1)%100+1;
		$startid2=$startid+10+($k-1)%10*3+floor(($k-1)/10);
		$imghash=substr($code, $startid2, 3);
		$url="http://img".$domain.".6comic.com:99/".$folder."/".$id."/".$i."/".str_pad($j, 3, "0", STR_PAD_LEFT)."_".$imghash.".jpg";
		$img=file_get_contents($url);
		file_put_contents("downloads/".$id."/".$i."/".$j.".jpg", $img);
	}
}
?>
