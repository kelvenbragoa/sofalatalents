import{G as w,l as h,c as B,E as c,Y as L,aM as Y,H as g,aG as y,b as i,W as $,Z as M,b1 as q,k as A,C as S,w as f,s as H,X as _,m as K,V,d as n,r as W,o as G,bl as N,F as O}from"./app-DIymDWP7.js";import{s as j}from"./index-CMSjribt.js";import{s as Z}from"./index-D3aWgD_j.js";import{U as D}from"./index-BmOqLsRc.js";import{s as J}from"./index-Bjs91ZeR.js";import{s as T}from"./index-D3dibykF.js";import{s as Q}from"./index-f0uYfb0h.js";import{s as k}from"./index-cnl34PeR.js";import{s as ee}from"./index-NLyvc_C9.js";var te=function(e){var s=e.dt;return`
.p-skeleton {
    overflow: hidden;
    background: `.concat(s("skeleton.background"),`;
    border-radius: `).concat(s("skeleton.border.radius"),`;
}

.p-skeleton::after {
    content: "";
    animation: p-skeleton-animation 1.2s infinite;
    height: 100%;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    transform: translateX(-100%);
    z-index: 1;
    background: linear-gradient(90deg, rgba(255, 255, 255, 0), `).concat(s("skeleton.animation.background"),`, rgba(255, 255, 255, 0));
}

[dir='rtl'] .p-skeleton::after {
    animation-name: p-skeleton-animation-rtl;
}

.p-skeleton-circle {
    border-radius: 50%;
}

.p-skeleton-animation-none::after {
    animation: none;
}

@keyframes p-skeleton-animation {
    from {
        transform: translateX(-100%);
    }
    to {
        transform: translateX(100%);
    }
}

@keyframes p-skeleton-animation-rtl {
    from {
        transform: translateX(100%);
    }
    to {
        transform: translateX(-100%);
    }
}
`)},ne={root:{position:"relative"}},se={root:function(e){var s=e.props;return["p-skeleton p-component",{"p-skeleton-circle":s.shape==="circle","p-skeleton-animation-none":s.animation==="none"}]}},re=w.extend({name:"skeleton",theme:te,classes:se,inlineStyles:ne}),ie={name:"BaseSkeleton",extends:L,props:{shape:{type:String,default:"rectangle"},size:{type:String,default:null},width:{type:String,default:"100%"},height:{type:String,default:"1rem"},borderRadius:{type:String,default:null},animation:{type:String,default:"wave"}},style:re,provide:function(){return{$pcSkeleton:this,$parentInstance:this}}},C={name:"Skeleton",extends:ie,inheritAttrs:!1,computed:{containerStyle:function(){return this.size?{width:this.size,height:this.size,borderRadius:this.borderRadius}:{width:this.width,height:this.height,borderRadius:this.borderRadius}}}};function oe(t,e,s,a,u,o){return h(),B("div",c({class:t.cx("root"),style:[t.sx("root"),o.containerStyle],"aria-hidden":"true"},t.ptmi("root")),null,16)}C.render=oe;var ae=function(e){var s=e.dt;return`
.p-scrollpanel-content-container {
    overflow: hidden;
    width: 100%;
    height: 100%;
    position: relative;
    z-index: 1;
    float: left;
}

.p-scrollpanel-content {
    height: calc(100% + calc(2 * `.concat(s("scrollpanel.bar.size"),`));
    width: calc(100% + calc(2 * `).concat(s("scrollpanel.bar.size"),`));
    padding-inline: 0 calc(2 * `).concat(s("scrollpanel.bar.size"),`);
    padding-block: 0 calc(2 * `).concat(s("scrollpanel.bar.size"),`);
    position: relative;
    overflow: auto;
    box-sizing: border-box;
    scrollbar-width: none;
}

.p-scrollpanel-content::-webkit-scrollbar {
    display: none;
}

.p-scrollpanel-bar {
    position: relative;
    border-radius: `).concat(s("scrollpanel.bar.border.radius"),`;
    z-index: 2;
    cursor: pointer;
    opacity: 0;
    outline-color: transparent;
    background: `).concat(s("scrollpanel.bar.background"),`;
    border: 0 none;
    transition: outline-color `).concat(s("scrollpanel.transition.duration"),", opacity ").concat(s("scrollpanel.transition.duration"),`;
}

.p-scrollpanel-bar:focus-visible {
    box-shadow: `).concat(s("scrollpanel.bar.focus.ring.shadow"),`;
    outline: `).concat(s("scrollpanel.barfocus.ring.width")," ").concat(s("scrollpanel.bar.focus.ring.style")," ").concat(s("scrollpanel.bar.focus.ring.color"),`;
    outline-offset: `).concat(s("scrollpanel.barfocus.ring.offset"),`;
}

.p-scrollpanel-bar-y {
    width: `).concat(s("scrollpanel.bar.size"),`;
    inset-block-start: 0;
}

.p-scrollpanel-bar-x {
    height: `).concat(s("scrollpanel.bar.size"),`;
    inset-block-end: 0;
}

.p-scrollpanel-hidden {
    visibility: hidden;
}

.p-scrollpanel:hover .p-scrollpanel-bar,
.p-scrollpanel:active .p-scrollpanel-bar {
    opacity: 1;
}

.p-scrollpanel-grabbed {
    user-select: none;
}
`)},le={root:"p-scrollpanel p-component",contentContainer:"p-scrollpanel-content-container",content:"p-scrollpanel-content",barX:"p-scrollpanel-bar p-scrollpanel-bar-x",barY:"p-scrollpanel-bar p-scrollpanel-bar-y"},ce=w.extend({name:"scrollpanel",theme:ae,classes:le}),ue={name:"BaseScrollPanel",extends:L,props:{step:{type:Number,default:5}},style:ce,provide:function(){return{$pcScrollPanel:this,$parentInstance:this}}},U={name:"ScrollPanel",extends:ue,inheritAttrs:!1,initialized:!1,documentResizeListener:null,documentMouseMoveListener:null,documentMouseUpListener:null,frame:null,scrollXRatio:null,scrollYRatio:null,isXBarClicked:!1,isYBarClicked:!1,lastPageX:null,lastPageY:null,timer:null,outsideClickListener:null,data:function(){return{id:this.$attrs.id,orientation:"vertical",lastScrollTop:0,lastScrollLeft:0}},watch:{"$attrs.id":function(e){this.id=e||D()}},mounted:function(){this.id=this.id||D(),this.$el.offsetParent&&this.initialize()},updated:function(){!this.initialized&&this.$el.offsetParent&&this.initialize()},beforeUnmount:function(){this.unbindDocumentResizeListener(),this.frame&&window.cancelAnimationFrame(this.frame)},methods:{initialize:function(){this.moveBar(),this.bindDocumentResizeListener(),this.calculateContainerHeight()},calculateContainerHeight:function(){var e=getComputedStyle(this.$el),s=getComputedStyle(this.$refs.xBar),a=Y(this.$el)-parseInt(s.height,10);e["max-height"]!=="none"&&a===0&&(this.$refs.content.offsetHeight+parseInt(s.height,10)>parseInt(e["max-height"],10)?this.$el.style.height=e["max-height"]:this.$el.style.height=this.$refs.content.offsetHeight+parseFloat(e.paddingTop)+parseFloat(e.paddingBottom)+parseFloat(e.borderTopWidth)+parseFloat(e.borderBottomWidth)+"px")},moveBar:function(){var e=this;if(this.$refs.content){var s=this.$refs.content.scrollWidth,a=this.$refs.content.clientWidth,u=(this.$el.clientHeight-this.$refs.xBar.clientHeight)*-1;this.scrollXRatio=a/s;var o=this.$refs.content.scrollHeight,r=this.$refs.content.clientHeight,b=(this.$el.clientWidth-this.$refs.yBar.clientWidth)*-1;this.scrollYRatio=r/o,this.frame=this.requestAnimationFrame(function(){e.$refs.xBar&&(e.scrollXRatio>=1?(e.$refs.xBar.setAttribute("data-p-scrollpanel-hidden","true"),!e.isUnstyled&&g(e.$refs.xBar,"p-scrollpanel-hidden")):(e.$refs.xBar.setAttribute("data-p-scrollpanel-hidden","false"),!e.isUnstyled&&y(e.$refs.xBar,"p-scrollpanel-hidden"),e.$refs.xBar.style.cssText="width:"+Math.max(e.scrollXRatio*100,10)+"%; inset-inline-start:"+Math.abs(e.$refs.content.scrollLeft)/s*100+"%;bottom:"+u+"px;")),e.$refs.yBar&&(e.scrollYRatio>=1?(e.$refs.yBar.setAttribute("data-p-scrollpanel-hidden","true"),!e.isUnstyled&&g(e.$refs.yBar,"p-scrollpanel-hidden")):(e.$refs.yBar.setAttribute("data-p-scrollpanel-hidden","false"),!e.isUnstyled&&y(e.$refs.yBar,"p-scrollpanel-hidden"),e.$refs.yBar.style.cssText="height:"+Math.max(e.scrollYRatio*100,10)+"%; top: calc("+e.$refs.content.scrollTop/o*100+"% - "+e.$refs.xBar.clientHeight+"px); inset-inline-end:"+b+"px;"))})}},onYBarMouseDown:function(e){this.isYBarClicked=!0,this.$refs.yBar.focus(),this.lastPageY=e.pageY,this.$refs.yBar.setAttribute("data-p-scrollpanel-grabbed","true"),!this.isUnstyled&&g(this.$refs.yBar,"p-scrollpanel-grabbed"),document.body.setAttribute("data-p-scrollpanel-grabbed","true"),!this.isUnstyled&&g(document.body,"p-scrollpanel-grabbed"),this.bindDocumentMouseListeners(),e.preventDefault()},onXBarMouseDown:function(e){this.isXBarClicked=!0,this.$refs.xBar.focus(),this.lastPageX=e.pageX,this.$refs.yBar.setAttribute("data-p-scrollpanel-grabbed","false"),!this.isUnstyled&&g(this.$refs.xBar,"p-scrollpanel-grabbed"),document.body.setAttribute("data-p-scrollpanel-grabbed","false"),!this.isUnstyled&&g(document.body,"p-scrollpanel-grabbed"),this.bindDocumentMouseListeners(),e.preventDefault()},onScroll:function(e){this.lastScrollLeft!==e.target.scrollLeft?(this.lastScrollLeft=e.target.scrollLeft,this.orientation="horizontal"):this.lastScrollTop!==e.target.scrollTop&&(this.lastScrollTop=e.target.scrollTop,this.orientation="vertical"),this.moveBar()},onKeyDown:function(e){if(this.orientation==="vertical")switch(e.code){case"ArrowDown":{this.setTimer("scrollTop",this.step),e.preventDefault();break}case"ArrowUp":{this.setTimer("scrollTop",this.step*-1),e.preventDefault();break}case"ArrowLeft":case"ArrowRight":{e.preventDefault();break}}else if(this.orientation==="horizontal")switch(e.code){case"ArrowRight":{this.setTimer("scrollLeft",this.step),e.preventDefault();break}case"ArrowLeft":{this.setTimer("scrollLeft",this.step*-1),e.preventDefault();break}case"ArrowDown":case"ArrowUp":{e.preventDefault();break}}},onKeyUp:function(){this.clearTimer()},repeat:function(e,s){this.$refs.content[e]+=s,this.moveBar()},setTimer:function(e,s){var a=this;this.clearTimer(),this.timer=setTimeout(function(){a.repeat(e,s)},40)},clearTimer:function(){this.timer&&clearTimeout(this.timer)},onDocumentMouseMove:function(e){this.isXBarClicked?this.onMouseMoveForXBar(e):this.isYBarClicked?this.onMouseMoveForYBar(e):(this.onMouseMoveForXBar(e),this.onMouseMoveForYBar(e))},onMouseMoveForXBar:function(e){var s=this,a=e.pageX-this.lastPageX;this.lastPageX=e.pageX,this.frame=this.requestAnimationFrame(function(){s.$refs.content.scrollLeft+=a/s.scrollXRatio})},onMouseMoveForYBar:function(e){var s=this,a=e.pageY-this.lastPageY;this.lastPageY=e.pageY,this.frame=this.requestAnimationFrame(function(){s.$refs.content.scrollTop+=a/s.scrollYRatio})},onFocus:function(e){this.$refs.xBar.isSameNode(e.target)?this.orientation="horizontal":this.$refs.yBar.isSameNode(e.target)&&(this.orientation="vertical")},onBlur:function(){this.orientation==="horizontal"&&(this.orientation="vertical")},onDocumentMouseUp:function(){this.$refs.yBar.setAttribute("data-p-scrollpanel-grabbed","false"),!this.isUnstyled&&y(this.$refs.yBar,"p-scrollpanel-grabbed"),this.$refs.xBar.setAttribute("data-p-scrollpanel-grabbed","false"),!this.isUnstyled&&y(this.$refs.xBar,"p-scrollpanel-grabbed"),document.body.setAttribute("data-p-scrollpanel-grabbed","false"),!this.isUnstyled&&y(document.body,"p-scrollpanel-grabbed"),this.unbindDocumentMouseListeners(),this.isXBarClicked=!1,this.isYBarClicked=!1},requestAnimationFrame:function(e){var s=window.requestAnimationFrame||this.timeoutFrame;return s(e)},refresh:function(){this.moveBar()},scrollTop:function(e){var s=this.$refs.content.scrollHeight-this.$refs.content.clientHeight;e=e>s?s:e>0?e:0,this.$refs.content.scrollTop=e},timeoutFrame:function(e){setTimeout(e,0)},bindDocumentMouseListeners:function(){var e=this;this.documentMouseMoveListener||(this.documentMouseMoveListener=function(s){e.onDocumentMouseMove(s)},document.addEventListener("mousemove",this.documentMouseMoveListener)),this.documentMouseUpListener||(this.documentMouseUpListener=function(s){e.onDocumentMouseUp(s)},document.addEventListener("mouseup",this.documentMouseUpListener))},unbindDocumentMouseListeners:function(){this.documentMouseMoveListener&&(document.removeEventListener("mousemove",this.documentMouseMoveListener),this.documentMouseMoveListener=null),this.documentMouseUpListener&&(document.removeEventListener("mouseup",this.documentMouseUpListener),this.documentMouseUpListener=null)},bindDocumentResizeListener:function(){var e=this;this.documentResizeListener||(this.documentResizeListener=function(){e.moveBar()},window.addEventListener("resize",this.documentResizeListener))},unbindDocumentResizeListener:function(){this.documentResizeListener&&(window.removeEventListener("resize",this.documentResizeListener),this.documentResizeListener=null)}},computed:{contentId:function(){return this.id+"_content"}}},de=["id"],pe=["aria-controls","aria-valuenow"],me=["aria-controls","aria-valuenow"];function fe(t,e,s,a,u,o){return h(),B("div",c({class:t.cx("root")},t.ptmi("root")),[i("div",c({class:t.cx("contentContainer")},t.ptm("contentContainer")),[i("div",c({ref:"content",id:o.contentId,class:t.cx("content"),onScroll:e[0]||(e[0]=function(){return o.onScroll&&o.onScroll.apply(o,arguments)}),onMouseenter:e[1]||(e[1]=function(){return o.moveBar&&o.moveBar.apply(o,arguments)})},t.ptm("content")),[$(t.$slots,"default")],16,de)],16),i("div",c({ref:"xBar",class:t.cx("barx"),tabindex:"0",role:"scrollbar","aria-orientation":"horizontal","aria-controls":o.contentId,"aria-valuenow":u.lastScrollLeft,onMousedown:e[2]||(e[2]=function(){return o.onXBarMouseDown&&o.onXBarMouseDown.apply(o,arguments)}),onKeydown:e[3]||(e[3]=function(r){return o.onKeyDown(r)}),onKeyup:e[4]||(e[4]=function(){return o.onKeyUp&&o.onKeyUp.apply(o,arguments)}),onFocus:e[5]||(e[5]=function(){return o.onFocus&&o.onFocus.apply(o,arguments)}),onBlur:e[6]||(e[6]=function(){return o.onBlur&&o.onBlur.apply(o,arguments)})},t.ptm("barx"),{"data-pc-group-section":"bar"}),null,16,pe),i("div",c({ref:"yBar",class:t.cx("bary"),tabindex:"0",role:"scrollbar","aria-orientation":"vertical","aria-controls":o.contentId,"aria-valuenow":u.lastScrollTop,onMousedown:e[7]||(e[7]=function(){return o.onYBarMouseDown&&o.onYBarMouseDown.apply(o,arguments)}),onKeydown:e[8]||(e[8]=function(r){return o.onKeyDown(r)}),onKeyup:e[9]||(e[9]=function(){return o.onKeyUp&&o.onKeyUp.apply(o,arguments)}),onFocus:e[10]||(e[10]=function(){return o.onFocus&&o.onFocus.apply(o,arguments)})},t.ptm("bary"),{"data-pc-group-section":"bar"}),null,16,me)],16)}U.render=fe;var he=function(e){return e.dt,`
.p-scrolltop.p-button {
    position: fixed !important;
    inset-block-end: 20px;
    inset-inline-end: 20px;
}

.p-scrolltop-sticky.p-button {
    position: sticky !important;
    display: flex;
    margin-inline-start: auto;
}

.p-scrolltop-enter-from {
    opacity: 0;
}

.p-scrolltop-enter-active {
    transition: opacity 0.15s;
}

.p-scrolltop.p-scrolltop-leave-to {
    opacity: 0;
}

.p-scrolltop-leave-active {
    transition: opacity 0.15s;
}
`},ve={root:function(e){var s=e.props;return["p-scrolltop",{"p-scrolltop-sticky":s.target!=="window"}]},icon:"p-scrolltop-icon"},be=w.extend({name:"scrolltop",theme:he,classes:ve}),ge={name:"BaseScrollTop",extends:L,props:{target:{type:String,default:"window"},threshold:{type:Number,default:400},icon:{type:String,default:void 0},behavior:{type:String,default:"smooth"},buttonProps:{type:Object,default:function(){return{rounded:!0}}}},style:be,provide:function(){return{$pcScrollTop:this,$parentInstance:this}}},F={name:"ScrollTop",extends:ge,inheritAttrs:!1,scrollListener:null,container:null,data:function(){return{visible:!1}},mounted:function(){this.target==="window"?this.bindDocumentScrollListener():this.target==="parent"&&this.bindParentScrollListener()},beforeUnmount:function(){this.target==="window"?this.unbindDocumentScrollListener():this.target==="parent"&&this.unbindParentScrollListener(),this.container&&(M.clear(this.container),this.overlay=null)},methods:{onClick:function(){var e=this.target==="window"?window:this.$el.parentElement;e.scroll({top:0,behavior:this.behavior})},checkVisibility:function(e){e>this.threshold?this.visible=!0:this.visible=!1},bindParentScrollListener:function(){var e=this;this.scrollListener=function(){e.checkVisibility(e.$el.parentElement.scrollTop)},this.$el.parentElement.addEventListener("scroll",this.scrollListener)},bindDocumentScrollListener:function(){var e=this;this.scrollListener=function(){e.checkVisibility(q())},window.addEventListener("scroll",this.scrollListener)},unbindParentScrollListener:function(){this.scrollListener&&(this.$el.parentElement.removeEventListener("scroll",this.scrollListener),this.scrollListener=null)},unbindDocumentScrollListener:function(){this.scrollListener&&(window.removeEventListener("scroll",this.scrollListener),this.scrollListener=null)},onEnter:function(e){M.set("overlay",e,this.$primevue.config.zIndex.overlay)},onAfterLeave:function(e){M.clear(e)},containerRef:function(e){this.container=e?e.$el:void 0}},computed:{scrollTopAriaLabel:function(){return this.$primevue.config.locale.aria?this.$primevue.config.locale.aria.scrollTop:void 0}},components:{ChevronUpIcon:J,Button:T}};function ye(t,e,s,a,u,o){var r=A("Button");return h(),S(V,c({name:"p-scrolltop",appear:"",onEnter:o.onEnter,onAfterLeave:o.onAfterLeave},t.ptm("transition")),{default:f(function(){return[u.visible?(h(),S(r,c({key:0,ref:o.containerRef,class:t.cx("root"),onClick:o.onClick,"aria-label":o.scrollTopAriaLabel,unstyled:t.unstyled},t.buttonProps,{pt:t.pt}),{icon:f(function(b){return[$(t.$slots,"icon",{class:H(t.cx("icon"))},function(){return[(h(),S(_(t.icon?"span":"ChevronUpIcon"),c({class:[t.cx("icon"),t.icon,b.class]},t.ptm("icon")),null,16,["class"]))]})]}),_:3},16,["class","onClick","aria-label","unstyled","pt"])):K("",!0)]}),_:3},16,["onEnter","onAfterLeave"])}F.render=ye;var we={root:"p-avatar-group p-component"},Be=w.extend({name:"avatargroup",classes:we}),xe={name:"BaseAvatarGroup",extends:L,style:Be,provide:function(){return{$pcAvatarGroup:this,$parentInstance:this}}},P={name:"AvatarGroup",extends:xe,inheritAttrs:!1};function Le(t,e,s,a,u,o){return h(),B("div",c({class:t.cx("root")},t.ptmi("root")),[$(t.$slots,"default")],16)}P.render=Le;var $e=function(e){var s=e.dt;return`
.p-overlaybadge {
    position: relative;
}

.p-overlaybadge .p-badge {
    position: absolute;
    inset-block-start: 0;
    inset-inline-end: 0;
    transform: translate(50%, -50%);
    transform-origin: 100% 0;
    margin: 0;
    outline-width: `.concat(s("overlaybadge.outline.width"),`;
    outline-style: solid;
    outline-color: `).concat(s("overlaybadge.outline.color"),`;
}

.p-overlaybadge .p-badge:dir(rtl) {
    transform: translate(-50%, -50%);
}
`)},Me={root:"p-overlaybadge"},Se=w.extend({name:"overlaybadge",theme:$e,classes:Me}),ke={name:"OverlayBadge",extends:k,style:Se,provide:function(){return{$pcOverlayBadge:this,$parentInstance:this}}},R={name:"OverlayBadge",extends:ke,inheritAttrs:!1,components:{Badge:k}};function ze(t,e,s,a,u,o){var r=A("Badge");return h(),B("div",c({class:t.cx("root")},t.ptmi("root")),[$(t.$slots,"default"),n(r,c(t.$props,{pt:t.ptm("pcBadge")}),null,16,["pt"])],16)}R.render=ze;const De={class:"card"},Ae={class:"flex flex-col md:flex-row gap-4"},Te={class:"md:w-1/2"},Ce={class:"md:w-1/2"},Ue={class:"flex flex-col md:flex-row gap-8"},Fe={class:"md:w-1/2"},Pe={class:"card"},Re={class:"flex gap-2"},Xe={class:"flex gap-6"},Ie={class:"flex gap-2"},Ee={class:"flex items-start gap-2"},Ye={class:"card"},qe={class:"card"},He={class:"md:w-1/2"},_e={class:"card"},Ke={class:"flex gap-2"},Ve={class:"flex gap-2"},We={class:"flex gap-2"},Ge={class:"card"},Ne={class:"flex items-center flex-col sm:flex-row"},Oe={class:"flex items-center flex-col sm:flex-row"},je={class:"flex items-center flex-col sm:flex-row"},Ze={class:"card"},Je={class:"rounded-border border border-surface p-6"},Qe={class:"flex mb-4"},et={class:"flex justify-between mt-4"},ut={__name:"MiscDoc",setup(t){const e=W(0);let s=null;function a(){s=setInterval(()=>{let o=e.value+Math.floor(Math.random()*10)+1;o>=100&&(o=100),e.value=o},2e3)}function u(){clearInterval(s),s=null}return G(()=>{a()}),N(()=>{u()}),(o,r)=>{const b=ee,m=k,x=R,z=T,p=Q,X=P,I=F,E=U,l=Z,d=j,v=C;return h(),B(O,null,[i("div",De,[r[0]||(r[0]=i("div",{class:"font-semibold text-xl mb-4"},"ProgressBar",-1)),i("div",Ae,[i("div",Te,[n(b,{value:e.value},null,8,["value"])]),i("div",Ce,[n(b,{value:50,showValue:!1})])])]),i("div",Ue,[i("div",Fe,[i("div",Pe,[r[4]||(r[4]=i("div",{class:"font-semibold text-xl mb-4"},"Badge",-1)),i("div",Re,[n(m,{value:2}),n(m,{value:8,severity:"success"}),n(m,{value:4,severity:"info"}),n(m,{value:12,severity:"Warn"}),n(m,{value:3,severity:"danger"})]),r[5]||(r[5]=i("div",{class:"font-semibold my-4"},"Overlay",-1)),i("div",Xe,[n(x,{value:"2"},{default:f(()=>r[1]||(r[1]=[i("i",{class:"pi pi-bell",style:{"font-size":"2rem"}},null,-1)])),_:1}),n(x,{value:"4",severity:"danger"},{default:f(()=>r[2]||(r[2]=[i("i",{class:"pi pi-calendar",style:{"font-size":"2rem"}},null,-1)])),_:1}),n(x,{severity:"danger"},{default:f(()=>r[3]||(r[3]=[i("i",{class:"pi pi-envelope",style:{"font-size":"2rem"}},null,-1)])),_:1})]),r[6]||(r[6]=i("div",{class:"font-semibold my-4"},"Button",-1)),i("div",Ie,[n(z,{label:"Emails",badge:"8",class:"mr-2"}),n(z,{label:"Messages",icon:"pi pi-users",severity:"warn",badge:"8",badgeClass:"p-badge-danger"})]),r[7]||(r[7]=i("div",{class:"font-semibold my-4"},"Sizes",-1)),i("div",Ee,[n(m,{value:2}),n(m,{value:4,size:"large",severity:"warn"}),n(m,{value:6,size:"xlarge",severity:"success"})])]),i("div",Ye,[r[8]||(r[8]=i("div",{class:"font-semibold text-xl mb-4"},"Avatar",-1)),r[9]||(r[9]=i("div",{class:"font-semibold mb-4"},"Group",-1)),n(X,null,{default:f(()=>[n(p,{image:"/demo/images/avatar/amyelsner.png",size:"large",shape:"circle"}),n(p,{image:"/demo/images/avatar/asiyajavayant.png",size:"large",shape:"circle"}),n(p,{image:"/demo/images/avatar/onyamalimba.png",size:"large",shape:"circle"}),n(p,{image:"/demo/images/avatar/ionibowcher.png",size:"large",shape:"circle"}),n(p,{image:"/demo/images/avatar/xuxuefeng.png",size:"large",shape:"circle"}),n(p,{label:"+2",shape:"circle",size:"large",style:{"background-color":"#9c27b0",color:"#ffffff"}})]),_:1}),r[10]||(r[10]=i("div",{class:"font-semibold my-4"},"Label - Circle",-1)),n(p,{label:"P",class:"mr-2",size:"xlarge",shape:"circle"}),n(p,{label:"V",class:"mr-2",size:"large",style:{"background-color":"#2196F3",color:"#ffffff"},shape:"circle"}),n(p,{label:"U",class:"mr-2",style:{"background-color":"#9c27b0",color:"#ffffff"},shape:"circle"}),r[11]||(r[11]=i("div",{class:"font-semibold my-4"},"Icon - Badge",-1)),n(x,{value:"4",severity:"danger",class:"inline-flex"},{default:f(()=>[n(p,{label:"U",size:"xlarge"})]),_:1})]),i("div",qe,[r[13]||(r[13]=i("div",{class:"font-semibold text-xl mb-4"},"ScrollTop",-1)),n(E,{style:{width:"250px",height:"200px"}},{default:f(()=>[r[12]||(r[12]=i("p",null," Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Vitae et leo duis ut diam. Ultricies mi quis hendrerit dolor magna eget est lorem. Amet consectetur adipiscing elit ut. Nam libero justo laoreet sit amet. Pharetra massa massa ultricies mi quis hendrerit dolor magna. Est ultricies integer quis auctor elit sed vulputate. Consequat ac felis donec et. Tellus orci ac auctor augue mauris. Semper feugiat nibh sed pulvinar proin gravida hendrerit lectus a. Tincidunt arcu non sodales neque sodales. Metus aliquam eleifend mi in nulla posuere sollicitudin aliquam ultrices. Sodales ut etiam sit amet nisl purus. Cursus sit amet dictum sit amet. Tristique senectus et netus et malesuada fames ac turpis egestas. Et tortor consequat id porta nibh venenatis cras sed. Diam maecenas ultricies mi eget mauris. Eget egestas purus viverra accumsan in nisl nisi. Suscipit adipiscing bibendum est ultricies integer. Mattis aliquam faucibus purus in massa tempor nec. ",-1)),n(I,{target:"parent",threshold:100,icon:"pi pi-arrow-up"})]),_:1})])]),i("div",He,[i("div",_e,[r[14]||(r[14]=i("div",{class:"font-semibold text-xl mb-4"},"Tag",-1)),r[15]||(r[15]=i("div",{class:"font-semibold mb-4"},"Default",-1)),i("div",Ke,[n(l,{value:"Primary"}),n(l,{severity:"success",value:"Success"}),n(l,{severity:"info",value:"Info"}),n(l,{severity:"warn",value:"Warn"}),n(l,{severity:"danger",value:"Danger"})]),r[16]||(r[16]=i("div",{class:"font-semibold my-4"},"Pills",-1)),i("div",Ve,[n(l,{value:"Primary",rounded:!0}),n(l,{severity:"success",value:"Success",rounded:!0}),n(l,{severity:"info",value:"Info",rounded:!0}),n(l,{severity:"warn",value:"Warn",rounded:!0}),n(l,{severity:"danger",value:"Danger",rounded:!0})]),r[17]||(r[17]=i("div",{class:"font-semibold my-4"},"Icons",-1)),i("div",We,[n(l,{icon:"pi pi-user",value:"Primary"}),n(l,{icon:"pi pi-check",severity:"success",value:"Success"}),n(l,{icon:"pi pi-info-circle",severity:"info",value:"Info"}),n(l,{con:"pi pi-exclamation-triangle",severity:"warn",value:"Warn"}),n(l,{icon:"pi pi-times",severity:"danger",value:"Danger"})])]),i("div",Ge,[r[18]||(r[18]=i("div",{class:"font-semibold text-xl mb-4"},"Chip",-1)),r[19]||(r[19]=i("div",{class:"font-semibold mb-4"},"Basic",-1)),i("div",Ne,[n(d,{label:"Action",class:"mr-2 mb-2"}),n(d,{label:"Comedy",class:"mr-2 mb-2"}),n(d,{label:"Mystery",class:"mr-2 mb-2"}),n(d,{label:"Thriller",removable:!0,class:"mb-2"})]),r[20]||(r[20]=i("div",{class:"font-semibold my-4"},"Icon",-1)),i("div",Oe,[n(d,{label:"Apple",icon:"pi pi-apple",class:"mr-2 mb-2"}),n(d,{label:"Facebook",icon:"pi pi-facebook",class:"mr-2 mb-2"}),n(d,{label:"Google",icon:"pi pi-google",class:"mr-2 mb-2"}),n(d,{label:"Microsoft",icon:"pi pi-microsoft",removable:!0,class:"mb-2"})]),r[21]||(r[21]=i("div",{class:"font-semibold my-4"},"Image",-1)),i("div",je,[n(d,{label:"Amy Elsner",image:"/demo/images/avatar/amyelsner.png",class:"mr-2 mb-2"}),n(d,{label:"Asiya Javayant",image:"/demo/images/avatar/asiyajavayant.png",class:"mr-2 mb-2"}),n(d,{label:"Onyama Limba",image:"/demo/images/avatar/onyamalimba.png",class:"mr-2 mb-2"})])]),i("div",Ze,[r[22]||(r[22]=i("div",{class:"font-semibold text-xl mb-4"},"Skeleton",-1)),i("div",Je,[i("div",Qe,[n(v,{shape:"circle",size:"4rem",class:"mr-2"}),i("div",null,[n(v,{width:"10rem",class:"mb-2"}),n(v,{width:"5rem",class:"mb-2"}),n(v,{height:".5rem"})])]),n(v,{width:"100%",height:"150px"}),i("div",et,[n(v,{width:"4rem",height:"2rem"}),n(v,{width:"4rem",height:"2rem"})])])])])])],64)}}};export{ut as default};
