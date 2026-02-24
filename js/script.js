"use strict";
document.addEventListener('DOMContentLoaded', function(){
        // modal 
        document.addEventListener('click', function (e) {
        if (!e.target.matches('[data-show-modal]')) return;
        else{
                e.preventDefault();
                var modal = document.querySelectorAll('#'+e.target.dataset.id);
                Array.prototype.forEach.call(modal, function (el) {
                        el.classList.add('active');
                });
        }
        });
        document.addEventListener('click', function (e) {
                if (!e.target.matches('[data-close-modal]')) return;
                else{
                e.target.closest('.modal').classList.remove('active');
                }
        });
})
