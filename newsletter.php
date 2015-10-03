<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
include('../../config.php');
include('lang/lang.php');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'plugin': ?>
		<link rel="stylesheet" type="text/css" media="screen" href="uno/plugins/newsletter/newsletter.css" />
		<div class="blocForm">
			<div id="newsletterC" class="bouton fr" onClick="f_config_newsletter();" title="<?php echo _("Configure the plugin");?>"><?php echo _("Config");?></div>
			<div id="newsletterL" class="bouton fr" onClick="f_list_newsletter();" title="<?php echo _("Edit your mailing list");?>"><?php echo _("Mailing List");?></div>
			<div id="newsletterW" class="bouton fr current" onClick="f_write_newsletter();" title="<?php echo _("Write a newsletter");?>"><?php echo _("Write");?></div>
			<h2><?php echo _("Newsletter");?></h2>
			<div id="newsletterWrite">
				<div id="newsletterResult"></div>
				<p>
					<?php echo _("This plugin allows you to send a newsletter to an email list.")." ";?>
					<?php echo _("It works with the mail() function of your server or in SMTP with your GMAIL account.");?>
				</p>
				<p>
					<?php echo _("The shortcode");?>&nbsp;<code>[[newsletter]]</code>&nbsp;<?php echo _("add a field to enter his email address and receive the newsletter.")." ";?>
					<?php echo _("The newsletter contains in footer an automatic unsubscribe link.");?>
				</p>
				<div class="blocForm">
					<div class="input" id="newsletterP">
						<p><?php echo _("Subject");?></p>
						<input name="newsletterSu" id="newsletterSu" size="50" type="text" value="" />
						<p><?php echo _("Content");?></p>
						<textarea name="newsletterCont" id="newsletterCont"></textarea>
					</div>
				</div>
				<div class="blocBouton">
					<div class="bouton fr <?php if(!file_exists('../../data/_sdata-'.$sdata.'/newsletter.json')) echo 'danger'; ?>" onClick="f_save_newsletter();" title="<?php echo _("Saves the contents");?>"><?php echo _("Save");?></div>
					<div class="bouton fr" onClick="f_send_newsletter(0,'<?php echo _("Sending");?>','<?php echo _("finished.");?>');" title="<?php echo _("Send only to test");?>"><?php echo _("Send to test");?></div>
					<div class="bouton fr" onClick="f_send_newsletter(1,'<?php echo _("Sending");?>','<?php echo _("finished.");?>');" title="<?php echo _("Send the newsletter");?>"><?php echo _("Send");?></div>
				</div>
			</div>
			<div id="newsletterList" style="display:none;">
				<div>
					<p><?php echo _("New recipient");?></p>
					<input name="newsletterAdd" id="newsletterAdd" size="50" type="text" />
					<div class="bouton fr" onClick="f_add_newsletter();" title="<?php echo _("Add a new recipient");?>"><?php echo _("Add");?></div>
					<div id="newsletterML"></div>
				</div>
			</div>
			<div id="newsletterConfig" style="display:none;">
				<table class="hForm">
					<tr>
						<td colspan=2 style="text-align:left;padding-left:30px;font-weight:700;"><?php echo _("Sending method");?> :</td>
					</tr>
					<tr>
						<td><label><?php echo _("PHP");?></label></td>
						<td><input type="radio" name="newsletterMet" value="php" checked></td>
						<td><em><?php echo _("Newsletter is sent by using the features of your server.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Gmail");?></label></td>
						<td><input type="radio" name="newsletterMet" value="gm"></td>
						<td><em><?php echo _("Newsletter is sent with your Gmail account.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Gmail account");?></label></td>
						<td><input type="text" class="input" name="newsletterGmA" id="newsletterGmA" style="width:150px;" /></td>
						<td><em><?php echo _("Gmail address. Only needed for Gmail procedure.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Gmail password");?></label></td>
						<td><input type="password" class="input" name="newsletterGmP" id="newsletterGmP" style="width:150px;" /></td>
						<td><em><?php echo _("Only needed for Gmail procedure. (Registered encrypted)");?></em></td>
					</tr>
					<tr>
						<td colspan=2 style="text-align:left;padding-left:30px;font-weight:700;"><?php echo _("Control key");?> :</td>
					</tr>
					<tr>
						<td><label><?php echo _("Passphrase");?></label></td>
						<td><input type="text" class="input" name="newsletterPhrase" id="newsletterPhrase" style="width:250px;" /></td>
						<td><em><?php echo _("Sequence of words. Used to check the authenticity of a removal request.");?></em></td>
					</tr>
				</table>
				<div class="bouton fr <?php if(!file_exists('../../data/_sdata-'.$sdata.'/newsletter.json')) echo 'danger'; ?>" onClick="f_saveConf_newsletter();" title="<?php echo _("Save settings");?>"><?php echo _("Save");?></div>
			</div>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$b = 0;
		if (file_put_contents('../../data/newsletter.txt', $_POST['cont'])) $b=1;
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json'))
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
			}
		else $a = array();
		if(!isset($a['met'])) $a['met']='';
		if(!isset($a['gma'])) $a['gma']='';
		if(!isset($a['gmp'])) $a['gmp']='';
		if(!isset($a['pass'])) $a['pass']='';
		$a['su'] = $_POST['su'];
		$out = json_encode($a);
		if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out) && $b) echo _('newsletter saved');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'saveConf':
		$b = 0;
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json'))
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
			}
		else $a = array();
		$a['met'] = $_POST['met'];
		$a['gma'] = $_POST['gma'];
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$a['gmp'] = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sdata, strip_tags($_POST['gmp']), MCRYPT_MODE_ECB, $iv));
		$a['pass'] = $_POST['pass'];
		$out = json_encode($a);
		if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out)) echo _('config saved');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'load':
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json'))
			{
			$q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $Ubusy = $a['nom'];
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json'); // liste des emails + options + sujet
			$a = json_decode($q,true);
			if(file_exists('../../data/'.$Ubusy.'/site.json'))
				{
				$q = file_get_contents('../../data/'.$Ubusy.'/site.json'); $b = json_decode($q,true); $a['tit'] = $b['tit']; $a['url'] = $b['url']; $a['nom'] = $b['nom'];
				}
			else exit;
			if(file_exists('../../data/_sdata-'.$sdata.'/ssite.json'))
				{
				$q = file_get_contents('../../data/_sdata-'.$sdata.'/ssite.json'); $b = json_decode($q,true); $a['mel'] = $b['mel'];
				}
			else if(isset($a['gma']) && $a['gma']!='') $a['mel'] = $a['gma'];
			else exit;
			if(isset($a['gmp']) && $a['gmp'])
				{
				$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
				$a['gmp'] = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sdata, strip_tags(base64_decode($a['gmp'])), MCRYPT_MODE_ECB, $iv);
				$a['gmp'] =rtrim($a['gmp'], "\0");
				}
			$out = json_encode($a);
			echo $out;
			}
		else echo '';
		exit;
		break;
		// ********************************************************************************************
		case 'loadContent':
		if (file_exists('../../data/newsletter.txt'))
			{
			$q = file_get_contents('../../data/newsletter.txt'); // contenu
			echo stripslashes($q);
			}
		else echo '';
		exit;
		break;
		// ********************************************************************************************
		case 'add':
		$l = $_POST['add'];
		if($l)
			{
			if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json'))
				{
				$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
				$a = json_decode($q,true);
				}
			if(!isset($a['list']) || array_search($l,$a['list'])===false) $a['list'][] = $l; // ajout du mail a la liste
			else 
				{
				echo '!'._('already in the list');
				break;
				}
			$b = array();
			foreach($a['list'] as $r) $b[] = $r;
			$a['list'] = $b;
			$out = json_encode($a);
			if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out)) echo _('email added');
			else echo '!'._('impossible add');
			}
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'del':
		$l = $_POST['del'];
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json') && $l)
			{
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
			if(($k=array_search($l,$a['list']))!==false) unset($a['list'][$k]);
			else 
				{
				echo '!'._('Error');
				break;
				}
			$b = array();
			foreach($a['list'] as $r) $b[] = $r;
			$a['list'] = $b;
			$out = json_encode($a);
			if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out)) echo _('email deleted');
			else echo '!'._('undeletable');
			}
		else echo '!'._('No data');
		break;
		// ********************************************************************************************
		case 'send':
		include '../../template/mailTemplate.php';
		$key = strip_tags($_POST['pass']);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$r = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, substr($key,0,30), strip_tags($_POST['dest']), MCRYPT_MODE_ECB, $iv));
		$rn = "\r\n";
		$ul = strip_tags($_POST['url'])."/uno/plugins/newsletter/newsletterSubscribe.php?c=".urlencode($r)."&m=".urlencode(strip_tags($_POST['dest']))."&a=del&b=".urlencode(strip_tags($_POST['url'].'/'.$_POST['nom'].'.html'));
		$supp = "<a href='".$ul."'>"._("Unsubscribe")."</a>";
		$bottom= str_replace('[[unsubscribe]]',$supp, $bottom); // template
		$boundary = "-----=".md5(rand());
		$msgT = strip_tags($_POST['cont']);
		$msgH = $top . $_POST['cont'] . $bottom;
		$sujet = stripslashes($_POST['su']);
		$fm = preg_replace("/[^a-zA-Z ]+/", "", $_POST['tit']);
		$header  = "From: ".$fm."<".$_POST['mel'].">".$rn."Reply-To:".$fm."<".$_POST['mel'].">";
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
echo '<span style="color:green;">'.$_POST['dest'].' : OK</span> --- '; exit;
		if($_POST['met']!='gm') // PHP mail()
			{
			if(mail($_POST['dest'], stripslashes($sujet), $msg,$header)) echo '<span style="color:green;">'.$_POST['dest'].' : OK</span> --- ';
			else echo '<span style="color:red;">'.$_POST['dest'].' : '._("Failure").'</span> --- ';
			}
		else // Gmail
			{
			require realpath(dirname(__FILE__)).'/PHPMailer/class.phpmailer.php';
			$smtp = new PHPMailer();
			$smtp->IsSMTP(); // enable SMTP
			$smtp->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
			$smtp->SMTPAuth = true;  // authentication enabled
			$smtp->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
			$smtp->Host = 'smtp.gmail.com';
			$smtp->Port = 465; 
			$smtp->Username = $_POST['gma'];  
			$smtp->Password = utf8_encode($_POST['gmp']);
			$smtp->SetFrom($_POST['mel'], $fm);
			$smtp->Subject = $sujet;
			$smtp->IsHTML(true);
			$smtp->Body = utf8_decode(stripslashes($msgH));
			$smtp->AltBody = $msgT;
			$smtp->AddAddress($_POST['dest']);
			if(!$smtp->Send()) echo '<span style="color:red;">'.$_POST['dest'].' : '._("Failure").'</span> --- ';
			else echo '<span style="color:green;">'.$_POST['dest'].' : OK</span> --- ';
			sleep(1.5);
			}
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>
