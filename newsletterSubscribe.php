<?php
include('../../config.php');
include('lang/lang.php');
// ********************* actions *************************************************************************
if (isset($_GET['a'])&&isset($_GET['c'])&&isset($_GET['m'])&&isset($_GET['b']))
	{
	$mail = strip_tags($_GET['m']);
	$url = strip_tags($_GET['b']);
	switch($_GET['a'])
		{
		case 'add':
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json') && filter_var($mail,FILTER_VALIDATE_EMAIL))
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
			if(!isset($a['list'][$mail]))
				{
				$a['list'][$mail] = array('ext'); // ajout du mail a la liste
				$a['unsub'] = (isset($a['unsub'])?array_diff($a['unsub'], $mail):array());
				$out = json_encode($a);
				if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out))
					{
					newsletterOutput($url, T_('email added'));
					break;
					}
				}
			}
		echo "<script language='JavaScript'>document.location.href='".$url."';</script>";
		break;
		// ********************************************************************************************
		case 'del':
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json'))
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
			$c = openssl_decrypt(base64_decode($_GET['c']), 'AES-256-CBC', substr($Ukey,0,32), OPENSSL_RAW_DATA, base64_decode($a['iv']));
			$c = rtrim($c, "\0");
			if(($c==$mail))
				{
				$b = 0;
				if(isset($a['list'][$mail]))
					{
					unset($a['list'][$mail]);
					$b = 1;
					}
				else if(isset($a['unsub']) && !in_array($mail,$a['unsub']))
					{
					$a['unsub'][] = $mail;
					$b = 1;
					}
				if($b)
					{
					$out = json_encode($a);
					if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out))
						{
						newsletterOutput($url, T_('email deleted'));
						break;
						}
					}
				}
			}
		echo "<script language='JavaScript'>document.location.href='".$url."';</script>";
		break;
		// ********************************************************************************************
		case 'new':
		if(isset($_GET['u']) && file_exists('../../data/'.strip_tags($_GET['u']).'/site.json'))
			{
			include '../../template/mailTemplate.php';
			$bottom = str_replace('[[unsubscribe]]',"", $bottom); // template
			$q = file_get_contents('../../data/'.strip_tags($_GET['u']).'/site.json'); $a = json_decode($q,true);
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json'); $b = json_decode($q,true);
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/ssite.json'); $c = json_decode($q,true);
			$r = base64_encode(openssl_encrypt($mail, 'AES-256-CBC', substr($Ukey,0,32), OPENSSL_RAW_DATA, base64_decode($b['iv'])));
			$rn = "\r\n";
			$ul = $a['url']."/uno/plugins/newsletter/newsletterSubscribe.php?c=".urlencode($r)."&m=".urlencode($mail)."&a=add&b=".urlencode($a['url'].'/'.$a['nom'].'.html');
			$supp = "<div style='color:#999;font-size:11px;text-align:center;'><a href='".$ul."'>".T_("Unsubscribe")."</a></div>";
			$body = T_("Confirm registration by clicking this link").": <a href='".$ul."'>".$ul."</a>";
			$msgT = strip_tags($body).' -------- '.T_("Unsubscribe").' : '.$ul;
			$msgH = $top . $body . $bottom;
			$sujet = $a['tit'].' - '. T_("Subscribe Newsletter");
			$fm = preg_replace("/[^a-zA-Z ]+/", "", $a['tit']);
			// PHPMailer
			require 'PHPMailer/PHPMailerAutoload.php';
			$phm = new PHPMailer();
			$phm->CharSet = "UTF-8";
			$phm->setFrom($c['mel'], $fm);
			$phm->addReplyTo($c['mel'], $fm);
			$phm->AddAddress($mail);
			$phm->isHTML(true);
			$phm->Subject = $sujet;
			$phm->Body = $msgH;		
			$phm->AltBody = $msgT;
			if($phm->Send()) newsletterOutput($a['url'].'/'.$a['nom'].'.html', T_("You will receive an email to confirm").'...');
			else newsletterOutput($a['url'].'/'.$a['nom'].'.html', T_("Error").'...');
			}
		break;
		}
	clearstatcache();
	exit;
	}
//
function newsletterOutput($url,$content)
	{ ?>
	
	<script language="JavaScript">setTimeout(function(){document.location.href='<?php echo $url; ?>';},2000);</script>
	<html>
	<head>
	<meta charset="utf-8">
	</head>
	<body>
		<h2 style="text-align:center;margin-top:50px;"><?php echo $content; ?></h2>
	</body>
	</html>
	<?php }
?>
