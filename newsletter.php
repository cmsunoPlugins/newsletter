<?php
session_start(); 
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
include('../../config.php');
include('lang/lang.php');
$busy = (isset($_POST['ubusy'])?preg_replace("/[^A-Za-z0-9-_]/",'',$_POST['ubusy']):'index');
// ********************* actions *************************************************************************
if(isset($_POST['action'])) {
	switch ($_POST['action']) {
		// ********************************************************************************************
		case 'plugin': ?>
		<link rel="stylesheet" type="text/css" media="screen" href="uno/plugins/newsletter/newsletter.css" />
		<div class="blocForm">
			<div id="newsletterC" class="bouton fr" onClick="f_config_newsletter();" title="<?php echo T_("Configure the plugin");?>"><?php echo T_("Config");?></div>
			<div id="newsletterL" class="bouton fr" onClick="f_list_newsletter();" title="<?php echo T_("Edit your mailing list");?>"><?php echo T_("Mailing List");?></div>
			<div id="newsletterW" class="bouton fr current" onClick="f_write_newsletter();" title="<?php echo T_("Write a newsletter");?>"><?php echo T_("Write");?></div>
			<h2><?php echo T_("Newsletter");?></h2>
			<div id="newsletterWrite">
				<div id="newsletterResult"></div>
				<p>
					<?php echo T_("This plugin allows you to send a newsletter to an email list.")." ";?>
					<?php echo T_("It works with the mail() function of your server or in SMTP with your GMAIL account or with another SMTP Provider account.");?>
				</p>
				<p>
					<?php echo T_("The shortcode");?>&nbsp;<code>[[newsletter]]</code>&nbsp;<?php echo T_("add a field to enter his email address and receive the newsletter.")." ";?>
					<?php echo T_("The newsletter contains in footer an automatic unsubscribe link.");?>
				</p>
				<p><?php echo T_("This plugin adds the excellent PHPMailer module which will then be used by other plugins (contact, users...).");?></p>
				<div class="blocForm">
					<div class="input" id="newsletterP">
						<p><?php echo T_("Subject");?></p>
						<input name="newsletterSu" id="newsletterSu" type="text" value="" />
						<p><?php echo T_("Content");?></p>
						<textarea name="newsletterCont" id="newsletterCont"></textarea>
					</div>
				</div>
				<div class="blocBouton">
					<div id="newsletterGroup" class="newsletterGroup fl"></div>
					<div id="newsletterSaveCont" class="bouton fr <?php if(!file_exists('../../data/_sdata-'.$sdata.'/newsletter.json')) echo 'danger'; ?>" onClick="f_save_newsletter();" title="<?php echo T_("Saves the contents");?>"><?php echo T_("Save");?></div>
					<div class="bouton fr" onClick="f_send_newsletter(0,'<?php echo T_("Sending");?>','<?php echo T_("finished.");?>');" title="<?php echo T_("Send only to Admin");?>"><?php echo T_("Send to test");?></div>
					<div class="bouton fr" onClick="f_send_newsletter(1,'<?php echo T_("Sending");?>','<?php echo T_("finished.");?>');" title="<?php echo T_("Send the newsletter");?>"><?php echo T_("Send");?></div>
				</div>
			</div>
			<div id="newsletterList" style="display:none;">
				<div>
					<p><?php echo T_("New recipient");?></p>
					<table>
						<tr><td>
							<input name="newsletterAdd" id="newsletterAdd" size="50" type="text" />
							<br />
							<input name="newsletterLGadd" id="newsletterLGadd" size="10" type="text" placeholder="<?php echo T_("Create group");?>" />
							<div id="newsletterLgroup" class="newsletterGroup fl" style="width:320px;"></div>
						</td><td><div style="height:4px;">&nbsp;</div>
							<div class="bouton" style="margin-left:25px;" onClick="f_add_newsletter();" title="<?php echo T_("Add a new recipient");?>"><?php echo T_("Add");?></div>
						</td></tr>
					</table>
					<div id="newsletterML">
						<table>
							<thead>
								<tr>
									<th><?php echo T_("Email");?></th>
									<th><?php echo T_("Group");?></th>
									<th colspan="2"><?php echo T_("Action");?></th>
								</tr>
							</thead>
							<tbody id="newsletterTlist"></tbody>
						</table>
					</div>
					<?php
					if(file_exists('../../data/_sdata-'.$sdata.'/users.json') && file_exists('../users/users.php')) {
						$q = file_get_contents('../../data/_sdata-'.$sdata.'/users.json');
						$a = json_decode($q,true);
						echo '<p>'.T_("The User group contains the members of the Users plugin :").'&nbsp;'.count($a['user']).'&nbsp;'.T_("members.").'</p>';
					} ?>
					
				</div>
			</div>
			<div id="newsletterConfig" style="display:none;">
				<table class="hForm">
					<tr>
						<td colspan=2 style="text-align:left;padding-left:30px;font-weight:700;"><?php echo T_("Sending method");?> :</td>
					</tr>
					<tr>
						<td><label><?php echo T_("Method");?></label></td>
						<td>
							<select name="newsletterMet" id="newsletterMet" onChange="f_trsmtp_newsletter(this)">
								<option value=""><?php echo T_("Default (PHP)");?></option>
								<option value="gmail"><?php echo T_("Gmail (SMTP)");?></option>
								<option value="smtp"><?php echo T_("Other SMTP");?></option>
							</select>
						</td>
						<td><em><?php echo T_("Newsletter is sent by using this method.");?></em></td>
					</tr>
					<tr class="trsmtp">
						<td><label><?php echo T_("Username");?></label></td>
						<td><input type="text" class="input" name="newsletterGmA" id="newsletterGmA" style="width:150px;" /></td>
						<td><em><?php echo T_("Email address for Gmail, username for other SMTP.");?></em></td>
					</tr>
					<tr class="trsmtp">
						<td><label><?php echo T_("Password");?></label></td>
						<td><input type="password" class="input" name="newsletterGmP" id="newsletterGmP" style="width:150px;" /></td>
						<td><em><?php echo T_("Account password for this username. (Registered encrypted)");?></em></td>
					</tr>
					<tr class="trsmt2">
						<td><label><?php echo T_("Host");?></label></td>
						<td><input type="password" class="input" name="newsletterGmH" id="newsletterGmH" style="width:150px;" /></td>
						<td><em><?php echo T_("URL of your SMTP Provider");?> (smtp.mailgun.org, smtp.mandrillapp.com...)</em></td>
					</tr>
				</table>
				<div id="newsletterSaveConf" class="bouton fr <?php if(!file_exists('../../data/_sdata-'.$sdata.'/newsletter.json')) echo 'danger'; ?>" onClick="f_saveConf_newsletter();" title="<?php echo T_("Save settings");?>"><?php echo T_("Save");?></div>
			</div>
			<div class="clear"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$b = 0;
		if(file_put_contents('../../data/newsletter.txt', $_POST['cont'])) $b=1;
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json')) {
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
		}
		else $a = array();
		if(!isset($a['met'])) $a['met'] = '';
		if(!isset($a['gma'])) $a['gma'] = '';
		if(!isset($a['gmp'])) $a['gmp'] = '';
		if(!isset($a['gmh'])) $a['gmh'] = '';
		if(empty($a['iv'])) $a['iv'] = base64_encode(openssl_random_pseudo_bytes(16));
		if(empty($a['group'])) $a['group'] = array('ext','man');
		$a['su'] = strip_tags($_POST['su']);
		$out = json_encode($a);
		if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out) && $b) echo T_('newsletter saved');
		else echo '!'.T_('Impossible backup');
		break;
		// ********************************************************************************************
		case 'saveConf':
		$b = 0;
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json')) {
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
		}
		else $a = array();
		$a['met'] = $_POST['met'];
		$a['gma'] = $_POST['gma'];
		$a['gmh'] = $_POST['gmh'];
		if(empty($a['iv'])) $a['iv'] = base64_encode(openssl_random_pseudo_bytes(16));
		if(empty($a['group'])) $a['group'] = array('ext','man');
		if($a['gma']) $a['gmp'] = base64_encode(openssl_encrypt(strip_tags($_POST['gmp']), 'AES-256-CBC', substr($Ukey,0,32), OPENSSL_RAW_DATA, base64_decode($a['iv'])));
		$out = json_encode($a);
		if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out)) echo T_('config saved');
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'load':
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json')) {
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json'); // liste des emails + options + sujet
			$a = json_decode($q,true);
			if(isset($a['list'][0]) && is_string($a['list'][0])) { // *** PATCH - ADD GROUP IF NOT EXISTS
				$c = array();
				foreach($a['list'] as $k=>$v) {
					$c[$v] = array('man','ext');
				}
				$a['list'] = $c;
				file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', json_encode($a));
			} // ***
			if(file_exists('../../data/'.$busy.'/site.json')) {
				$q = file_get_contents('../../data/'.$busy.'/site.json'); $b = json_decode($q,true); $a['tit'] = $b['tit']; $a['url'] = $b['url']; $a['nom'] = $b['nom'];
			}
			else exit;
			if(file_exists('../../data/_sdata-'.$sdata.'/ssite.json')) {
				$q = file_get_contents('../../data/_sdata-'.$sdata.'/ssite.json'); $b = json_decode($q,true); $a['mel'] = $b['mel'];
			}
			else exit;
			if(isset($a['gmp']) && $a['gmp']) {
				$a['gmp'] = openssl_decrypt(base64_decode($a['gmp']), 'AES-256-CBC', substr($Ukey,0,32), OPENSSL_RAW_DATA, base64_decode($a['iv']));
				$a['gmp'] = rtrim($a['gmp'], "\0");
			}
			$c = array('ext'=>T_("External"), 'man'=>T_("Manual"));
			if(isset($a['group'])) {
				foreach($a['group'] as $k=>$v) {
					if(isset($c[$v])) $a['group'][$k] = '|'.$c[$v];
				}
				if(file_exists('../../data/_sdata-'.$sdata.'/users.json') && file_exists('../users/users.php')) $a['group'][] = '|User';
			}
			if(isset($a['list'])) {
				foreach($a['list'] as $k=>$v) {
					foreach($v as $k1=>$v1) if(isset($c[$v1])) $a['list'][$k][$k1] = $c[$v1];
				}
			}
			if(!empty($_POST['send']) && file_exists('../../data/_sdata-'.$sdata.'/users.json') && file_exists('../users/users.php')) {
				$q = file_get_contents('../../data/_sdata-'.$sdata.'/users.json');
				$c = json_decode($q,true); 
				if(isset($c['user'])) {
					foreach($c['user'] as $k=>$v) {
						if(!empty($v['e']) && (!isset($a['unsub']) || !in_array($v['e'],$a['unsub']))) {
							if(!isset($a['list'][$v['e']])) $a['list'][$v['e']] = array('User');
							else $a['list'][$v['e']][] = 'User';
						}
					}
				}
			}
			$out = json_encode($a);
			echo $out;
		}
		else echo '';
		exit;
		break;
		// ********************************************************************************************
		case 'loadContent':
		if(file_exists('../../data/newsletter.txt')) {
			$q = file_get_contents('../../data/newsletter.txt'); // contenu
			echo stripslashes($q);
		}
		else echo '';
		exit;
		break;
		// ********************************************************************************************
		case 'add':
		$mel = strip_tags($_POST['add']);
		$ngr = strip_tags($_POST['ng']);
		$group = ((isset($_POST['group'])&&$j=json_decode(strip_tags($_POST['group'])))?$j:array());
		if($mel) {
			if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json')) {
				$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
				$a = json_decode($q,true);
			}
			if(!in_array('man',$group)) $group[] = 'man';
			if($ngr && !in_array($ngr,$group)) $group[] = $ngr;
			$a['list'][$mel] = $group; // Add/Update mail to list
			foreach($group as $r) {
				if($r && !in_array($r,$a['group']) && $r!='User') $a['group'][] = $r;
			}
			// Check group list
			$b = array();
			foreach($a['list'] as $gr) { // "list":{"lol@gmail.com":["man","blue"],
				foreach($gr as $v) if(!in_array($v,$b)) $b[] = $v;
			}
			foreach($a['group'] as $k=>$v) { // "group":["ext","man","blue,...]
				if($v!='user' && $v!='ext' && $v!='man' && !in_array($v,$b)) unset($a['group'][$k]);
			}
			// CLEAN UNSUB
			if(file_exists('../../data/_sdata-'.$sdata.'/users.json') && file_exists('../users/users.php') && isset($a['unsub'])) {
				$q = file_get_contents('../../data/_sdata-'.$sdata.'/users.json');
				$c = json_decode($q,true);
				$b = ',';
				foreach($c['user'] as $k=>$v) $b .= $v['e'].',';
				foreach($a['unsub'] as $k=>$v) {
					if(strpos($b, ','.$v.',')===false) unset($a['unsub'][$k]);
				}
			}
			$out = json_encode($a);
			if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out)) echo T_('email added');
			else echo '!'.T_('impossible add');
		}
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'del':
		$l = strip_tags($_POST['del']);
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json') && $l) {
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
			if(isset($a['list'][$l])) unset($a['list'][$l]);
			else {
				echo '!'.T_('Error');
				break;
			}
			// Check group exists
			$b = array();
			foreach($a['list'] as $k=>$v) {
				foreach($v as $r) if(!in_array($r,$b)) $b[] = $r;
			}
			foreach($a['group'] as $k=>$v) {
				if($v!='user' && $v!='ext' && $v!='man' && !in_array($v,$b)) unset($a['group'][$k]);
			}
			$out = json_encode($a);
			if(file_put_contents('../../data/_sdata-'.$sdata.'/newsletter.json', $out)) echo T_('email deleted');
			else echo '!'.T_('undeletable');
		}
		else echo '!'.T_('No data');
		break;
		// ********************************************************************************************
		case 'send':
		$dest = strip_tags($_POST['dest']);
		$url = strip_tags($_POST['url']);
		$tit = strip_tags($_POST['tit']);
		$nom = strip_tags($_POST['nom']);
		$sujet = strip_tags(stripslashes($_POST['su']));
		$cont = stripslashes($_POST['cont']);
		$mel = strip_tags($_POST['mel']);
		$met = strip_tags($_POST['met']);
		$gma = strip_tags($_POST['gma']);
		$gmp = strip_tags($_POST['gmp']);
		$gmh = strip_tags($_POST['gmh']);
		if(file_exists('../../data/_sdata-'.$sdata.'/newsletter.json')) {
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/newsletter.json');
			$a = json_decode($q,true);
		}
		else {
			echo '!'.T_('Error');
			break;
		}
		include '../../template/mailTemplate.php';
		$r = base64_encode(openssl_encrypt(strip_tags($_POST['dest']), 'AES-256-CBC', substr($Ukey,0,32), OPENSSL_RAW_DATA, base64_decode($a['iv'])));
		$ul = $url."/uno/plugins/newsletter/newsletterSubscribe.php?c=".urlencode($r)."&m=".urlencode($dest)."&a=del&b=".urlencode($url.'/'.$nom.'.html');
		$supp = "<a href='".$ul."'>".T_("Unsubscribe")."</a>";
		$bottom = str_replace('[[unsubscribe]]',$supp, $bottom); // template
		$msgT = strip_tags($cont).' -------- '.T_("Unsubscribe").' : '.$ul;
		$msgH = $top . $cont . $bottom;
		$fm = preg_replace("/[^a-zA-Z ]+/", "", $tit);
		// PHPMailer
		require 'PHPMailer/PHPMailerAutoload.php';
		$phm = new PHPMailer();
		$phm->CharSet = "UTF-8";
		$phm->setFrom($mel, $fm);
		$phm->addReplyTo($mel, $fm);
		$phm->AddAddress($dest);
		$phm->isHTML(true);
		$phm->Subject = $sujet;
		$phm->Body = $msgH;		
		$phm->AltBody = $msgT;
		if(empty($met)) { // PHP mail()
			if($phm->Send()) echo '<div style="color:green;">'.$dest.' : OK</div>';
			else echo '<div style="color:red;">'.$dest.' : '.T_("Failure").' : '.$phm->ErrorInfo.'</div>';
		}
		else { // SMTP
			$phm->IsSMTP();
			$phm->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
			$phm->SMTPAuth = true;  // authentication enabled
			$phm->SMTPSecure = 'tls';
			$phm->Port = 587; 
			$phm->Host = ($met=='gmail'?'smtp.gmail.com':$gmh); // 'smtp.gmail.com'...
			$phm->Username = $gma;  
			$phm->Password = utf8_encode($gmp);
			if($phm->Send()) echo '<div style="color:green;">'.$dest.' : OK</div> --- ';
			else echo '<div style="color:red;">'.$dest.' : '.T_("Failure").' : '.$phm->ErrorInfo.'</div> --- ';
			sleep(1.1);
		}
		break;
		// ********************************************************************************************
	}
	clearstatcache();
	exit;
}
?>
