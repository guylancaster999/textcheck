<!DOCTYPE html>
<html lang="en-US">
<head>
	<title>Text Analysis</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="main.css">
 </head>
<body> 
<?php
if(count($_POST)>0)
{
	$pl		=$_POST["pl"];
	$order	=$_POST["order"];
	$atleast=$_POST["atleast"];
	$filename	=$_FILES["file"]["tmp_name"];
}
else
{
	$order="a";
	$pl=1;
	$atleast=1;
	$filename="";
}

?>

<h1>Text analysis</h1>
<form action="textcheck.php" method="post" enctype="multipart/form-data">
	<table class="tbl">
		<tr>
			<td>File (txt format)</td>
			<td>
				<input required  type="file" id="file" name="file" value="<? print $filename; ?>">
			</td>
		</tr>
		<tr>
			<td>Phrase length</td>
			<td>
				<select id="pl" name="pl">
				<?	for($i=1;$i<=8;$i++)
				{
					print '<option value="'.$i.'" '. ($pl==$i?" selected ":"").'>'.$i.'</option>';
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Order by</td>
			<td>
				Frequency <input type="radio" id="order" name="order" value="f" "<?php print ($order=="f"?" checked ":"");?>"> &nbsp;
				Alphabet  <input type="radio" id="order" name="order" value="a" "<?php print ($order=="a"?" checked ":"");?>"> 
			</td>
		</tr>
		<tr>
		<td>Frequency at least</td>
			<td> 
				<select  id="atleast" name="atleast" >
				<?	
				for($i=1;$i<=8;$i++)
				{
					print '<option value="'.$i.'" '. ($atleast==$i?" selected ":"").'>'.$i.'</option>';  
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td> &nbsp;</td>
			<td>
				<input type="submit" value="Go">
			</td>
		</tr>
	</table>
</form>
<?php
if (strlen($_FILES["file"]["name"])>0)
{ 
	set_time_limit(640);
	// heading------------------------------------------------------------------------------
	$pl		=$_POST["pl"];
	$order	=$_POST["order"];
	$atleast=$_POST["atleast"];
 	//load data----------------------------------------------------------------------------
	$filename	=$_FILES["file"]["tmp_name"];
	$data		="";
	$hdl		=fopen($filename,"r");	
	while (!feof($hdl))$data .= fread($hdl, 1024);
	fclose($hdl);
	//clean data------------------------------------------------------------------------------
	$data	=strtolower($data);
	$newdata="";
	$p		=" ";
	$dataCh =strlen($data);

	for($i=0;$i<$dataCh;$i++)
	{
		$c	= substr($data,$i,1);
		if ($c<"a" || $c>"z") $c=" " ;
		if ($p!=" " || $c!=" ")$newdata.=$c;
		$p = $c;
	}
	$data="";
	$cleanDataCh=strlen($newdata); 
	//split into words------------------------------------------------------------------------
	$a		= explode(" ",$newdata);
	$newdata="";
	$a1		= array();
	$phrases= array("","","","","","","","","","","","");
	$ptr	= $ctr = 0;
	foreach ($a as $v)
	{
		$v = trim($v);
		if (strlen($v)>0)
		{ 
			 $ptr++;
			 if ($ptr>5)	$ptr=0;
			 $phrases[$ptr]=$v;
			 $ctr++;
			 if($ctr>=$pl)
			 {
				$ptr0	=  $ptr;
				$p		= " ";
				for ($i=1;$i<=$pl;$i++)
				{
					$p = $phrases[$ptr0]." ".$p;
					$ptr0--;
					if($ptr0<0)$ptr0=5;	
				}
				$a1[]=trim($p);
			 }
		 }
	}
	//count phrases-------------------------------------------------------------------------------
	$phraseCtr=count($a1);
	asort($a1);
	$v0		= "";
	$vct	= 0;
	$b		= array();
	$c		= array();
	foreach ($a1 as $v)
	{
		if ($v!=$v0)
		{
			$b[]=$v0;
			$c[]=$vct;
			$vct=0;
		}
		$vct++;
		$v0=$v;
	}
	$a1	="";
	$b[]=$v0;
	$c[]=$vct;
	$difPhraseCtr=count($b);
	print '<br/>
	<table class="tbl">
				<tr class="tbl"><td >1.File</td><td>'.$_FILES["file"]["name"].'</td></tr>
				<tr><td>2.Data</td><td >'.$dataCh.' characters</td></tr>
				<tr><td>3.Clean Data</td><td  >'.$cleanDataCh.' characters</td></tr>
				<tr><td>4.Phrases</td><td >'.$phraseCtr.'</td></tr>
				<tr><td>5.Different Phrases&nbsp;</td><td  >'.$difPhraseCtr.'</td></tr>
			</table><br/>'; 
	// display--------------------------------------------------------------------------------------
	$ctr = 0;
	print '<table class="tbl">
	<tr><td>#</td><td>Phrase</td></tr>';
			
	switch($order)
	{
		case "a":
		asort($b);
		 	foreach($b as $k=>$v)if ($c[$k]>=$atleast)
			{
				$ctr++;
				print '<tr><td >'.$v.'</td><td  >'.$c[$k].'</td></tr>';
			}
			break;	
		default: 
			asort($c);
			foreach($c as $k=>$v)if ($v>=$atleast)
			{
				$ctr++;
				print '<tr><td  >'.$v.'</td><td  >'.$b[$k].'</td></tr>';
			}
		}	 
		print '</table><br/>';
	print $ctr. " found.";
	$b=$c="";
 }

?>
</body>
</html>