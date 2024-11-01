const firstNumber = document.getElementById("firstNumber");
const secondNumber = document.getElementById("secondNumber");
const result = document.getElementById("result");
const submit = document.querySelector(".wpcf7-submit");
const wpcf7tc = document.querySelector(".wpcf7");
const cf7tcContainer = document.querySelector(".cf7tc_container");

if (wpcf7tc.contains(cf7tcContainer)) {
    submit.setAttribute("disabled", '');
}

const firstRandNum = Math.floor(Math.random() * 20) + 1;
const secondRandNum = Math.floor(Math.random() * 20) + 1;

firstNumber.innerHTML = firstRandNum;
secondNumber.innerHTML = secondRandNum;

const sum = firstRandNum + secondRandNum;

window.onmouseover = () => {
    if (sum == result.value) {
        submit.style.cursor = "pointer";
        submit.disabled = false;
        submit.disabled.opactiy = "1";
        result.setAttribute("disabled", '');
    }
}

//Regenerte everything
document?.addEventListener('wpcf7mailsent', function (event) {
    result.disabled = false;

    if (wpcf7tc.contains(cf7tcContainer)) {
        submit.setAttribute("disabled", '');
    }
    submit.style.cursor = "not-allowed";

    const firstRandNum = Math.floor(Math.random() * 20) + 1;
    const secondRandNum = Math.floor(Math.random() * 20) + 1;

    firstNumber.innerHTML = firstRandNum;
    secondNumber.innerHTML = secondRandNum;

    const sum = firstRandNum + secondRandNum;

    window.onmouseover = () => {
        if (sum == result.value) {
            submit.style.cursor = "pointer";
            submit.disabled = false;
            submit.disabled.opactiy = "1";
            result.setAttribute("disabled", '');
        }
    }


}, false);