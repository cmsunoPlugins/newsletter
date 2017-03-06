//
// CMSUno
// Plugin Newsletter
//
function f_load_newsletter(){
	jQuery(document).ready(function(){
		jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'load','unox':Unox},function(r){
			var r=JSON.parse(r),t,to,tr,td,ch;
			if(r.su)document.getElementById('newsletterSu').value=r.su;
			if(r.met){
				if(r.met=='gmail')jQuery('.trsmt2').hide();
				t=document.getElementById("newsletterMet");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==r.met){to[v].selected=true;v=to.length;}}
			}
			else{
				jQuery('.trsmtp').hide();
				jQuery('.trsmt2').hide();
			}
			if(r.gma)document.getElementById('newsletterGmA').value=r.gma;
			if(r.gmp)document.getElementById('newsletterGmP').value=r.gmp;
			if(r.gmh)document.getElementById('newsletterGmH').value=r.gmh;
			if(r.list){
				t=document.getElementById("newsletterTlist");
				t.innerHTML='';
				jQuery.each(r.list,function(k,v){
					tr=document.createElement('tr');
					td=document.createElement('td');td.innerHTML=k;
					tr.appendChild(td);
					td=document.createElement('td');ch='';
					jQuery.each(v,function(k1,v1){ch+=v1+',';});
					td.innerHTML=ch.substr(0,ch.length-1);
					tr.appendChild(td);
					td=document.createElement('td');td.onclick=function(){f_del_newsletter(k)};td.innerHTML='X';
					tr.appendChild(td);
					t.appendChild(tr);
				});
			}
			if(r.group){
				t=document.getElementById('newsletterGroup');
				ch=document.getElementById('newsletterLgroup');
				t.innerHTML='';ch.innerHTML='';
				jQuery.each(r.group,function(k,v){
					tr=document.createElement('span');
					td=document.createElement('label');
					td.innerHTML=(v.substr(0,1)=='|'?v.substr(1):v);
					tr.appendChild(td);
					td=document.createElement('input');
					td.name=(v.substr(0,1)=='|'?v.substr(1):v);
					td.type='checkbox';
					tr.appendChild(td);
					t.appendChild(tr);
					if(v.substr(0,1)!='|'){
						tr=document.createElement('span');
						td=document.createElement('label');
						td.innerHTML=v;
						tr.appendChild(td);
						td=document.createElement('input');
						td.name=v;
						td.type='checkbox';
						if(v=='base')td.checked=true;
						tr.appendChild(td);
						ch.appendChild(tr);
					}
				});
			}
			jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'loadContent','unox':Unox},function(r){
				CKEDITOR.instances['newsletterCont'].setData(r);
			});
		});
	});
}
function f_save_newsletter(){
	document.getElementById('newsletterResult').style.display="none";
	jQuery('#newsletterResult').empty();
	var h=CKEDITOR.instances['newsletterCont'].getData();
	h=h.replace(/(\r\n|\n|\r)/gm,"");
	jQuery.post('uno/plugins/newsletter/newsletter.php',{
		'action':'save','unox':Unox,
		'su':document.getElementById('newsletterSu').value,
		'cont':h
		},function(r){
			f_alert(r);
			if(r.substr(0,1)!='!'){
				document.getElementById('newsletterSaveConf').className='bouton fr';
				document.getElementById('newsletterSaveCont').className='bouton fr';
			}
		}
	);
}
function f_saveConf_newsletter(){
	document.getElementById('newsletterResult').style.display="none";
	jQuery('#newsletterResult').empty();
	var a=document.getElementById('newsletterMet');
	jQuery.post('uno/plugins/newsletter/newsletter.php',{
		'action':'saveConf','unox':Unox,
		'met':a.options[a.selectedIndex].value,
		'gma':document.getElementById('newsletterGmA').value,
		'gmp':document.getElementById('newsletterGmP').value,
		'gmh':document.getElementById('newsletterGmH').value
		},function(r){
			f_alert(r);
			if(r.substr(0,1)!='!')document.getElementById('newsletterSaveConf').className='bouton fr';
		}
	);
}
function f_add_newsletter(){
	var l=document.getElementById('newsletterAdd').value,g=document.getElementById('newsletterLGadd').value,h=[];
	jQuery("#newsletterLgroup input:checked").each(function(){
		h.push(jQuery(this).attr('name'));
	});
	jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'add','unox':Unox,'add':l,'ng':g,'group':h},function(r){
		f_alert(r);
		f_load_newsletter();
	});
}
function f_del_newsletter(l){
	jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'del','unox':Unox,'del':l},function(r){
		f_alert(r);
		f_load_newsletter();
	});
}
function f_send_newsletter(f,start,stop){
	var a=new Array(),b,h,g=[];
	window.scrollTo(0,0);jQuery("#wait").show();
	document.getElementById('newsletterResult').style.display="block";
	jQuery('#newsletterGroup input:checked').each(function(){
		g.push(jQuery(this).attr('name'));
	});
	jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'load','unox':Unox,'send':1},function(r){
		a=JSON.parse(r);jQuery('#newsletterResult').empty();
		jQuery('#newsletterResult').append(start+'...<br />');
		jQuery.post('uno/plugins/newsletter/newsletter.php',{'action':'loadContent','unox':Unox},function(r){
			if(f)jQuery.each(a.list,function(k,v){
				b=v.filter(function(n){return g.indexOf(n)!==-1;});
				if(b!=''){
					h={'action':'send','unox':Unox,'tit':a.tit,'mel':a.mel,'dest':k,'su':a.su,'cont':r,'url':a.url,'nom':a.nom,'met':a.met,'gma':a.gma,'gmp':a.gmp,'gmh':a.gmh};
					jQuery.ajax({type:'POST',url:'uno/plugins/newsletter/newsletter.php',data:h,async:false}).done(function(r1){
						jQuery('#newsletterResult').append(r1);
					});
				}
			});
			h={'action':'send','unox':Unox,'tit':a.tit,'mel':a.mel,'dest':a.mel,'su':a.su,'cont':r,'url':a.url,'nom':a.nom,'met':a.met,'gma':a.gma,'gmp':a.gmp,'gmh':a.gmh};
			jQuery.ajax({type:'POST',url:'uno/plugins/newsletter/newsletter.php',data:h,async:false}).done(function(r1){
				jQuery('#newsletterResult').append(r1);
			});
		jQuery('#newsletterResult').append('<br />...'+stop);jQuery("#wait").hide();
		});
	});
}
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
function f_trsmtp_newsletter(f){
	if(f.options[f.selectedIndex].value==''){
		jQuery('.trsmtp').hide();
		jQuery('.trsmt2').hide();
	}
	else if(f.options[f.selectedIndex].value=='gmail'){
		jQuery('.trsmtp').show();
		jQuery('.trsmt2').hide();
		}
	else{
		jQuery('.trsmtp').show();
		jQuery('.trsmt2').show();
	}
}
//
CKEDITOR.replace('newsletterCont',{
	height:'300',
	on:{instanceReady:function(e){f_load_newsletter();}}
});

