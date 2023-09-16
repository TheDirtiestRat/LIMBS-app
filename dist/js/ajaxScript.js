function requestResponseOutputAjax(elem_id, src_php, param) {
    // var parameters = param;

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById(elem_id).innerHTML = this.responseText;
            console.log("Ajax is called");
        }
    };

    xmlhttp.open("POST", src_php, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(param);
}

function createParameterString(parameters) {
    // Create parameter string
    var parameterString = "";
    var isFirst = true;
    for (var index in parameters) {
        if (!isFirst) {
            parameterString += "&";
        }
        parameterString += encodeURIComponent(index) + "=" + encodeURIComponent(parameters[index]);
        isFirst = false;
    }

    return parameterString;
}