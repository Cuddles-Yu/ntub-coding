/*document.querySelector("html").onclick = function () {
    alert("Don't");
};
*/

let myImage = document.querySelector("img"); //對應HTML的IMG

myImage.onclick = function () {
    let mysrc = myImage.getAttribute("src"); //取得img中src的屬性
    if (mysrc === "images/google-icon.png") { //這邊寫對檔案類型
        myImage.setAttribute("src", "images/NTUB.jpg");
    } else {
        myImage.setAttribute("src", "images/google-icon.png");
    }
};

let myButton = document.querySelector("button");
let myHeading = document.querySelector("h1");

function setUserName() {
    let myName = prompt("enter your name:");
    localStorage.setItem("name", myName);
    myHeading.innerHTML = "歡迎" + myName;
}

if (!localStorage.getItem("name")) {
    setUserName();
} else {
    let name = localStorage.getItem("name");
    myHeading.innerHTML = "歡迎," + myName;
}

myButton.onclick = function () {
    setUserName();
};