const del = document.querySelectorAll(".js-del");

for(const element of del){
    element.addEventListener('click', function(event) {
        if(!confirm("Cette action est irréversible, êtes-vous sûr.e de vouloir suprimmer cette tâche ?")){
            window.location.href;
        }});
}