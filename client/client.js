const county = document.getElementById("megye")
const outputTable = document.getElementById("output")
const countySelect = document.getElementById("megyeSel")
const okButton = document.getElementById("ok")
let delButtons = []
let modButtons = []
let nameBars = []
let zipBars = []

const urlBase = "http://localhost:8000"

document.addEventListener("DOMContentLoaded", () =>{
    DynamicallyPopulateTheCountySelect(countySelect)
})

function FindBar(id, name){
    if(name == "name"){
        for(let i = 0; i < nameBars.length;i++){
            if(nameBars[i].id == id){
                return nameBars[i]
            }
        }
    }
    else{
        for(let i = 0; i < zipBars.length;i++){
            if(zipBars[i].id == id){
                return zipBars[i]
            }
        }
    }
    
}

function HideOthers(id, name){
    if(name == "name"){
        for(let i = 0;i<nameBars.length;i++){
            if(nameBars[i].id != id){
                nameBars[i].classList.add("hidden")
            }
        }
    }
    else if(name == "zip"){
        for(let i = 0;i<zipBars.length;i++){
            if(zipBars[i].id != id){
                zipBars[i].classList.add("hidden")
            }
        }
    }
}

async function ListenForDelete(type){
    delButtons.forEach((item) =>{
        item.addEventListener("click", () =>{
            let url = urlBase + "/"+type+"/"+item.value
            fetch(url, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json"
                }
        
            })
            .then(response =>{
                if(!response.ok){
                    throw new Error('Network response was not ok')
                }
                return response.json()
            })
            .then(data=>{            
                if(data.message = "OK"){
                    alert("Törlés sikeres")
                }
                else{
                    alert("Törlés sikertelen")
                }
                return data
            })
            .catch(error => {
                console.error('Error:', error);
            })
        })
    })
}

async function ListenForMod(type) {
    modButtons.forEach((item) =>{
        item.addEventListener("click", () =>{
            if(type == "counties"){
                let bar = FindBar("mod_name_"+item.value, "name")
                bar.classList.remove("hidden")
                HideOthers("mod_name_"+item.value, "name")
                if(bar.value != ""){
                    let modData = {
                        "name": bar.value
                    }
                    let url = urlBase + "/counties/"+item.value
                    fetch(url, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(modData)
                    })
                    .then(response => {
                        if(!response.ok){
                            throw new Error('Network response was not ok')
                        }
                        return response.json()
                    })
                    .then(data =>{
                        console.log(data)
                    })
                }
            }
            else if(type == "cities"){
                let nameBar = FindBar("mod_name_"+item.value, "name")
                nameBar.classList.remove("hidden")
                let zipBar = FindBar("mod_zip_"+item.value, "zip")
                zipBar.classList.remove("hidden")
                HideOthers("mod_zip_"+item.value, "zip")
                HideOthers("mod_name_"+item.value, "name")
                if(nameBar.value != ""){
                    let modData = {
                        "city": nameBar.value
                    }
                    let url = urlBase + "/cities/"+item.value
                    fetch(url, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(modData)
                    })
                    .then(response => {
                        if(!response.ok){
                            throw new Error('Network response was not ok')
                        }
                        return response.json()
                    })
                    .then(data =>{
                        console.log(data)
                    })
                }
                if(zipBar.value != ""){
                    let modData = {
                        "zip_code": zipBar.value
                    }
                    let url = urlBase + "/cities/"+item.value
                    fetch(url, {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(modData)
                    })
                    .then(response => {
                        if(!response.ok){
                            throw new Error('Network response was not ok')
                        }
                        return response.json()
                    })
                    .then(data =>{
                        console.log(data)
                    })
                }
            }
        })
    })
}

async function DynamicallyPopulateTheCountySelect(theSelect){
    let url = urlBase + "/counties";
    
    fetch(url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        }

    })
    .then(response =>{
        if(!response.ok){
            throw new Error('Network response was not ok')
        }
        return response.json()
    })
    .then(data=>{            
        for(let i = 0; i < data.data.length;i++){
            let opt = document.createElement("option")
            opt.value = data.data[i].id
            opt.innerText = data.data[i].name
            theSelect.appendChild(opt)
        }
        return data
    })
    .catch(error => {
        console.error('Error:', error);
    })
}

function GenLayout(data, type){
    
    outputTable.innerText = ''
    let th1 = document.createElement('th')
    th1.innerText = "#"
    let th2 = document.createElement('th')
    th2.innerText = "Megnevezés"
    let row = document.createElement('tr')
    row.appendChild(th1)
    row.appendChild(th2)
    outputTable.appendChild(row)

    for(let i = 0; i < data.data.length;i++){
        let del = document.createElement("button")
        del.id = "del_"+i;
        del.innerText = "Törlés"
        del.value = data.data[i].id;
        delButtons.push(del)

        let mod = document.createElement("button")
        mod.id = "mod_"+i;
        mod.innerText = "Módosítás"
        mod.value = data.data[i].id;
        modButtons.push(mod)

        let row = document.createElement("tr")

        if(type == "counties"){
            let col1 = document.createElement("td")
            col1.innerText = data.data[i].id;
            let col2 = document.createElement("td")
            col2.innerText = data.data[i].name;

            let nameBar = document.createElement("input")
            nameBar.type = "text"
            nameBar.classList.add("hidden")
            nameBar.placeholder = "Új megye név"
            nameBar.id = "mod_name_"+data.data[i].id
            nameBars.push(nameBar)

            col2.appendChild(nameBar)

            row.appendChild(col1);
            row.appendChild(col2);
        }
        else if(type == "cities"){
            let col1 = document.createElement("td")
            col1.innerText = data.data[i].id;
            let col2 = document.createElement("td")
            col2.innerText = data.data[i].zip_code;
            let col3 = document.createElement("td")
            col3.innerText = data.data[i].city;

            let nameBar = document.createElement("input")
            nameBar.type = "text"
            nameBar.classList.add("hidden")
            nameBar.placeholder = "Új város név"
            nameBar.id = "mod_name_"+data.data[i].id
            nameBars.push(nameBar)

            let zipBar = document.createElement("input")
            zipBar.type = "text"
            zipBar.classList.add("hidden")
            zipBar.placeholder = "Új irányítószám"
            zipBar.id = "mod_zip_"+data.data[i].id
            zipBars.push(zipBar)

            col2.appendChild(zipBar)
            col3.appendChild(nameBar)

            row.appendChild(col1);
            row.appendChild(col2);
            row.appendChild(col3)
        }
        row.appendChild(mod)
        row.appendChild(del)
        outputTable.appendChild(row); 
    }
    ListenForDelete(type)  
    ListenForMod(type)
}


okButton.addEventListener("click", async () =>{
    let countyId = countySelect.options[countySelect.selectedIndex].value;
    let url = urlBase + "/counties/"+countyId+"/cities";
    
    fetch(url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        }

    })
    .then(response =>{
        if(!response.ok){
            throw new Error('Network response was not ok')
        }
        return response.json()
    })
    .then(data=>{            
        console.log(data)
        GenLayout(data, "cities")
        return data
    })
    .catch(error => {
        console.error('Error:', error);
    })
})

county.addEventListener("click", async () =>{
    let url = urlBase + "/counties";
    
    fetch(url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        }

    })
    .then(response =>{
        if(!response.ok){
            throw new Error('Network response was not ok')
        }
        return response.json()
    })
    .then(data=>{            
        console.log(data)
        GenLayout(data, "counties")
        return data
    })
    .catch(error => {
        console.error('Error:', error);
    })
    
})

