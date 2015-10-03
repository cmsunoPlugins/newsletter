//
// CMSUno
// Plugin Newsletter
//
function f_save_newsletter(){
	document.getElementById('newsletterResult').style.display="none";
	jQuery('#newsletterResult').empty();
	jQuery.post('uno/plugins/newsletter/newsletter.php',{
		'action':'save','unox':Unox,
		'su':document.getElementById('newsletterSu').value,
		'cont':CKEDITOR.instances['newsletterCont'].getData()
		},function(r){f_alert(r);}
	);
}
//
function f_saveConf_newsletter(){
	document.getElementById('newsletterResult').style.display="none";
	jQuery('#newsletterResult').empty();
	if(document.getElementsByName('newsletterMet')[0].checked)a=document.getElementsByName('newsletterMet')[0].value;
	else a=document.getElementsByName('newsletterMet')[1].value;
	jQuery.post('uno/plugins/newsletter/newsletter.php',{
		'action':'saveConf','unox':Unox,
		'met':a,
		'gma':document.getElementById('newsletterGmA').value,
		'gmp':document.getElementById('newsletterGmP').value,
		'pass':document.getElementById('newsletterPhrase').value
		},function(r){f_alert(r);}
	);
}
//
function f_load_newsletter(){
	jQuery(document).ready(function(){
		jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'load','unox':Unox},function(r){r=JSON.parse(r);
			if(r.su)document.getElementById('newsletterSu').value=r.su;
			if(r.met=='gm')document.getElementsByName('newsletterMet')[1].checked=true;
			else document.getElementsByName('newsletterMet')[0].checked=true;
			if(r.gma)document.getElementById('newsletterGmA').value=r.gma;
			if(r.gmp)document.getElementById('newsletterGmP').value=r.gmp;
			if(r.pass)document.getElementById('newsletterPhrase').value=r.pass;
			if(r.list){
				t=document.createElement('table');
				jQuery.each(r.list,function(k,v){
					tr=document.createElement('tr');
					td=document.createElement('td');td.innerHTML=v;
					tr.appendChild(td);
					td=document.createElement('td');td.onclick=function(){f_del_newsletter(v)};td.innerHTML='X';
					tr.appendChild(td);
					t.appendChild(tr);
				});
				jQuery('#newsletterML').empty();
				document.getElementById('newsletterML').appendChild(t);
			}
			jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'loadContent','unox':Unox},function(r){CKEDITOR.instances['newsletterCont'].setData(r);});
		});
	});
}
//
function f_add_newsletter(){
	l=document.getElementById('newsletterAdd').value;
	jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'add','unox':Unox,'add':l},function(r){f_alert(r);f_load_newsletter();});
}
//
function f_del_newsletter(l){jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'del','unox':Unox,'del':l},function(r){f_alert(r);f_load_newsletter();});}
//
function f_send_newsletter(f,start,stop){
	var a=new Array();
	window.scrollTo(0,0);jQuery("#wait").show();
	document.getElementById('newsletterResult').style.display="block";
	jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'load','unox':Unox},function(r){
		a=JSON.parse(r);jQuery('#newsletterResult').empty();jQuery('#newsletterResult').append(start+'...<br />');
		jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'loadContent'},function(r){
			if(f)jQuery.each(a.list,function(k,v){
				h={'action':'send','unox':Unox,'tit':a.tit,'mel':a.mel,'dest':v,'su':a.su,'cont':r,'url':a.url,'nom':a.nom,'met':a.met,'gma':a.gma,'gmp':a.gmp,'pass':a.pass};
				jQuery.ajax({type:'POST',url:'uno/plugins/newsletter/newsletter.php',data:h,async:false}).done(function(r1){jQuery('#newsletterResult').append(r1);});
			});
			h={'action':'send','unox':Unox,'tit':a.tit,'mel':a.mel,'dest':a.mel,'su':a.su,'cont':r,'url':a.url,'nom':a.nom,'met':a.met,'gma':a.gma,'gmp':a.gmp,'pass':a.pass};
			jQuery.ajax({type:'POST',url:'uno/plugins/newsletter/newsletter.php',data:h,async:false}).done(function(r1){jQuery('#newsletterResult').append(r1);});
		jQuery('#newsletterResult').append('<br />...'+stop);jQuery("#wait").hide();
		});
	});
}
//
function f_write_newsletter(){
	document.getElementById('newsletterConfig').style.display="none";
	document.getElementById('newsletterWrite').style.display="block";
	document.getElementById('newsletterList').style.display="none";
	document.getElementById('newsletterC').className="bouton fr";
	document.getElementById('newsletterW').className="bouton fr current";
	document.getElementById('newsletterL').className="bouton fr";
	document.getElementById('newsletterResult').style.display="none";
	jQuery('#newsletterResult').empty();
}
//
function f_list_newsletter(){
	document.getElementById('newsletterConfig').style.display="none";
	document.getElementById('newsletterWrite').style.display="none";
	document.getElementById('newsletterList').style.display="block";
	document.getElementById('newsletterC').className="bouton fr";
	document.getElementById('newsletterW').className="bouton fr";
	document.getElementById('newsletterL').className="bouton fr current";
	document.getElementById('newsletterResult').style.display="none";
	jQuery('#newsletterResult').empty();
}
//
function f_config_newsletter(){
	document.getElementById('newsletterConfig').style.display="block";
	document.getElementById('newsletterWrite').style.display="none";
	document.getElementById('newsletterList').style.display="none";
	document.getElementById('newsletterC').className="bouton fr current";
	document.getElementById('newsletterW').className="bouton fr";
	document.getElementById('newsletterL').className="bouton fr";
	document.getElementById('newsletterResult').style.display="none";
	jQuery('#newsletterResult').empty();
}
//
CKEDITOR.replace('newsletterCont',{height:'300'});
f_load_newsletter();