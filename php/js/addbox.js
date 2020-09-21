function addFunction() {
  var box = document.getElementById("textbox");
  var arr = ["textboxfiledname"];
  for (i = 0; i < 2; i++) {
    var x = row.insertCell(i);
    if (i == 1) {
      x.innerHTML =
        "<input type='button' onclick='removeCell(" +
        row.id +
        ")' value=Delete>";
    } else {
      x.innerHTML =
        "<label>" +
        arr[i] +
        ":</label><input type='textbox' name='" +
        arr[i] +
        "'>";
    }
  }
}

function removeCell(rowid) {
  var table = document.getElementById(rowid).remove();
}
