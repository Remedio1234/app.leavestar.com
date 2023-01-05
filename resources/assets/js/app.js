/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
Vue.use(require('vue-full-calendar'));


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */
//Vue.component('example', require('./components/Example.vue'));
Vue.component('org-tree', require('./components/OrgTree.vue'));
Vue.component('tree-node', require('./components/TreeNode.vue'));
Vue.component('tree-subling', require('./components/SiblingTree.vue'));
Vue.component('leavestar-calendar', require('./components/Calendar.vue'));


window.app = new Vue({
    el: 'body',
});

$(function () {
    $(document.body).on("click", ".toggle-menu", function () {
        $(document.body).toggleClass("mobile-nav-open");
    });

    $('body').on('submit', 'form', function () {
        $(this).find('input[type="submit"]').attr('disabled', 'disabled').addClass('disabled');

    });
});
