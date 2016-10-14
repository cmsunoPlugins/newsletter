<?php
include('../../config.php');
include('lang/lang.php');
// ********************* actions *************************************************************************
if (isset($_GET['a'])&&isset($_GET['c'])&&isset($_GET['m'])&&isset($_GET['b']))
	{
	switch ($_GET['a'])
		{
		case 'add':
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json') && filter_var(strip_tags($_GET['m']),FILTER_VALIDATE_EMAIL))
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
			if(!isset($a['list']) || array_search(strip_tags($_GET['m']),$a['list'])===false)
				{
				$a['list'][] = strip_tags($_GET['m']); // ajout du mail a la liste
				$b = array();
				foreach($a['list'] as $r) $b[] = $r;
				$a['list'] = $b;
				$out = json_encode($a);
				if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out))
					{
					echo "<script language='JavaScript'>setTimeout(function(){document.location.href='".strip_tags($_GET['b'])."';},2000);</script>";
					echo "<html><head><meta charset='utf-8'></head><body><h3 style='text-align:center;margin-top:50px;'>".T_('email added')."</h3></body></html>";
					break;
					}
				}
			}
		echo "<script language='JavaScript'>document.location.href='".strip_tags($_GET['b'])."';</script>";
		break;
		// ********************************************************************************************
		case 'del':
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json'))
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
			$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
			$c = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, substr(strip_tags($a['pass']),0,30), base64_decode($_GET['c']), MCRYPT_MODE_ECB, $iv);
			$c = rtrim($c, "\0");
			if(($c==$_GET['m']) && ($k=array_search(strip_tags($_GET['m']),$a['list']))!==false)
				{
				unset($a['list'][$k]);
				$b = array();
				foreach($a['list'] as $r) $b[] = $r;
				$a['list'] = $b;
				$out = json_encode($a);
				if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out))
					{
					echo "<script language='JavaScript'>setTimeout(function(){document.location.href='".strip_tags($_GET['b'])."';},2000);</script>";
					echo "<html><head><meta charset='utf-8'></head><body><h3 style='text-align:center;margin-top:50px;'>".T_('email deleted')."</h3></body></html>";
					break;
					}
				}
			}
		echo "<script language='JavaScript'>document.location.href='".strip_tags($_GET['b'])."';</script>";
		break;
		// ********************************************************************************************
		case 'new':
		include '../../template/mailTemplate.php';
		$bottom= str_replace('[[unsubscribe]]',"", $bottom); // template
		if(isset($_GET['u']) && file_exists('../../data/'.strip_tags($_GET['u']).'/site.json'))
			{
			$q = file_get_contents('../../data/'.strip_tags($_GET['u']).'/site.json'); $a = json_decode($q,true);
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json'); $b = json_decode($q,true);
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/ssite.json'); $c = json_decode($q,true);
			$key = $b['pass'];
			$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
			$r = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, substr($key,0,30), strip_tags($_GET['m']), MCRYPT_MODE_ECB, $iv));
			$rn = "\r\n";
			$ul = $a['url']."/uno/plugins/newsletter/newsletterSubscribe.php?c=".urlencode($r)."&m=".urlencode(strip_tags($_GET['m']))."&a=add&b=".urlencode($a['url'].'/'.$a['nom'].'.html');
			$supp = "<div style='color:#999;font-size:11px;text-align:center;'><a href='".$ul."'>".T_("Unsubscribe")."</a></div>";
			$boundary = "-----=".md5(rand());
			$body = T_("Confirm registration by clicking this link").": <a href='".$ul."'>".$ul."</a>";
			$msgT = strip_tags($body);
			$msgH = $top . $body . $bottom;
			$sujet = $a['tit'].' - '. T_("Subscribe Newsletter");
			$fm = preg_replace("/[^a-zA-Z ]+/", "", $a['tit']);
			$header  = "From: ".$fm."<".$c['mel'].">".$rn."Reply-To:".$fm."<".$c['mel'].">";
			$header.= "MIME-Version: 1.0".$rn;
			$header.= "Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
			$msg= $rn."--".$boundary.$rn;
			$msg.= "Content-Type: text/plain; charset=\"utf-8\"".$rn;
			$msg.= "Content-Transfer-Encoding: 8bit".$rn;
			$msg.= $rn.$msgT.$rn;
			$msg.= $rn."--".$boundary.$rn;
			$msg.= "Content-Type: text/html; charset=\"utf-8\"".$rn;
			$msg.= "Content-Transfer-Encoding: 8bit".$rn;
			$msg.= $rn.$msgH.$rn;
			$msg.= $rn."--".$boundary."--".$rn;
			$msg.= $rn."--".$boundary."--".$rn;
			if(mail(strip_tags($_GET['m']), stripslashes($sujet), stripslashes($msg),$header))
				{
				echo "<script language='JavaScript'>setTimeout(function(){document.location.href='".$a['url'].'/'.$a['nom'].'.html'."';},2000);</script>";
				echo "<html><head><meta charset='utf-8'></head><body><h2 style='text-align:center;margin-top:50px;'>".T_("You will receive an email to confirm")."...</h2></body></html>";
				}
			else
				{
				echo "<script language='JavaScript'>setTimeout(function(){document.location.href='".$a['url'].'/'.$a['nom'].'.html'."';},2000);</script>";
				echo "<html><head><meta charset='utf-8'></head><body><h2 style='text-align:center;margin-top:50px;'>".T_("Error")."...</h2></body></html>";
				}
			}
		break;
		}
	clearstatcache();
	exit;
	}
?>
