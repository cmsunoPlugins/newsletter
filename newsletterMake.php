<?php
if(!isset($_SESSION['cmsuno'])) exit();
?>
<?php
if(file_exists('data/newsletter.txt'))
	{
	include('plugins/newsletter/lang/lang.php');
	$a1 = '<form class="newsletterFrm" method="GET" action="uno/plugins/newsletter/newsletterSubscribe.php">'."\r\n".
		'<input type="hidden" name="a" value="new" />'."\r\n".
		'<input type="hidden" name="b" value="1" />'."\r\n".
		'<input type="hidden" name="c" value="1" />'."\r\n".
		'<input type="hidden" name="u" value="'.$Ubusy.'" />'."\r\n".
		'<label>'.T_("Email address").' : </label>'."\r\n".
		'<input type="text" name="m" value="" />'."\r\n".
		'<input style="margin-left:10px;" type="submit" value="'.T_("Subscribe").'" />'."\r\n".
		'</form><div style="font-size:90%">'.T_("You will receive an email to confirm").'</div>'."\r\n";
	$Uhtml = str_replace('[[newsletter]]',"\r\n".$a1, $Uhtml); // template
	$Ucontent = str_replace('[[newsletter]]',"\r\n".$a1, $Ucontent); // editor
	}
?>
