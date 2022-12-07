//
// CMSUno
// Plugin Newsletter
//
function f_load_newsletter(){
	let x=new FormData();
	x.set('action','load');
	x.set('unox',Unox);
	fetch('uno/plugins/newsletter/newsletter.php',{method:'post',body:x})
	.then(r=>r.json())
	.then(function(r){
		let t,to,tr,td,ch;
		if(r.su)document.getElementById('newsletterSu').value=r.su;
		if(r.met){
			if(r.met=='gmail')document.querySelectorAll('.trsmt2').forEach(function(e){e.style.display='none';});
			t=document.getElementById("newsletterMet");
			to=t.options;
			for(v=0;v<to.length;v++){if(to[v].value==r.met){to[v].selected=true;v=to.length;}}
		}
		else{
			document.querySelectorAll('.trsmtp').forEach(function(e){e.style.display='none';});
			document.querySelectorAll('.trsmt2').forEach(function(e){e.style.display='none';});
		}
		if(r.gma)document.getElementById('newsletterGmA').value=r.gma;
		if(r.gmp)document.getElementById('newsletterGmP').value=r.gmp;
		if(r.gmh)document.getElementById('newsletterGmH').value=r.gmh;
		if(r.list){
			t=document.getElementById("newsletterTlist");
			t.innerHTML='';
			for(let k in r.list){
				let v=r.list[k];
				tr=document.createElement('tr');
				td=document.createElement('td');td.innerHTML=k;
				tr.appendChild(td);
				td=document.createElement('td');ch='';
				for(let k1 in v)ch+=v[k1]+',';
				td.innerHTML=ch.substr(0,ch.length-1);
				tr.appendChild(td);
				td=document.createElement('td');td.onclick=function(){f_del_newsletter(k)};td.innerHTML='X';
				tr.appendChild(td);
				td=document.createElement('td');td.onclick=function(){f_edit_newsletter(k,r.list[k])};td.innerHTML='&nbsp;';
				td.style.backgroundImage='url('+Udep+'includes/img/ui-icons_444444_256x240.png)';
				td.style.backgroundPosition='-62px -110px';
				td.style.backgroundRepeat='no-repeat';
				td.style.display='inline-block';
				tr.appendChild(td);
				t.appendChild(tr);
			}
		}
		if(r.group){
			t=document.getElementById('newsletterGroup');
			ch=document.getElementById('newsletterLgroup');
			t.innerHTML='';ch.innerHTML='';
			r.group.forEach(function(v){
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
		let x=new FormData();
		x.set('action','loadContent');
		x.set('unox',Unox);
		fetch('uno/plugins/newsletter/newsletter.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(r=>CKEDITOR.instances['newsletterCont'].setData(r));
	});
}
function f_save_newsletter(){
	document.getElementById('newsletterResult').style.display="none";
	document.getElementById('newsletterResult').innerHTML="";
	let h=CKEDITOR.instances['newsletterCont'].getData();
	h=h.replace(/(\r\n|\n|\r)/gm,"");
	let x=new FormData();
	x.set('action','save');
	x.set('unox',Unox);
	x.set('su',document.getElementById('newsletterSu').value);
	x.set('cont',h);
	fetch('uno/plugins/newsletter/newsletter.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		if(r.substr(0,1)!='!'){
			document.getElementById('newsletterSaveConf').className='bouton fr';
			document.getElementById('newsletterSaveCont').className='bouton fr';
		}
	});
}
function f_saveConf_newsletter(){
	document.getElementById('newsletterResult').style.display="none";
	document.getElementById('newsletterResult').innerHTML="";
	let a=document.getElementById('newsletterMet');
	let x=new FormData();
	x.set('action','saveConf');
	x.set('unox',Unox);
	x.set('met',a.options[a.selectedIndex].value);
	x.set('gma',document.getElementById('newsletterGmA').value);
	x.set('gmp',document.getElementById('newsletterGmP').value);
	x.set('gmh',document.getElementById('newsletterGmH').value);
	fetch('uno/plugins/newsletter/newsletter.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		if(r.substr(0,1)!='!')document.getElementById('newsletterSaveConf').className='bouton fr';
	});
}
function f_add_newsletter(){
	let l=document.getElementById('newsletterAdd').value,g=document.getElementById('newsletterLGadd').value;
	let d=document.querySelectorAll('#newsletterLgroup input:checked'),h=[];
	d.forEach(function(e){h.push(e.getAttribute('name'));});
	let x=new FormData();
	x.set('action','add');
	x.set('unox',Unox);
	x.set('add',l);
	x.set('ng',g);
	x.set('group',JSON.stringify(h));
	fetch('uno/plugins/newsletter/newsletter.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		f_load_newsletter();
	});
}
function f_del_newsletter(l){
	let x=new FormData();
	x.set('action','del');
	x.set('unox',Unox);
	x.set('del',l);
	fetch('uno/plugins/newsletter/newsletter.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		f_load_newsletter();
	});
}
function f_edit_newsletter(f,g){
	document.getElementById('newsletterAdd').value=f;
	document.getElementById('newsletterLGadd').value='';
	let a=document.querySelectorAll('#newsletterLgroup input:checked');
	a.forEach(function(e){e.checked=false;});
	for(let k in g){
		let a=document.querySelector("#newsletterLgroup input[name='"+g[k]+"']");
		if(a!==null)a.checked=true;
	}
}
function f_send_newsletter(f,start,stop){
	window.scrollTo(0,0);
	document.getElementById('wait').style.display='block';
	document.getElementById('newsletterResult').style.display="block";
	let gr=[],d=document.querySelectorAll('#newsletterGroup input:checked');
	d.forEach(function(e){gr.push(e.getAttribute('name'));});
	let x=new FormData();
	x.set('action','load');
	x.set('unox',Unox);
	x.set('send',1);
	fetch('uno/plugins/newsletter/newsletter.php',{method:'post',body:x})
	.then(r=>r.json())
	.then(function(a){
		document.getElementById('newsletterResult').innerHTML='';
		document.getElementById('newsletterResult').insertAdjacentHTML('afterbegin',start+'...<br />');
		let x=new FormData();
		x.set('action','loadContent');
		x.set('unox',Unox);
		fetch('uno/plugins/newsletter/newsletter.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(function(cont){
			let syncSend=(function(v){
				let c='',dest=a.mel;
				if(f&&kl.hasOwnProperty(v)){
					dest=kl[v];
					c=Object.values(al)[v].filter(function(n){return gr.indexOf(n)!==-1;});
				}
				else if(!adm)adm=1;
				else{
					document.getElementById('newsletterResult').insertAdjacentHTML('beforeend','<br />...'+stop);
					document.getElementById('wait').style.display='none';
					return;
				}
				if(c!=''||adm==1){
					let x=new FormData();
					x.set('action','send');
					x.set('unox',Unox);
					x.set('tit',a.tit);
					x.set('mel',a.mel);
					x.set('dest',dest);
					x.set('su',a.su);
					x.set('cont',cont);
					x.set('url',a.url);
					x.set('nom',a.nom);
					x.set('met',a.met);
					x.set('gma',a.gma);
					x.set('gmp',a.gmp);
					x.set('gmh',a.gmh);
					fetch('uno/plugins/newsletter/newsletter.php',{method:'post',body:x})
					.then(r=>r.text())
					.then(function(r){
						document.getElementById('newsletterResult').insertAdjacentHTML('beforeend',r);
						v++;
						syncSend(v);
					});
				}
				else{
					v++;
					syncSend(v);
				}
			});
			let al=a.list,kl=Object.keys(al),adm=0,dest;
			syncSend(0);
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
	document.getElementById('newsletterResult').innerHTML="";
}
function f_list_newsletter(){
	document.getElementById('newsletterConfig').style.display="none";
	document.getElementById('newsletterWrite').style.display="none";
	document.getElementById('newsletterList').style.display="block";
	document.getElementById('newsletterC').className="bouton fr";
	document.getElementById('newsletterW').className="bouton fr";
	document.getElementById('newsletterL').className="bouton fr current";
	document.getElementById('newsletterResult').style.display="none";
	document.getElementById('newsletterResult').innerHTML="";
}
function f_config_newsletter(){
	document.getElementById('newsletterConfig').style.display="block";
	document.getElementById('newsletterWrite').style.display="none";
	document.getElementById('newsletterList').style.display="none";
	document.getElementById('newsletterC').className="bouton fr current";
	document.getElementById('newsletterW').className="bouton fr";
	document.getElementById('newsletterL').className="bouton fr";
	document.getElementById('newsletterResult').style.display="none";
	document.getElementById('newsletterResult').innerHTML="";
}
function f_trsmtp_newsletter(f){
	if(f.options[f.selectedIndex].value==''){
		document.querySelectorAll('.trsmtp').forEach(function(e){e.style.display='none';});
		document.querySelectorAll('.trsmt2').forEach(function(e){e.style.display='none';});
	}
	else if(f.options[f.selectedIndex].value=='gmail'){
		document.querySelectorAll('.trsmtp').forEach(function(e){e.style.display='table-row';});
		document.querySelectorAll('.trsmt2').forEach(function(e){e.style.display='none';});
	}
	else{
		document.querySelectorAll('.trsmtp').forEach(function(e){e.style.display='table-row';});
		document.querySelectorAll('.trsmt2').forEach(function(e){e.style.display='table-row';});
	}
}
//
CKEDITOR.replace('newsletterCont',{
	height:'300',
	on:{instanceReady:function(e){f_load_newsletter();}}
});
