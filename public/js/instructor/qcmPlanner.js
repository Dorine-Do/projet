// document.addEventListener('DOMContentLoaded', function(){
//     let session = document.querySelector('#plan_qcm_session');
//     session.addEventListener('change', function(){
//         let form = this.closest('form');
//         let data = {};
//         data[session.getAttribute('name')] = session.value;
//         fetch(form.getAttribute('action'), data)
//             .then( html => {
//                 console.log(html)
//                 document.querySelector('#plan_qcm_module').replaceWith(
//                     document.querySelector(html.responseText).find('#plan_qcm_module')
//                 );
//             })
//     });
// })
//
