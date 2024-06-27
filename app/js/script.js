const del = document.querySelectorAll(".js-del");

for(const element of del){
    element.addEventListener('click', function(event) {confirm("Cette action est irréversible, êtes-vous sûr.e de vouloir suprimmer cette tâche ?")});
}