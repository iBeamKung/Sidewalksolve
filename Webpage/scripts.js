const navBurger = document.querySelector('.nav-hamburger');
const navSec = document.querySelector('.sec-navbar');
let menuOpen = false;

navBurger.addEventListener('click', () => {
    if(!menuOpen) {
        navBurger.classList.add('open');
        navSec.classList.add('open');
        menuOpen = true;
    }
    else {
        navBurger.classList.remove('open');
        navSec.classList.remove('open');
        menuOpen = false;
    }
});

document.getElementsByClassName('admin_login_page')[0].addEventListener('click', () => {
    document.body.classList.add('stop_scrolling');
    document.getElementsByClassName('main__login')[0].style.display = "flex";
});

document.getElementsByClassName('close_login')[0].addEventListener('click', () => {
    document.body.classList.remove('stop_scrolling');
    document.getElementsByClassName('main__login')[0].style.display = "none";
});

let input = document.querySelectorAll('input');
var clear = document.querySelectorAll('.input__field');
var focused = document.activeElement;

input.forEach(element => {
    element.addEventListener('input', () => {
        let bool = element.value.length
        focused = document.querySelector(":focus");

        clear.forEach(element => {
            try {
                if (element.querySelector('.'.concat(focused.id))) {
                    element.querySelector('.clear').style.display = 'block'
                    element.querySelector('.clear').style.opacity = bool ? 1:0;
                }
            } catch {}
        })
    })
})

const file = document.querySelector('#file-upload');
const file_name = document.getElementById('file-name');
var dataTransferInputFile = new DataTransfer();
var currentNoFile = true;

function PerformFileDelete(e, This) {
    document.querySelectorAll('.invalid-input').forEach(element => {
                element.innerHTML = '';
                element.style.display ="none";
    });
    This.remove();
    dataTransferInputFile.items.remove(e);
    document.querySelector('#file-upload').files = dataTransferInputFile.files;
    document.getElementsByClassName('Attach-files')[0].innerHTML = `Attach files (Total selected files: ${dataTransferInputFile.files.length})`;
    console.log(document.querySelector('#file-upload').files);
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) == true) {
            if (dataTransferInputFile.files.length >= 1) {
                document.querySelector('#file-upload').setAttribute('disabled', 'disabled');
            }
            else {
                document.querySelector('#file-upload').removeAttribute('disabled');
            }
    }
}
file.addEventListener('change', (e) => {
    const [file] = e.target.files;
    var files; var fileSize; var p;
    var { name: fileName, size } = file || {};

    if (`${fileName}` != 'undefined' && `${fileName}` != NaN) {
        document.getElementsByClassName('file-upload')[0].style.removeProperty('box-shadow');
        document.querySelector('#invalid_ext').style.display = "none";
        document.querySelector('#invalid_ext').innerHTML = '';

        if (currentNoFile == true) {
          document.getElementsByClassName('current-no-file')[0].remove();   
          currentNoFile = false;
        }
        
        
        for (var i=0; i<e.target.files.length; i++) {
            files  = e.target.files[i];
            ({fileName, size} = {fileName: files.name, size: files.size});
            console.log(fileName);
            dataTransferInputFile.items.add(files);
            
            if (fileName.length >= 25) {
                let lastIndex = fileName.lastIndexOf('.');
                fileName = fileName.substr(0, 25) + "... " + fileName.substr(lastIndex);
            }
            fileSize = (size / 1000);
            let new_fileSize;
            (fileSize < 1024) ? new_fileSize = fileSize + " KB" : new_fileSize = (size / (1024 * 1024)).toFixed(2) + " MB";

            var fileIconSvg = `<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="image" class="svg-inline--fa fa-image fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm-6 336H54a6 6 0 0 1-6-6V118a6 6 0 0 1 6-6h404a6 6 0 0 1 6 6v276a6 6 0 0 1-6 6zM128 152c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40zM96 352h320v-80l-87.515-87.515c-4.686-4.686-12.284-4.686-16.971 0L192 304l-39.515-39.515c-4.686-4.686-12.284-4.686-16.971 0L96 304v48z"></path></svg>`;

            let indexFileName;
            indexFileName = (file_name.childElementCount == 0) ? indexFileName = 0 : indexFileName = file_name.childElementCount;
            
            p = document.createElement('p');
            p.className = "file";
            
            file_name.appendChild(p);
            file_name.children[indexFileName].appendChild(document.createElement('span')).textContent = `${fileName}`;
            file_name.children[indexFileName].appendChild(document.createElement('span')).textContent = `${new_fileSize}`;
            file_name.children[indexFileName].innerHTML += 
            `<button class="clear-file" onclick="PerformFileDelete(Array.prototype.indexOf.call(this.parentNode.parentNode.children, this.parentNode), this.parentNode);">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><defs><style>.cls-1{fill:#fff;opacity:0;}.cls-2{fill:#646B8C;}</style></defs><title>close</title><g id="Layer_2" data-name="Layer 2"><g id="close"><g id="close-2" data-name="close"><rect class="cls-1" width="24" height="24" transform="translate(24 24) rotate(180)"/><path class="cls-2" d="M13.41,12l4.3-4.29a1,1,0,1,0-1.42-1.42L12,10.59,7.71,6.29A1,1,0,0,0,6.29,7.71L10.59,12l-4.3,4.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L12,13.41l4.29,4.3a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42Z"/></g></g></g></svg>
            </button>`;
            console.log(files);
        }
        console.log(dataTransferInputFile);
        document.getElementsByClassName('Attach-files')[0].innerHTML = `Attach files (Total selected files: ${dataTransferInputFile.files.length})`;
    }
    const parent = document.querySelectorAll('.file');
    parent.forEach(element => {
        if (element.childElementCount == 3) {
            element.innerHTML = fileIconSvg + element.innerHTML;
        }
        });
    e.target.files = dataTransferInputFile.files;
    console.log(document.querySelector('#file-upload').files);
});

