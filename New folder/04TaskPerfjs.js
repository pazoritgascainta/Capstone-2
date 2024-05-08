function Display25(){
    var age = parseInt(document.getElementById("25").value);
    let text;
    if (age >= 25) { 
        text = "You met the Requirements";
    } else {
        text = "You don't meet the Required age";
    }
    document.getElementById("id1").innerHTML = text;
}

function Display100(){
    var num = parseInt(document.getElementById("num100").value);
    let text;
    if (num < 100) {
        text = "The number is less than 100";
    } else {
        text = "The number is more than or equal to 100";
    }
    document.getElementById("id2").innerHTML = text;
}

let text = "";
for (let num = 0; num < 31; num++) {
    text += num + "<br>";
}
document.getElementById("id3").innerHTML = text;

let evenNumText = "";
for (var i = 0; i <= 40; i++) {
    if (i % 2 === 0){
        evenNumText += i + "<br>";
    }
}
document.getElementById("id4").innerHTML = evenNumText;

var DecrementText = "";
for (var dec = 40; dec >= 10; dec--) {
    if (dec % 3 === 0) {
        DecrementText += dec + "<br>"
    }
}
document.getElementById("id5").innerHTML = DecrementText;
