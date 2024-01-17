"use strict";(self.webpackChunk=self.webpackChunk||[]).push([["mto-order-picking"],{465:(e,t,i)=>{var r,n,c,s=i(6285),o=i(9068),a=i(1966);class d extends s.Z{init(){this.registerEventListeners();const e=this;this.el.querySelector("button#order").addEventListener("click",(function(){e.order()}))}registerEventListeners(){const e=this;this.el.querySelector("[data-picking-list-select-all]").addEventListener("click",(function(t){e.setAllProductsChecked(t.target.checked),e.el.querySelectorAll("[data-picking-list] input[type=checkbox]").forEach((function(e){e.checked=t.target.checked}))}));this.el.querySelectorAll("[data-picking-list]").forEach((function(t){const i=t.dataset.pickingListNumber;t.querySelectorAll("input[type=checkbox]").forEach((t=>{t.addEventListener("change",(function(t){const i=t.target.parentElement.dataset.pickingListNumber;e.setPickingListProductsChecked(i,t.target.checked)}))})),t.querySelectorAll("button").forEach((t=>{t.addEventListener("click",(function(t){e.setPickingListProductsVisible(i,t.target.ariaPressed)}))}))}))}setPickingListProductsChecked(e,t=!0){this.el.querySelectorAll('div[data-picking-product][data-picking-list-number="'+e+'"] input[type=checkbox]').forEach((function(e){e.checked=t}))}setAllProductsChecked(e=!0){this.el.querySelectorAll("div[data-picking-product] input[type=checkbox]").forEach((function(t){t.checked=e}))}setPickingListProductsVisible(e,t="true"){const i="true"===t?"visible":"hidden",r="true"===t?"hidden":"visible";this.el.querySelectorAll('[data-picking-product][data-picking-list-number="'+e+'"]').forEach((e=>{e.classList.add(i),e.classList.remove(r)}))}order(){const e=this.el.querySelectorAll("[data-picking-product]:has(input:checked)"),t=[];e.forEach((function(e){t.push({referencedId:e.dataset.productId,pickingListNumber:e.dataset.pickingListNumber,quantity:e.querySelector("input.product-quantity").value,netPrice:e.querySelector("input.product-net-price").value,grossPrice:e.querySelector("input.product-gross-price").value})}));const i={billing:this.el.querySelector("select[data-billing-address] option:checked").dataset.address,shipping:this.el.querySelector("select[data-shipping-address] option:checked").dataset.address},r={};this.el.querySelectorAll("input:not([type=checkbox])[data-shipping-option],textarea[data-shipping-option], input[type=checkbox][data-shipping-option]:checked").forEach((function(e){const t=e.dataset.name;r[t]=e.value}));const n=window.router["frontend.account.order-picking-add-to-cart"],c=o.Z.getPluginInstances("OffCanvasCart");a.Z.iterate(c,(e=>{e.openOffCanvas(n,JSON.stringify({redirectTo:"frontend.cart.offcanvas",lineItems:t,addresses:i,options:r}),(()=>{this.$emitter.publish("openOffCanvasCart")}))}))}}r=d,c={pickingLists:[],visiblePickingLists:[]},(n=function(e){var t=function(e,t){if("object"!=typeof e||null===e)return e;var i=e[Symbol.toPrimitive];if(void 0!==i){var r=i.call(e,t||"default");if("object"!=typeof r)return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"==typeof t?t:String(t)}(n="options"))in r?Object.defineProperty(r,n,{value:c,enumerable:!0,configurable:!0,writable:!0}):r[n]=c;window.PluginManager.register("OrderPickingPlugin",d,"[data-order-picking-plugin]")}},e=>{e.O(0,["vendor-node","vendor-shared"],(()=>{return t=465,e(e.s=t);var t}));e.O()}]);