if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) == true) {
    file.addEventListener('change', (e) => {
        if (dataTransferInputFile.files.length >= 1) {
            document.querySelector('#file-upload').setAttribute('disabled', 'disabled');
        }
        else {
            document.querySelector('#file-upload').removeAttribute('disabled');
        }
    });
}

const anonymous = document.querySelector('.checkbox');
const {inputFirstname, inputLastname, inputIDNum, inputPhoneNum, inputAddress} = {inputFirstname: document.getElementById('fn'),
                                                                                    inputLastname: document.getElementById('ln'),
                                                                                    inputIDNum: document.getElementById('idn'),
                                                                                    inputPhoneNum: document.getElementById('pn'),
                                                                                    inputAddress: document.getElementById('ua')};

anonymous.addEventListener('change', () => {
    let bool = inputFirstname.disabled;
    inputFirstname.disabled = bool ? false:true;
    inputLastname.disabled = bool ? false:true;
    inputIDNum.disabled = bool ? false:true;
    inputPhoneNum.disabled = bool ? false:true;
    inputAddress.disabled = bool ? false:true;
    clearInput('ln');
    clearInput('fn');
    clearInput('idn');
    clearInput('pn');
    clearInput('ua');

    if (bool === false) {
        document.getElementsByClassName('first_name')[0].setAttribute('style', 'background: #e7e6e3;');
        document.getElementsByClassName('last_name')[0].setAttribute('style', 'background: #e7e6e3;');
        document.getElementsByClassName('id_number')[0].setAttribute('style', 'background: #e7e6e3;');
        document.getElementsByClassName('phone_number')[0].setAttribute('style', 'background: #e7e6e3;');
        document.getElementsByClassName('user_address')[0].setAttribute('style', 'background: #e7e6e3;');
    } 
    else {
        document.getElementsByClassName('first_name')[0].setAttribute('style', 'background: #fff;');
        document.getElementsByClassName('last_name')[0].setAttribute('style', 'background: #fff;');
        document.getElementsByClassName('id_number')[0].setAttribute('style', 'background: #fff;');
        document.getElementsByClassName('phone_number')[0].setAttribute('style', 'background: #fff;');
        document.getElementsByClassName('user_address')[0].setAttribute('style', 'background: #fff;');
    }
})
const inputField = ['first_name', 'last_name', 'id_number', 'phone_number', 'user_address', 'date', 'file-upload', 'description'];

var newFileName;
const uniqidLength = 20;
var newFileName_array = [];

