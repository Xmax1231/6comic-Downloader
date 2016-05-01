<?php
$id=$_SERVER["argv"][1];
if (!isset($id)) {
	exit("no id");
} else if (!is_numeric($id)) {
	exit("id not a number");
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
		exit("ch not a number");
	}
}
echo "download id=".$id." ch=".$ch1."-".$ch2."\n";
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
		echo "ch ".$i." not found.\n";
		continue;
	}
	@mkdir("downloads/".$id."/".$i);
	$domain=substr($code, $startid+5, 1);
	$folder=substr($code, $startid+6, 1);
	preg_match("/(\d+)$/", substr($code, $startid+7, 3), $m);
	$page=$m[1];
	for ($j=1; $j <= $page; $j++) {
		echo "downloading ch=".$i." page=".$j."\n";
		$startid2=$startid+10+($j-1)%10*3+floor(($j-1)/10);
		$imghash=substr($code, $startid2, 3);
		$url="http://img".$domain.".6comic.com:99/".$folder."/".$id."/".$i."/".str_pad($j, 3, "0", STR_PAD_LEFT)."_".$imghash.".jpg";
		$img=file_get_contents($url);
		file_put_contents("downloads/".$id."/".$i."/".$j.".jpg", $img);
	}
}
?>
