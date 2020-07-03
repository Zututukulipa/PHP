function fileSelected() {
    let fileName = document.getElementById('fileToUpload').files[0].name;
    document.getElementById('fileLbl').innerText = fileName;
}