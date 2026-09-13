// Run after: npm install --prefix storage/framework/font-tools --no-save --ignore-scripts subset-font@2.4.0
// Complete Font Awesome faces remain as fallbacks for future dashboard-selected icons.
import fs from 'node:fs';
import path from 'node:path';
import subsetFont from '../storage/framework/font-tools/node_modules/subset-font/index.js';
const roots=['resources/views','packages/core/pages/src/resources/views/web'];
const sources=[];
function scan(dir){for(const e of fs.readdirSync(dir,{withFileTypes:true})){const p=path.join(dir,e.name);if(e.isDirectory())scan(p);else if(p.endsWith('.blade.php'))sources.push(fs.readFileSync(p,'utf8'));}}
roots.forEach(scan);
const names=new Set(sources.join('\n').match(/fa-[a-z0-9-]+/g));
['fa-wallet','fa-gift','fa-map-location-dot','fa-tags','fa-shirt','fa-mobile-screen-button','fa-door-closed','fa-truck-fast','fa-soap','fa-check'].forEach(x=>names.add(x));
const original=fs.readFileSync('public/control/assets/vendor/fonts/fontawesome.css','utf8');
const codes=new Set();
for(const match of original.matchAll(/([^{}]+)\{([^{}]*)\}/g)){
 const content=match[2].match(/content:\s*["']\\([0-9a-f]+)["']/i);
 if(content&&[...match[1].matchAll(/\.(fa-[a-z0-9-]+):/g)].some(x=>names.has(x[1])))codes.add(parseInt(content[1],16));
}
if(codes.size<20)throw new Error('Icon extraction failed');
const text=String.fromCodePoint(...[...codes].sort((a,b)=>a-b));
const ranges=[...codes].sort((a,b)=>a-b).map(x=>'U+'+x.toString(16).toUpperCase()).join(',');
let css='/* Derived from Font Awesome Free 6.4.0. Fonts: SIL OFL 1.1; see landing-icons.css. */\n';
for(const [name,family,weight] of [['solid-900','Font Awesome 6 Free',900],['regular-400','Font Awesome 6 Free',400],['brands-400','Font Awesome 6 Brands',400]]){
 const originalFont=fs.readFileSync('public/control/assets/vendor/fonts/fontawesome/fa-'+name+'.woff2');
 const output=await subsetFont(originalFont,text,{targetFormat:'woff2'});
 fs.writeFileSync('resources/fonts/landing/icons-'+name+'.woff2',output);
 css+=`@font-face { font-family: '${family}'; font-style: normal; font-weight: ${weight}; font-display: swap; src: url('../../fonts/landing/icons-${name}.woff2') format('woff2'); unicode-range: ${ranges}; }\n`;
 console.log(name,originalFont.length,'->',output.length);
}
fs.writeFileSync('resources/css/vendor/landing-icon-subsets.css',css);

// Only symbol mappings used by the public templates block first paint.
// Other mappings load asynchronously and retain dashboard-selected icon compatibility.
let critical=original, deferred='/* Font Awesome Free 6.4.0 — Icons: CC BY 4.0; Code: MIT. */\n';
critical=critical.replace(/([^{}]+)\{([^{}]*)\}/g,(rule,selector,body)=>{
 if(!/content:/.test(body)||!/\.fa-/.test(selector))return rule;
 if([...selector.matchAll(/\.(fa-[a-z0-9-]+):/g)].some(x=>names.has(x[1])))return rule;
 deferred+=rule+'\n';return '';
});
critical=critical.replace(/url\("fontawesome\/([^".]+)\.woff2"\) format\("woff2"\), url\("fontawesome\/[^".]+\.ttf"\) format\("truetype"\)/g,'url("../../../public/control/assets/vendor/fonts/fontawesome/$1.woff2") format("woff2")').replace(/font-display: block/g,'font-display: swap');
fs.writeFileSync('resources/css/vendor/landing-icons.css',critical);
fs.writeFileSync('resources/css/vendor/landing-icons-full.css',deferred);
