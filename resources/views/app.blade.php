<!DOCTYPE html>
<html lang="id"><head><meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
@verbatim
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Pasolex Store</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--bg:#f1f4fb;--card:#fff;--ink:#14213d;--mute:#6b7690;--line:#e6eaf3;--btn:#22529b;--side:#101f3d;--act:#1e3a6e;--pink:#e0719a;--soft:#f5f7fc;--ok:#1f8f5f;--okb:#dff5ea;--wn:#a86a00;--wnb:#fdefd0;--bad:#d03b3b;--badb:#fde3e3;color-scheme:light;box-sizing:border-box;padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}
@media(prefers-color-scheme:dark){:root:not([data-theme=light]){--bg:#0b1020;--card:#151c30;--ink:#e8ecf6;--mute:#93a0bd;--line:#242d47;--btn:#4a7fd4;--side:#080d1a;--soft:#1b2440;--okb:#17372b;--wnb:#3b2f12;--badb:#3d1c1c;--ok:#4fc08d;--wn:#e8b44f;--bad:#f07a7a;color-scheme:dark}}
:root[data-theme=dark]{--bg:#0b1020;--card:#151c30;--ink:#e8ecf6;--mute:#93a0bd;--line:#242d47;--btn:#4a7fd4;--side:#080d1a;--soft:#1b2440;--okb:#17372b;--wnb:#3b2f12;--badb:#3d1c1c;--ok:#4fc08d;--wn:#e8b44f;--bad:#f07a7a;color-scheme:dark}
*{box-sizing:border-box}html,body{margin:0;min-height:100%}
body{background:var(--bg);color:var(--ink);font:14px/1.5 Inter,system-ui,sans-serif}
button,input,select,textarea{font:inherit;color:inherit}:focus-visible{outline:2px solid var(--btn);outline-offset:2px}
h1{font-size:28px;margin:0;letter-spacing:-.4px}.sub{color:var(--mute);margin:2px 0 0;font-size:13px}
.shell{display:grid;grid-template-columns:230px 1fr;min-height:100vh}
aside{background:var(--side);border-left:4px solid var(--pink);color:#cfd8ec;padding:22px 14px;display:flex;flex-direction:column;position:sticky;top:0;height:100vh;overflow:hidden}
.brand{padding:0 8px 22px}.brand b{font-size:19px;color:#fff;display:block;letter-spacing:.3px}.brand span{font-size:10px;color:#8fa1c6}
nav{display:flex;flex-direction:column;gap:3px}
nav a{color:#c3cee6;text-decoration:none;padding:10px 14px;border-radius:8px;border-left:3px solid transparent}
nav a:hover{background:#ffffff10}nav a.on{background:var(--act);color:#fff;font-weight:600;border-left-color:var(--pink)}
nav hr{border:0;border-top:1px solid #ffffff1c;margin:10px 0;width:100%}
aside .fl{position:absolute;left:12px;bottom:12px;opacity:.5}
main{padding:28px 34px 40px;min-width:0}
.top{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;margin-bottom:22px;flex-wrap:wrap}
.acts{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
.chip{background:var(--card);border:1px solid var(--line);border-radius:99px;padding:6px 14px;font-weight:600;font-size:13px}
.btn{background:var(--btn);color:#fff;border:0;border-radius:8px;padding:9px 16px;font-weight:600;cursor:pointer}.btn:hover{filter:brightness(1.12)}
.btn.w{background:var(--card);color:var(--ink);border:1px solid var(--line)}.btn.g{background:#1f8f5f}.btn.g2{background:#1d7a52}
.card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:20px}.card h3{margin:0 0 4px;font-size:16px}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px}.stats.s3{grid-template-columns:repeat(3,1fr)}
.st{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:16px 18px;display:flex;gap:12px;align-items:center}
.st i{width:36px;height:36px;border-radius:10px;background:var(--soft);display:grid;place-items:center;font-style:normal;font-weight:700;color:var(--btn)}
.st small{color:var(--mute);font-size:12px;display:block}.st b{font-size:26px;line-height:1.15;display:block;letter-spacing:-.5px}
.s3 .st{border:0;background:none;padding:6px 8px;display:block}.s3 .st b{font-size:30px}
.chart{display:flex;align-items:flex-end;justify-content:space-around;height:180px;margin:16px 0 34px;border-bottom:1px solid var(--line)}
.chart>div{display:flex;flex-direction:column;align-items:center;position:relative}
.chart .p{display:flex;flex-direction:row;gap:5px;align-items:flex-end}
.chart .p s{display:block;width:12px;min-height:3px;border-radius:4px 4px 0 0;background:var(--btn)}.chart .p s+s{background:#9fb6dd}
.chart span{position:absolute;bottom:-22px;font-size:11px;color:var(--mute)}
.lg{display:flex;gap:14px;font-size:12px;color:var(--mute);margin-top:10px}.lg b{display:inline-block;width:9px;height:9px;border-radius:3px;background:var(--btn);margin-right:5px}.lg b+b{}
.rack{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-top:14px}.bin{border:1px solid var(--line);background:var(--soft);border-radius:12px;padding:14px}.bin small{color:var(--mute);font-size:11px;display:block}.bin .pc{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:2px}.bin .pc b{font-size:13px}.bin .pc span{font-size:22px;font-weight:700;letter-spacing:-.5px}.fill{height:7px;border-radius:4px;background:var(--line);margin-top:10px;overflow:hidden}.fill i{display:block;height:100%;border-radius:4px;background:var(--btn)}.fill i.hi{background:#e8a93a}.fill i.full{background:var(--bad)}
.tw{overflow-x:auto}table{width:100%;border-collapse:collapse;font-size:13px}
th{text-align:left;font-weight:600;color:var(--mute);font-size:11px;letter-spacing:.4px;padding:10px;white-space:nowrap}
td{padding:11px 10px;border-top:1px solid var(--line)}tbody tr:nth-child(even) td{background:color-mix(in srgb,var(--soft) 60%,transparent)}
.pill{display:inline-block;padding:2px 10px;border-radius:99px;font-size:12px;font-weight:600}.ok{background:var(--okb);color:var(--ok)}.wn{background:var(--wnb);color:var(--wn)}.bd{background:var(--badb);color:var(--bad)}
.av{width:30px;height:30px;border-radius:8px;display:inline-block;vertical-align:middle;margin-right:10px;background:var(--c)}
.pn{display:inline-block;vertical-align:middle}.pn small{display:block;color:var(--mute);font-size:11px}
.lk{background:none;border:0;color:var(--btn);cursor:pointer;font-weight:600;padding:2px 6px}.lk.d{color:var(--bad)}
.tools{display:flex;gap:10px;margin:12px 0}.tools input,.tools select{margin:0;flex:1}.tools select{flex:0 0 170px}
label{display:block;font-size:12px;font-weight:600;margin-bottom:12px}
input,select,textarea{display:block;width:100%;margin-top:5px;padding:10px 12px;border:1px solid var(--line);border-radius:8px;background:var(--soft);font-weight:400}textarea{min-height:64px;resize:vertical}
.tabs{display:flex;gap:6px;margin-bottom:14px;flex-wrap:wrap}.tabs button{border:1px solid var(--line);background:var(--card);padding:6px 14px;border-radius:99px;cursor:pointer;font-weight:500}.tabs .on{background:var(--btn);color:#fff;border-color:var(--btn)}
.inl{display:flex;gap:10px;align-items:end;flex-wrap:wrap;margin-bottom:16px}.inl label{margin:0;flex:1;min-width:130px}
.hint{font-size:12px;color:var(--mute);background:var(--soft);padding:8px 10px;border-radius:8px;margin-bottom:12px}
.empty{color:var(--mute);padding:20px 0;margin:0}.err{color:var(--bad);font-size:12px;min-height:18px;margin:0 0 6px}
.mb{position:fixed;inset:0;background:#0a122499;display:grid;place-items:center;padding:16px;z-index:20}.md{background:var(--card);border-radius:16px;padding:24px;width:min(540px,100%);max-height:90vh;overflow:auto}.md h3{font-size:19px;margin:0 0 14px}
#t{position:fixed;right:16px;bottom:calc(16px + env(safe-area-inset-bottom,0px));display:flex;flex-direction:column;gap:8px;z-index:30}#t div{background:var(--ink);color:var(--bg);padding:10px 14px;border-radius:8px;max-width:340px}#t .x{background:var(--bad);color:#fff}
.lgw{min-height:100vh;display:grid;place-items:center;padding:20px;position:relative;overflow:hidden;background:var(--bg)}
.lgc{background:var(--card);border-radius:14px;padding:34px 40px;width:min(400px,100%);text-align:center;box-shadow:0 2px 24px #22529b14;position:relative;z-index:1}
.lgc .lo{font-weight:700;font-size:18px;letter-spacing:.3px}.lgc .lo small{display:block;font-size:9px;color:var(--btn);font-weight:500}
.ps{width:54px;height:54px;border-radius:50%;background:#e8f0fb;color:var(--btn);font-weight:700;font-size:20px;display:grid;place-items:center;margin:16px auto}
.lgc h1{font-size:22px}.lgc form{text-align:left;margin-top:18px}.lgc .btn{width:100%;padding:12px;margin-top:6px}
.lgc a{display:block;text-align:center;margin-top:14px;font-size:12px;font-weight:600;color:var(--btn);text-decoration:none}
.demo{margin-top:16px;display:flex;gap:6px;flex-wrap:wrap;justify-content:center}.demo p{width:100%;margin:0;font-size:11px;color:var(--mute)}.demo button{border:1px solid var(--line);background:var(--soft);border-radius:99px;padding:4px 10px;font-size:12px;cursor:pointer}
.fl{position:absolute}.fl.a{top:24px;right:22px}.fl.b{bottom:24px;left:22px}
@media print{aside,.noprint,.top .acts{display:none!important}.shell{display:block}main{padding:0}}
@media(max-width:860px){.shell{display:block}aside{position:static;height:auto;flex-direction:row;flex-wrap:wrap;padding:10px;border-left:0;border-top:4px solid var(--pink)}.brand{padding:0 8px 0 0}nav{flex-direction:row;overflow-x:auto;flex:1 0 100%;order:3;margin-top:8px}nav a{white-space:nowrap}nav hr,aside .fl{display:none}main{padding:18px 14px}.stats,.stats.s3{grid-template-columns:1fr 1fr}}
@media(prefers-reduced-motion:reduce){*{animation:none!important}}
</style></head>
<body><div id="app"></div><div id="m"></div><div id="t"></div>
<script>
const $=s=>document.querySelector(s),A={},KEY='pasolex-v1';
const esc=s=>String(s??'').replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const h=s=>'h$'+btoa(s),now=()=>new Date().toISOString().slice(0,16).replace('T',' '),ymd=n=>new Date(Date.now()-n*864e5).toISOString().slice(0,10),dago=n=>ymd(n)+' 09:30';
const fd=t=>new Date(t.slice(0,10)).toLocaleDateString('id-ID',{day:'numeric',month:'short'});
const by=(a,k,v)=>a.find(x=>x[k]==v),nid=a=>a.reduce((m,x)=>Math.max(m,x.id),0)+1;
let DB=null,U=null,P='dash',MT='produk',IT='stok',Q='',SF='',RP=0,RD='',RS=ymd(0);


const sto=(p,l)=>(DB.stok.find(s=>s.p==p&&s.l==l)||{q:0}).q,tot=p=>DB.stok.filter(s=>s.p==p).reduce((a,s)=>a+s.q,0),used=l=>DB.stok.filter(s=>s.l==l).reduce((a,s)=>a+s.q,0);
function add(p,l,d){let s=DB.stok.find(s=>s.p==p&&s.l==l);if(!s)DB.stok.push(s={p,l,q:0});s.q+=d}
const pn=id=>{const p=by(DB.produk,'id',id);return `${p.nama}, ${by(DB.warna,'id',p.w).n} ${by(DB.ukuran,'id',p.u).n}`};
const _tk=document.querySelector('meta[name=csrf-token]'),TOK=_tk?_tk.content:'';
async function api(url,method='GET',body){
 const r=await fetch(url,{method,credentials:'same-origin',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':TOK},body:body?JSON.stringify(body):undefined});
 let j={};try{j=await r.json()}catch(e){}
 if(!r.ok){const e=new Error(j.message||('Terjadi kesalahan pada server ('+r.status+'). Lihat storage/logs/laravel.log.'));e.status=r.status;throw e}
 return j}
const tryit=p=>p.catch(e=>{if(e.status==401){U=null;DB=null;draw()}toast(e.message,1)});
const load=async()=>{DB=await api('/api/data')};
const done=m=>async()=>{await load();A.close();toast(m);draw()};
const ROLE={pemilik:'Pemilik toko',admin:'Staf admin',gudang:'Staf gudang'};
const NAV={pemilik:[['dash','Dashboard'],['inv','Inventory'],['rep','Reports']],admin:[['dash','Dashboard'],['master','Master Data'],['inv','Inventory'],['rep','Reports']],gudang:[['dash','Dashboard'],['inv','Inventory']]};
const ops=(a,f)=>a.map(x=>[x.id,f(x)]);
const fld=(id,l,t,o,v='')=>`<label>${l}${t=='select'?`<select id="${id}">${o.map(([a,b])=>`<option value="${a}" ${a==v?'selected':''}>${esc(b)}</option>`).join('')}</select>`:t=='area'?`<textarea id="${id}">${esc(v)}</textarea>`:`<input id="${id}" type="${t}" value="${esc(v)}" ${t=='number'?'min=0':''}>`}</label>`;
const val=id=>$('#'+id).value.trim();
const tbl=(hd,r)=>r.length?`<div class=tw><table><thead><tr>${hd.map(x=>`<th>${x}</th>`).join('')}</tr></thead><tbody>${r.map(c=>`<tr>${c.map(x=>`<td>${x}</td>`).join('')}</tr>`).join('')}</tbody></table></div>`:'<p class=empty>Belum ada data.</p>';
function toast(m,bad){const d=document.createElement('div');d.textContent=m;if(bad)d.className='x';$('#t').append(d);setTimeout(()=>d.remove(),3800)}
const modal=(t,b)=>{$('#m').innerHTML=`<div class=mb onclick="if(event.target==this)A.close()"><div class=md role=dialog><h3>${t}</h3>${b}</div></div>`;A.hint()};
A.close=()=>$('#m').innerHTML='';
const flower=(c='#e9d3e6',o=.9)=>`<svg width=64 height=64 viewBox="0 0 64 64" style="opacity:${o}">${[[32,12],[50,26],[43,48],[21,48],[14,26]].map(([x,y])=>`<circle cx=${x} cy=${y} r=10 fill="${c}"/>`).join('')}<circle cx=32 cy=34 r=5 fill="#e8bf6a"/></svg>`;
const hdr=(t,s,a='')=>`<div class=top><div><h1>${t}</h1><p class=sub>${s}</p></div><div class=acts>${a}<span class=chip>${esc(U.nama)} · Store</span></div></div>`;

/* login */
const loginV=()=>`<div class=lgw><span class="fl a">${flower('#dfe3ef')}</span><span class="fl b">${flower('#dfe3ef')}</span><div class=lgc><div class=lo>PASOLEX<small>STORE</small></div><div class=ps>PS</div><h1>Welcome back</h1><p class=sub>Sign in to manage your inventory</p>
<form onsubmit="A.login(event)"><label>Username<input id=lu placeholder="Enter your username" autocomplete=username></label><label>Password<input id=lp type=password placeholder="Enter your password" autocomplete=current-password></label><p id=err class=err role=alert></p><button class=btn>LOGIN</button><a href="#" onclick="toast('Hubungi admin toko untuk mengatur ulang password.');return false">Forgot Password?</a></form>
<div class=demo><p>Akun contoh (password 123456)</p>${['admin','pemilik'].map(r=>`<button onclick="A.fill('${r}')">${ROLE[r]}</button>`).join('')}</div></div></div>`;
A.fill=r=>{$('#lu').value=r;$('#lp').value='123456'};
A.login=async e=>{e.preventDefault();$('#err').textContent='';try{U=await api('/login','POST',{username:val('lu'),password:$('#lp').value});await load();P='dash';draw()}catch(x){U=null;$('#err').textContent=x.message}};
A.out=()=>modal('Keluar dari akun?','<p class=sub style="margin:0 0 18px;font-size:14px">Anda akan keluar dari Pasolex Store dan perlu login lagi untuk melanjutkan.</p><button class=btn onclick="A.out2()">Ya, keluar</button> <button class=lk onclick="A.close()">Batal</button>');
A.out2=()=>{api('/logout','POST').catch(()=>{}).then(()=>location.reload())};
A.go=p=>{P=p;Q='';tryit(load().then(draw))};

const pg={};
const stat=p=>{const q=tot(p);return q==0?['Habis','bd']:q<=15?['Menipis','wn']:['Aman','ok']};
const lastT=p=>[...DB.inbound,...DB.outbound,...DB.rusak].filter(x=>x.p==p).map(x=>x.t).sort().pop()||'';
const moves=()=>[...DB.inbound.map(x=>({...x,j:'Inbound'})),...DB.outbound.map(x=>({...x,j:'Outbound'})),...DB.rusak.map(x=>({...x,j:'Reject'}))].sort((a,b)=>b.t.localeCompare(a.t));

pg.dash=()=>{const M=[];for(let i=6;i>=0;i--){const d=new Date();d.setDate(1);d.setMonth(d.getMonth()-i);M.push(d.toISOString().slice(0,7))}
 const sm=(k,m)=>DB[k].filter(x=>x.t.slice(0,7)==m).reduce((a,x)=>a+x.q,0),mx=Math.max(1,...M.flatMap(m=>[sm('inbound',m),sm('outbound',m)]));
 const jc={Inbound:'color:var(--btn)',Outbound:'color:var(--btn)',Reject:'color:var(--bad)'};
 return hdr(`Welcome, ${esc(U.nama)}`,'Inventory overview & activity')+
 `<div class="stats s3"><div class=st><small>Total Stock</small><b>${DB.stok.reduce((a,s)=>a+s.q,0).toLocaleString('id-ID')}</b><small>items</small></div><div class=st><small>Total Categories</small><b>${new Set(DB.produk.map(p=>p.nama)).size}</b><small>models</small></div><div class=st><small>Recent Outbound</small><b>${sm('outbound',M[6])}</b><small>this month</small></div></div>
 <div class=card style="margin-bottom:18px"><h3>Monthly Inventory Movement</h3><p class=sub>Inbound vs Outbound</p><div class=chart>${M.map(m=>`<div><div class=p><s style="height:${Math.round(sm('inbound',m)/mx*170)}px" title="Inbound ${sm('inbound',m)}"></s><s style="height:${Math.round(sm('outbound',m)/mx*170)}px" title="Outbound ${sm('outbound',m)}"></s></div><span>${new Date(m+'-01').toLocaleDateString('id-ID',{month:'short'})}</span></div>`).join('')}</div><div class=lg><span><b></b>Inbound</span><span><b style="background:#9fb6dd"></b>Outbound</span></div></div>
 <div class=card style="margin-bottom:18px"><h3>Isi Rak</h3><p class=sub>Persentase kapasitas yang terpakai di tiap lokasi rak</p><div class=rack>${DB.lok.map(l=>{const u=used(l.id),r=Math.round(u/l.kap*100);return `<div class=bin><div class=pc><b>${l.kode}</b><span>${r}%</span></div><small>${u} dari ${l.kap} pcs</small><div class=fill role=img aria-label="Rak ${l.kode} terisi ${r} persen"><i class="${r>=100?'full':r>=85?'hi':''}" style="width:${Math.min(100,r)}%"></i></div></div>`}).join('')}</div><div class=lg><span><b style="background:#e8a93a"></b>85% atau lebih</span><span><b style="background:var(--bad)"></b>Penuh</span></div></div>
 <div class=card><h3>Recent Transactions</h3>${tbl(['Date','Item','Qty','Status'],moves().slice(0,5).map(x=>[fd(x.t),esc(by(DB.produk,'id',x.p).nama),x.q,`<b style="${jc[x.j]};font-size:11px;letter-spacing:.3px">${x.j.toUpperCase()}</b>`]))}</div>`};

/* master */
const CFG={warna:['Warna',[['n','Nama warna','text']]],lok:['Lokasi Rak',[['rak','Rak','text'],['tg','Tingkat','text'],['sd','Sisi','text'],['kap','Kapasitas','number']]],kategori:['Jenis Rusak',[['n','Jenis kerusakan','text']]],tindak:['Tindak Lanjut',[['n','Nama tindakan','text']]]};
const TABS=()=>[['produk','Varian Produk'],...Object.entries(CFG).map(([k,v])=>[k,v[0]]),['akun','Akun']];
pg.master=()=>{const tabs=`<div class=tabs>${TABS().map(([k,l])=>`<button class="${k==MT?'on':''}" onclick="MT='${k}';draw()">${l}</button>`).join('')}</div>`;
 if(MT=='produk')return hdr('Manage Varian Produk (SKU)','Kelola SKU, model, warna, ukuran, dan stok.',`<button class=btn onclick="A.pf()">+ Add New Varian</button>`)+tabs+`<div class=card>${tbl(['No','SKU','Model','Warna','Ukuran','Stok','Action'],DB.produk.map((x,i)=>[String(i+1).padStart(2,'0'),x.sku,esc(x.nama),by(DB.warna,'id',x.w).n,by(DB.ukuran,'id',x.u).n,`<b>${tot(x.id)}</b>`,`<button class=lk onclick="A.pf(${x.id})">Edit</button><button class="lk d" onclick="A.pd(${x.id})">Delete</button>`]))}<p class=sub style="margin-top:12px">Showing ${DB.produk.length} varian</p></div>`;
 if(MT=='akun')return hdr('Akun Pengguna','Kelola akun pemilik, admin, dan staf gudang.')+tabs+`<div class=card><form class=inl onsubmit="A.ak(event)">${fld('ar','Peran','select',Object.entries(ROLE))}${fld('an','Nama lengkap','text')}${fld('au','Username','text')}${fld('ap','Password (min. 6)','password')}<button class=btn>Simpan</button></form>${tbl(['Nama','Username','Peran',''],Object.keys(DB.u).flatMap(r=>DB.u[r].map(x=>[esc(x.nama),esc(x.username),ROLE[r],r==U.role&&x.id==U.id?'':`<button class="lk d" onclick="A.ad('${r}',${x.id})">Delete</button>`])))}</div>`;
 const [t,f]=CFG[MT],R=DB[MT];
 return hdr(`Manage ${t}`,'Data pendukung untuk produk dan transaksi.')+tabs+`<div class=card><form class=inl onsubmit="A.ma(event)">${f.map(([k,l,ty])=>fld('m_'+k,l,ty,[['KI','Kiri'],['KA','Kanan']])).join('')}<button class=btn>Tambah</button></form>${tbl(MT=='lok'?['Kode rak','Kapasitas','Terisi','']:[t,''],R.map(x=>MT=='lok'?[x.kode,x.kap,used(x.id),`<button class="lk d" onclick="A.md(${x.id})">Delete</button>`]:[esc(x.n),`<button class="lk d" onclick="A.md(${x.id})">Delete</button>`]))}</div>`};
A.pf=id=>{const x=id?by(DB.produk,'id',id):{};modal(id?'Edit Varian':'Add New Varian',`<form onsubmit="A.ps(event,${id||0})">${fld('a','SKU','text',0,x.sku)}${fld('b','Model','text',0,x.nama)}${fld('c','Warna','select',ops(DB.warna,w=>w.n),x.w)}${fld('d','Ukuran','select',ops(DB.ukuran,w=>w.n),x.u)}${fld('e','Deskripsi (opsional)','area',0,x.desk)}<button class=btn>Simpan</button> <button type=button class=lk onclick="A.close()">Batal</button></form>`)};
A.ps=(e,id)=>{e.preventDefault();const o={id:id||0,sku:val('a'),nama:val('b'),w:+val('c'),u:+val('d'),desk:val('e')};if(!o.sku||!o.nama)return toast('SKU dan model wajib diisi.',1);tryit(api('/api/produk','POST',o).then(done('Varian disimpan.')))};
A.pd=id=>{if(confirm('Hapus varian ini?'))tryit(api('/api/produk/'+id,'DELETE').then(done('Varian dihapus.')))};
A.ma=e=>{e.preventDefault();const o={};CFG[MT][1].forEach(([k,,ty])=>o[k]=ty=='number'?+val('m_'+k):val('m_'+k));tryit(api('/api/master/'+MT,'POST',o).then(done('Data ditambahkan.')))};
A.md=id=>{if(confirm('Hapus data ini?'))tryit(api('/api/master/'+MT+'/'+id,'DELETE').then(done('Data dihapus.')))};
A.ak=e=>{e.preventDefault();tryit(api('/api/akun','POST',{role:val('ar'),nama:val('an'),username:val('au'),password:$('#ap').value}).then(done('Akun dibuat.')))};
A.ad=(r,id)=>{if(confirm('Hapus akun ini?'))tryit(api('/api/akun/'+r+'/'+id,'DELETE').then(done('Akun dihapus.')))};

/* inventory (hanya lihat; transaksi dikerjakan staff gudang lewat aplikasi gudang) */
const wc=n=>({navy:'#2a4d86',hitam:'#1d2433',putih:'#d9dde6','abu-abu':'#8a93a5',olive:'#6b7a3a'}[String(n).toLowerCase()]||'#9aa5bd');
const stokTab=()=>{const rows=DB.produk.filter(p=>(p.sku+p.nama).toLowerCase().includes(Q.toLowerCase())&&(!SF||stat(p.id)[0]==SF));
 return tbl(['PRODUK / SKU','LOKASI RAK','TERSEDIA','STATUS','DIPERBARUI'],rows.map(p=>{const ls=DB.stok.filter(s=>s.p==p.id&&s.q>0).sort((a,b)=>b.q-a.q),[st,c]=stat(p.id),w=by(DB.warna,'id',p.w).n;
 return [`<span class=av style="--c:${wc(w)}"></span><span class=pn><b>${esc(p.nama)}</b><small>${esc(p.sku)} · ${esc(w)} · ${esc(by(DB.ukuran,'id',p.u).n)}</small></span>`,ls.length?esc(by(DB.lok,'id',ls[0].l).kode)+(ls.length>1?` +${ls.length-1}`:''):'-',`<b style="${tot(p.id)==0?'color:var(--bad)':''}">${tot(p.id)}</b>`,`<span class="pill ${c}">${st}</span>`,lastT(p.id)?fd(lastT(p.id)):'-']}))};
const rusakTab=()=>`<h3>Riwayat Barang Rusak</h3><p class=sub style="margin-bottom:8px">Dicatat oleh staff gudang.</p>${tbl(['Tanggal','Produk','Rak','Qty','Jenis Kerusakan','Tindak Lanjut','Petugas'],DB.rusak.slice().reverse().map(x=>[fd(x.t),esc(pn(x.p)),esc(by(DB.lok,'id',x.l).kode),x.q,esc(by(DB.kategori,'id',x.k).n),esc(by(DB.tindak,'id',x.f).n),esc(x.by)]))}`;
const opTab=()=>`<h3>Riwayat Stock Opname</h3><p class=sub style="margin-bottom:8px">Hasil hitung fisik dari staff gudang. Setiap opname langsung menyesuaikan stok rak.</p>${tbl(['Tanggal','Produk','Rak','Stok Sistem','Stok Fisik','Selisih','Alasan','Petugas'],DB.adj.slice().reverse().map(a=>[fd(a.t),esc(pn(a.p)),esc(by(DB.lok,'id',a.l).kode),a.sis,a.fis,`<b style="color:${a.sel<0?'var(--bad)':a.sel>0?'var(--ok)':'inherit'}">${a.sel>0?'+':''}${a.sel}</b>`,esc(a.alasan||'-'),esc(a.by)]))}`;
pg.inv=()=>{const ps=DB.produk,low=ps.filter(p=>stat(p.id)[0]=='Menipis').length,out=ps.filter(p=>stat(p.id)[0]=='Habis').length;
 const tab=(k,l)=>`<button class="${IT==k?'on':''}" onclick="IT='${k}';draw()">${l}</button>`;
 return hdr('Inventory','Pantau stok, lokasi, dan ketersediaan barang secara real-time.')+
 `<div class=stats><div class=st><i>▦</i><div><small>Total Stok</small><b>${DB.stok.reduce((a,s)=>a+s.q,0).toLocaleString('id-ID')}</b></div></div><div class=st><i style="color:var(--wn)">!</i><div><small>Stok Menipis</small><b>${low}</b></div></div><div class=st><i style="color:var(--bad)">×</i><div><small>Stok Habis</small><b>${out}</b></div></div><div class=st><i>✓</i><div><small>Barang Rusak</small><b>${DB.rusak.reduce((a,x)=>a+x.q,0)}</b></div></div></div>
 <div class=card><div class=tabs>${tab('stok','Daftar Stok Barang')}${tab('rusak','Barang Rusak')}${tab('op','Stock Opname')}</div>${IT=='op'?opTab():IT=='rusak'?rusakTab():`<div class=tools><input placeholder="Cari produk atau SKU..." value="${esc(Q)}" oninput="Q=this.value;$('#st').innerHTML=stokTab()"><select onchange="SF=this.value;$('#st').innerHTML=stokTab()"><option value="">Semua status</option>${['Aman','Menipis','Habis'].map(x=>`<option ${x==SF?'selected':''}>${x}</option>`).join('')}</select></div><div id=st>${stokTab()}</div>`}</div>`};
A.hint=()=>{};

/* reports */
const defRD=()=>moves().reduce((m,x)=>x.t.slice(0,10)<m?x.t.slice(0,10):m,ymd(0));
const repRows=()=>moves().filter(x=>{const d=x.t.slice(0,10);return d>=(RD||defRD())&&d<=RS}).map(x=>{const p=by(DB.produk,'id',x.p);return {d:x.t.slice(5,10).split('-').reverse().join('/'),j:x.j,sku:p.sku,n:p.nama,l:by(DB.lok,'id',x.l).kode,q:x.q}});
pg.rep=()=>{const adm=U.role=='admin',R=repRows(),sm=j=>R.filter(x=>x.j==j).reduce((a,x)=>a+x.q,0);
 return hdr('Laporan Inventaris',adm?'Filter dan export riwayat mutasi inventaris.':'Pantau riwayat mutasi inventaris pada periode yang dipilih.')+
 `<form class="inl noprint" onsubmit="A.lb(event)"><label>Start Date<input id=ld type=date value="${RD||defRD()}"></label><label>End Date<input id=ls type=date value="${RS}"></label><button class=btn>${adm?'Generate Report':'Tampilkan'}</button>${adm?`<button type=button class="btn g2" onclick="window.print()">PDF</button><button type=button class="btn g" onclick="A.csv()">Excel</button>`:''}</form>
 <div class=card><h3>Inventory Movement Preview</h3><div class="stats s3" style="margin:8px 0"><div class=st><small>Inbound</small><b>${sm('Inbound')}</b><small>pcs</small></div><div class=st><small>Outbound</small><b>${sm('Outbound')}</b><small>pcs</small></div><div class=st><small>Reject</small><b>${sm('Reject')}</b><small>pcs</small></div></div>${tbl(['Tanggal','Jenis Mutasi','SKU','Barang','Lokasi Rak','Qty'],R.map(x=>[x.d,`<span style="${x.j=='Reject'?'color:var(--bad)':''}">${x.j}</span>`,x.sku,esc(x.n),x.l,x.q]))}</div>`};
A.lb=e=>{e.preventDefault();const d=val('ld'),s=val('ls');if(!d||!s||d>s)return toast('Pilih periode yang valid. Tanggal awal tidak boleh setelah tanggal akhir.',1);
 RD=d;RS=s;if(U.role=='admin')toast('Laporan berhasil dibuat.');draw()};
A.csv=()=>{const r=repRows();if(!r.length)return toast('Tidak ada data pada periode ini.',1);
 const c='Tanggal,Jenis,SKU,Barang,Rak,Qty\n'+r.map(x=>[x.d,x.j,x.sku,x.n,x.l,x.q].join(',')).join('\n');
 try{const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([c],{type:'text/csv'}));a.download='laporan-inventaris.csv';a.click()}catch(e){toast('Export tidak didukung di tampilan ini.',1)}};

function draw(){const r=$('#app');if(!U){r.innerHTML=loginV();return}
 const nav=NAV[U.role];if(!nav.some(n=>n[0]==P))P='dash';
 r.innerHTML=`<div class=shell><aside><div class=brand><b>PASOLEX</b><span>STORE</span></div><nav>${nav.map(([k,l])=>`<a href="#" class="${k==P?'on':''}" onclick="A.go('${k}');return false">${l}</a>`).join('')}<hr><a href="#" onclick="A.out();return false">Logout</a></nav><span class=fl>${flower('#3a4a72',.8)}</span></aside><main>${pg[P]()}</main></div>`}
(async()=>{try{U=await api('/api/me');await load()}catch(e){U=null;DB=null}draw()})();
</script></body></html>
@endverbatim
