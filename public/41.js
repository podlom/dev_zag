(window.webpackJsonp=window.webpackJsonp||[]).push([[41],{60:function(a,t,s){"use strict";s.r(t);var n={name:"companycard",data:function(){return{company:this.dataCompany,favorite_text:favorite_text}},props:{dataCompany:{required:!0}},methods:{addToFavorites:function(a,t){this.$emit("add-to-favorites",a,t)},addToNotifications:function(a,t){this.$emit("add-to-notifications",a,t)}},watch:{dataCompany:{handler:function(a){this.company=a},deep:!0}}},i=s(2),o=Object(i.a)(n,(function(){var a=this,t=a.$createElement,s=a._self._c||t;return s("li",{staticClass:"best-company-info__item"},[s("div",{staticClass:"best-company-info__header"},[s("div",{staticClass:"info"},[s("h4",[s("a",{attrs:{href:a.company.link}},[a._v(a._s(a.company.name))])]),a._v(" "),s("a",{attrs:{href:a.company.link}},[a._v(a._s(a.company.category_name))])]),a._v(" "),s("div",{staticClass:"best-company-info__button-list"},[s("button",{staticClass:"best-company-info__button",class:{active:a.$parent.favorites.companies.includes(a.company.id)||a.$parent.favorites.companies.includes(a.company.original_id)},attrs:{title:a.favorite_text},on:{click:function(t){return a.addToFavorites(a.company,"companies")}}},[s("span",{staticClass:"icon-heart-outline"})]),a._v(" "),1==a.company.category_id||18==a.company.category_id?s("button",{staticClass:"best-company-info__button",class:{active:a.$parent.notifications.companies.includes(a.company.id)||a.$parent.notifications.companies.includes(a.company.original_id)},on:{click:function(t){return a.addToNotifications(a.company,"companies")}}},[s("span",{staticClass:"icon-bell-outline"})]):a._e()])]),a._v(" "),s("div",{staticClass:"best-company-info__footer"},[s("ul",{staticClass:"best-company-info__contact-list"},[s("li",{staticClass:"best-company-info__contact-item"},[s("span",{staticClass:"icon-web"}),a._v(" "),s("span",{staticClass:"text"},[a._v(a._s(a.company.contacts.site?a.company.contacts.site:"н.д."))])]),a._v(" "),s("li",{staticClass:"best-company-info__contact-item"},[s("span",{staticClass:"icon-phone-outline"}),a._v(" "),s("span",{staticClass:"text"},[a._v(a._s(a.company.contacts.phone?a.company.contacts.phone:"н.д."))])]),a._v(" "),s("li",{staticClass:"best-company-info__contact-item"},[s("span",{staticClass:"icon-map-marker-outline"}),a._v(" "),s("span",{staticClass:"text"},[a._v(a._s(a.company.city?a.company.city:"н.д."))])])]),a._v(" "),s("div",{staticClass:"wrapper"},[s("ul",{staticClass:"best-company-info__social-list"},[a.company.contacts.fb?s("li",{staticClass:"best-company-info__social-item"},[s("a",{staticClass:"best-company-info___social-link",attrs:{href:a.company.contacts.fb}},[s("span",{staticClass:"icon-facebook"})])]):a._e(),a._v(" "),a.company.contacts.inst?s("li",{staticClass:"best-company-info__social-item"},[s("a",{staticClass:"best-company-info___social-link",attrs:{href:a.company.contacts.inst}},[s("span",{staticClass:"icon-instagram"})])]):a._e()]),a._v(" "),s("div",{staticClass:"best-company-info__img"},[s("a",{attrs:{href:a.company.link}},[s("img",{directives:[{name:"lazy",rawName:"v-lazy",value:a.company.business_card,expression:"company.business_card"}],attrs:{alt:"Фото: "+a.company.name,title:"Картинка: "+a.company.name}})])])])])])}),[],!1,null,null,null);t.default=o.exports}}]);
//# sourceMappingURL=41.js.map