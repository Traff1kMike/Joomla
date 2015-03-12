/*
---
description:     
  - MultiSelect is a MooTools plugin that turns your checkbox set into one single multi-select dropdown menu. MultiSelect is also completely CSS skinnable.

authors:
  - Blaž Maležič (http://twitter.com/blazmalezic)

version:
  - 1.3.1

license:
  - MIT-style license

requires:
  core/1.2.1:   '*'

provides:
  - MultiSelect
...
*/
var MultiSelect=new Class({Implements:[Options],options:{boxes:"input[type=checkbox]",labels:"label",monitorText:" selected",containerClass:"MultiSelect",monitorClass:"monitor",monitorActiveClass:"active",itemSelectedClass:"selected",itemHoverClass:"hover"},initialize:function(a,b){this.setOptions(b);this.active=false;this.action="open";this.state="closed";this.elements=document.getElements(a);this.elements.each(function(a){this.buildMenu(a)},this)},buildMenu:function(a){var b=this;a.addClass(b.options.containerClass);var c=a.getElements(b.options.boxes);var d=a.getElements(b.options.labels);var e=new Element("ul",{styles:{display:"none"},events:{mouseenter:function(){b.action="open"},mouseleave:function(){b.action="close";b.itemHover(this,"none")},mousedown:function(a){a.stop()},selectstart:function(){return false},keydown:function(a){if(a.key=="esc"){b.toggleMenu("close",f,this)}else{if(a.key=="down"||a.key=="up"){b.itemHover(this,a.key)}}}}});c.each(function(a,c){a.addEvents({click:function(a){a.stop()},keydown:function(a){if(a.key=="space"){b.active=true;b.changeItemState(this.getParent(),this,f)}if(b.active&&(a.key=="down"||a.key=="up")){b.changeItemState(this.getParent(),this,f)}},keyup:function(a){if(a.key=="space"){b.active=false}}});var g=d[c];(new Element("li",{"class":a.get("checked")?b.options.itemSelectedClass:"",events:{mouseenter:function(){if(b.active===true){b.changeItemState(this,a,f)}b.itemHover(e,this)},mousedown:function(){b.active=true;b.changeItemState(this,a,f)}}})).adopt([a,g]).inject(e)});var f=new Element("div",{"class":b.options.monitorClass,html:"<div><div>"+b.changeMonitorValue(e)+"</div></div>",tabindex:0,events:{mouseenter:function(){b.action="open"},mouseleave:function(){b.action="close"},click:function(){if(this.hasClass(b.options.monitorActiveClass)){b.toggleMenu("close",f,e)}else{b.toggleMenu("open",f,e)}},keydown:function(a){if(a.key=="space"||a.key=="down"||a.key=="up"){b.action="close";b.toggleMenu("open",f,e)}},mousedown:function(a){a.stop()},selectstart:function(){return false}}});document.addEvents({mouseup:function(){b.active=false},click:function(){if(b.action=="close"){b.toggleMenu("close",f,e)}},keydown:function(a){if(a.key=="esc"){b.toggleMenu("close",f,e);b.itemHover(e,"none")}if(b.state=="opened"&&(a.key=="down"||a.key=="up")){a.stop()}}});a.empty().adopt([f,e])},append:function(a){var b=document.getElements(a);this.elements.combine(b);b.each(function(a){this.buildMenu(a)},this)},changeItemState:function(a,b,c){if(a.hasClass(this.options.itemSelectedClass)){a.removeClass(this.options.itemSelectedClass);b.set("checked",false).focus()}else{a.addClass(this.options.itemSelectedClass);b.set("checked",true).focus()}c.set("html","<div><div>"+this.changeMonitorValue(a.getParent())+"</div></div>")},changeMonitorValue:function(a){var b=a.getElements(this.options.boxes).filter(function(a){return a.get("checked")}).length+this.options.monitorText;return b},itemHover:function(a,b){var c=a.getElement("li."+this.options.itemHoverClass);switch(b){case"down":if(c&&(d=c.getNext())){c.removeClass(this.options.itemHoverClass)}else{this.itemHover(a,"last")}break;case"up":if(c&&(d=c.getPrevious())){c.removeClass(this.options.itemHoverClass)}else{this.itemHover(a,"first")}break;case"none":a.getElements("li."+this.options.itemHoverClass).removeClass(this.options.itemHoverClass);break;case"first":var d=a.getFirst();break;case"last":var d=a.getLast();break;default:if(c){c.removeClass(this.options.itemHoverClass)}var d=b;break}if(d){d.addClass(this.options.itemHoverClass).getElement(this.options.boxes).focus()}},toggleMenu:function(a,b,c){if(a=="open"){b.addClass(this.options.monitorActiveClass);c.setStyle("display","");this.itemHover(c,"first");this.state="opened"}else{this.elements.getElement("div.monitor").removeClass(this.options.monitorActiveClass);this.elements.getElement("ul").setStyle("display","none");this.action="open";this.state="closed"}if(c.getScrollSize().y>(c.getStyle("max-height").toInt()?c.getStyle("max-height").toInt():c.getStyle("height").toInt())){c.setStyle("overflow-y","scroll")}}})