
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body{color:#6f6969}
        .validator-box{transform:translate(-50%);position:fixed;top:25%;left:50%;box-shadow: 0 0 5px #ddd;padding: 10px 15px;border-radius: 10px;box-sizing:border-box;}
        .validator-box > *{margin-bottom:10px;box-sizing:border-box;}
        input{display: block;width:100%;border-radius: 5px;padding: 5px;border: solid #ddd 2px;}
        input:focus{box-shadow:0px 0 2px #1160db}
        input#sub{background-color:#2679f9;color:#fff;padding:5px 10px;border-radius:20px;border:none;transition:ease-in-out .2s}
        input#sub:hover{background-color:#2679f9bf}
    </style>
    <title>Document</title>
</head>
<body>
    <div class="validator-box">
        <h2>Produect validator</h2>
        <input id="serial" type="text" placeholder="Enter produect serial number">
        <input type="submit" value="Submit" id="sub">
        <div style="text-align:center" id="res"></div>
    </div>
    <script>
        document.querySelector(".validator-box input#sub").onclick = function(e) {
            var s = document.querySelector(".validator-box input#serial").value.trim();
            document.querySelector(".validator-box div#res").textContent = "";
            var xhr = new XMLHttpRequest;
            xhr.onreadystatechange = function() {
                if(this.readyState == 4 && this.status == 200) {
                    document.querySelector(".validator-box div#res").textContent = this.responseText;
                }
            }
            xhr.open("GET", "https://apix5130.000webhostapp.com/pchecker/?p="+s, true);
            xhr.setRequestHeader("Access-Control-Allow-Origin", "https://apix5130.000webhostapp.com");
            xhr.send();
        }
    </script>
</body>
</html>