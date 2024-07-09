const del = document.querySelectorAll(".js-del");

for(const element of del){
    element.addEventListener('click', function(e) {
        if(confirm("Cette action est irréversible, êtes-vous sûr.e de vouloir suprimmer cette tâche ?")){
            callAPIDelete('delete&i =' + element.dataset.idDelete + '&token=' + document.getElementById('token').value);
        }});
}



async function callAPIDelete(params) {
    try {
        const response = await fetch("api.php?action=" + params);
        const json = await response.json();
        document.querySelector("[data-id-delete='" + json.id + "']").remove;
    }
    catch(error) {
        console.error("Unable to load todolist datas from the server : " + error);
    }
}