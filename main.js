const icone= document.querySelector('.icone') 
const ul = document.querySelector(".nav-links"); 
icone.addEventListener('click',()=>{
    ul.classList.toggle('mobile')
})
console.log(icone);