function makeid(length) {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

submitForm = function() {
    var count = 0;
    var invalid_input_value;
    var error_color = `#ffa8a8`;
    const file_extension = ["jpg", "jpeg", "png", "gif", "mp4", "mov", "wmv", "flv"];

    for (var i=0; i < inputField.length; i++) {
        let element = document.getElementById(inputField[i]);

        if (inputField[i] === 'id_number' && (`${element[0].value}`.match(new RegExp(/[0-9]/, "g")) || []).length != 13 && element[0].value != 0) {
            let error_msg = "Identification card number must have 13 digits.";

            document.getElementsByClassName(inputField[i])[0].style = `box-shadow: inset 0 0 0 0.125rem ${error_color};`;
            document.querySelector('#invalid_id').innerHTML = `<div>
                                                                       <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-circle" class="svg-inline--fa fa-exclamation-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zm-248 50c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"></path></svg>
                                                                  </div>
                                                                  <p>${error_msg}</p>`;
            if (window.matchMedia("(min-width: 952px)").matches) {
                document.querySelector('#invalid_phone').style.display = "flex";
            }
            document.querySelector('#invalid_id').style.display = "flex";
            count--;
        }
        else if (inputField[i] === 'phone_number' && (`${element[0].value}`.match(new RegExp(/[0-9]/, "g")) || []).length != 10 && element[0].value != 0) {
            let error_msg = "Phone number must have 10 digits.";

            document.getElementsByClassName(inputField[i])[0].style = `box-shadow: inset 0 0 0 0.125rem ${error_color};`;
            document.querySelector('#invalid_phone').innerHTML = `<div>
                                                                       <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-circle" class="svg-inline--fa fa-exclamation-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zm-248 50c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"></path></svg>
                                                                  </div>
                                                                  <p>${error_msg}</p>`;
            if (window.matchMedia("(min-width: 952px)").matches) {
                document.querySelector('#invalid_id').style.display = "flex";
            }
            document.querySelector('#invalid_phone').style.display = "flex";
            count--;
        }
        // ================== check file extension ================== //
        else if (inputField[i] === 'file-upload'){
            document.querySelectorAll("input").forEach(element => {
                if (element.name === "upload[]") {
                    var invalidFileType = '';
                    for (var i=0; i <= element.files.length-1; i++) {
                        let file_name = element.files.item(i).name;
                        
                        if (file_extension.includes(file_name.slice((file_name.lastIndexOf(".")-1 >>> 0)+2 ).toLowerCase()) != true ) {
                            invalidFileType += (invalidFileType == '') ? file_name : ', ' + file_name;
                        }
                            
                        if (i == element.files.length-1 && invalidFileType != "") {
                            let error_msg = `The specified file <span style="font-weight: bold;">${invalidFileType} </span>has invalid extension. Only files with the following extensions are allow: jpg jpeg png gif for image files and mp4 mov wmv flv for video files.`;
                            document.querySelector('#invalid_ext').innerHTML = `<div>
                                                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-circle" class="svg-inline--fa fa-exclamation-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M504 256c0 136.997-111.043 248-248 248S8 392.997 8 256C8 119.083 119.043 8 256 8s248 111.083 248 248zm-248 50c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"></path></svg>
                                                                                </div>
                                                                                <p>${error_msg}</p>`;
                            document.getElementsByClassName('file-upload')[0].style = `box-shadow: inset 0 0 0 0.125rem ${error_color};`;
                            document.querySelector('#invalid_ext').style.display = "flex";
                            count--;
                        }
                         
                    }
                }
            });
        }
        
        if (element.checkValidity()) {
            count++;
            newFileName_array = [];
            if (count === inputField.length) {
                var json = [];
                var video_count = 0;
                var image_count = 0;
                document.querySelectorAll("form").forEach(f => {
                    let obj = {};
                    f.querySelectorAll("input").forEach(element => {
                        if (element.name === "upload[]") {
                            let video_obj = {};
                            let image_obj = {};
                            for (var i=0; i <= element.files.length-1; i++) {
                                newFileName = makeid(uniqidLength);
                                newFileName_array.push(newFileName);
                                if(file_extension.includes(element.files.item(i).name.slice((element.files.item(i).name.lastIndexOf(".")-1 >>> 0)+2 ).toLowerCase(), 4) == true) {
                                    video_obj['video_src' + `[${video_count}]`] = newFileName + '.' + element.files.item(i).name.split('.').pop();  
                                    video_count++;
                                }
                                else {
                                    image_obj['photo_src' + `[${image_count}]`] = newFileName + '.' + element.files.item(i).name.split('.').pop();
                                    image_count++;
                                }
                            }
                            json.push(video_obj);
                            json.push(image_obj);
                            
                        }
                        else {
                            obj[element.name] = element.value || "";
                        }
                    });

                    f.querySelectorAll("textarea").forEach(element => obj[element.name] = element.value || "");
                    json.push(obj);
                });
                console.log(json);
                var json_format = {
                    "first_name": `${json[1].first_name}`,
                    "last_name": `${json[2].last_name}`,
                    "tel": `${json[4].phone_number}`,
                    "id_number": `${json[3].id_number}`,
                    "address": `${json[5].user_address}`,
                    "date": `${json[6].date}`,
                    "description": `${json[10].description}`,
                    "photo_src": `${JSON.stringify(json[8])}`,
                    "video_src": `${JSON.stringify(json[7])}`
                };
                console.log(json_format);
                    
                    $.ajax({
                        url: "./php/mqtt_www.php",
                        type: "POST",
                        data: json_format,
                        success: function (response) {
                            console.log(response);
                        }
                    });
                    document.getElementById('myform').dispatchEvent(new Event('submit'));
                    alert("Success");
                    setTimeout(() => {window.location.reload(false)} , 500);
                }   
        }
        else {
            let error_message = "Send confirmation cannot be completed since the fields are left blank. Please check your information and try again. If you wouldn't like to disclose your personal information, check the anonymous box.";
            if (inputField[i] === 'user_address' || inputField[i] === 'description') {
                document.getElementsByClassName(inputField[i])[0].style = `box-shadow: inset 0 0 0 0.125rem ${error_color};`;
                document.getElementsByName(inputField[i])[0].style = `border: 0.125rem solid ${error_color};`;
            }
            else {
                document.getElementsByClassName(inputField[i])[0].style = `box-shadow: inset 0 0 0 0.125rem ${error_color};`;
            }
            if (inputField[i] != 'file-upload')
                document.getElementById(inputField[i]).reportValidity();

            let invalid_form = `<div>
                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="bomb" class="svg-inline--fa fa-bomb fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M440.5 88.5l-52 52L415 167c9.4 9.4 9.4 24.6 0 33.9l-17.4 17.4c11.8 26.1 18.4 55.1 18.4 85.6 0 114.9-93.1 208-208 208S0 418.9 0 304 93.1 96 208 96c30.5 0 59.5 6.6 85.6 18.4L311 97c9.4-9.4 24.6-9.4 33.9 0l26.5 26.5 52-52 17.1 17zM500 60h-24c-6.6 0-12 5.4-12 12s5.4 12 12 12h24c6.6 0 12-5.4 12-12s-5.4-12-12-12zM440 0c-6.6 0-12 5.4-12 12v24c0 6.6 5.4 12 12 12s12-5.4 12-12V12c0-6.6-5.4-12-12-12zm33.9 55l17-17c4.7-4.7 4.7-12.3 0-17-4.7-4.7-12.3-4.7-17 0l-17 17c-4.7 4.7-4.7 12.3 0 17 4.8 4.7 12.4 4.7 17 0zm-67.8 0c4.7 4.7 12.3 4.7 17 0 4.7-4.7 4.7-12.3 0-17l-17-17c-4.7-4.7-12.3-4.7-17 0-4.7 4.7-4.7 12.3 0 17l17 17zm67.8 34c-4.7-4.7-12.3-4.7-17 0-4.7 4.7-4.7 12.3 0 17l17 17c4.7 4.7 12.3 4.7 17 0 4.7-4.7 4.7-12.3 0-17l-17-17zM112 272c0-35.3 28.7-64 64-64 8.8 0 16-7.2 16-16s-7.2-16-16-16c-52.9 0-96 43.1-96 96 0 8.8 7.2 16 16 16s16-7.2 16-16z"></path></svg>
                                </div>
                                <p>${error_message}</p>`
            document.querySelector('.invalid-form').innerHTML = invalid_form;
        }
    }
    return false;
    // document.getElementsByClassName('fn')[0].style = 'box-shadow: inset 0 0 0 0.125rem red;'
}
document.getElementById('myform').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let inputFile = document.querySelector('#file-upload');
    var currentFile = inputFile.files;
    var formData = new FormData();
    
    var totalfiles = inputFile.files.length;
    for (var index = 0; index < totalfiles; index++) {
        formData.append("upload[]", inputFile.files[index], newFileName_array[index] + "." + currentFile[index].name.slice((currentFile[index].name.lastIndexOf(".")-1 >>> 0)+2 ));
    }

    $.ajax({
        url: './php/upload.php',
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        success : function(data) {
          console.log(data);
      }
    });
});

function alphaOnly(event) {
    var value = String.fromCharCode(event.which);
    var pattern = new RegExp(/^[^0-9]/i);
    return pattern.test(value);
}

function numberOnly(event) {
    var value = String.fromCharCode(event.which);
    var pattern = new RegExp(/[0-9]/i);
    return pattern.test(value);
}

$('#fn').bind('keypress', alphaOnly);
$('#ln').bind('keypress', alphaOnly);
$('#idn').bind('keypress', numberOnly);
$('#pn').bind('keypress', numberOnly);

var inputForm = document.querySelectorAll('input, textarea');

inputForm.forEach(element => {
    element.addEventListener('input', () => {
        for (var i=0; i < inputField.length; i++) {
            document.getElementsByClassName(inputField[i])[0].style.removeProperty('box-shadow');
            if (inputField[i] === 'user_address' || inputField[i] === 'description') {
                document.getElementsByName(inputField[i])[0].style.removeProperty('border');
            }
            document.querySelector('.invalid-form').innerHTML = '';

            document.querySelectorAll('.invalid-input').forEach(element => {
                element.innerHTML = '';
                element.style.display ="none";
            });
        }
    });
});
