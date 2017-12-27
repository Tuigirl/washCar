///用于核查是否选择全部数据
function GetCheckID() {
    var ival = ''
    document.getElementById('Hidden1').value = '';
    var allInput = document.getElementsByTagName("input");
    var loopTime = allInput.length;
    for (i = 0; i < loopTime; i++) {
        if ((allInput[i].type == "checkbox") && (allInput[i].checked == true) && allInput[i].name != "CheckBox1") {
            ival += allInput[i].title + ',';
        }
    }
    document.getElementById('Hidden1').value = ival;
}



//全选
function selectAll(obj) {
    var allInput = document.getElementsByTagName("input");
    //alert(allInput.length);
    var loopTime = allInput.length;
    for (i = 0; i < loopTime; i++) {
        //alert(allInput[i].type);
        if (allInput[i].type == "checkbox") {
            allInput[i].checked = obj.checked;
        }
    }
}

//单选框删除
function radio_del() {
    var allInput = document.getElementsByTagName("input");
    var loopTime = allInput.length;
    for (i = 0; i < loopTime; i++) {
        if ((allInput[i].type == "radio") && (allInput[i].checked == true)) {
            document.getElementById('HiddenRadio').value = allInput[i].title;
            if (confirm("确定要删除吗？")) {
                return true;
            }
            else {
                return false;
            }
        }
    }
    
    alert("请选择一项！");
    return false;
}

//单选框编辑
function radio_edit() {
    var allInput = document.getElementsByTagName("input");
    var loopTime = allInput.length;
    for (i = 0; i < loopTime; i++) {
        if ((allInput[i].type == "radio") && (allInput[i].checked == true)) {
            document.getElementById('HiddenRadio').value = allInput[i].title;
            return true;
        }
    }
    alert("请选择一项！");
    return false;
}


//单选框
function radioCheck() {
    var allInput = document.getElementsByTagName("input");
    var loopTime = allInput.length;
    for (i = 0; i < loopTime; i++) {
        if ((allInput[i].type == "radio") && (allInput[i].checked == true)) {
            document.getElementById('HiddenRadio').value = allInput[i].title;
        }
    }
}

//单选框
function radioCheck1() {
    var allInput = document.getElementsByTagName("input");
    var loopTime = allInput.length;
    for (i = 0; i < loopTime; i++) {
        if ((allInput[i].type == "radio") && (allInput[i].checked == true)) {
            document.getElementById('HiddenRadio').value = allInput[i].value;
        }
    }
}