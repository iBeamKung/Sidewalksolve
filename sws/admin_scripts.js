// get data from database

const page = document.querySelector('.page__wrapper');

const observer = new MutationObserver(mutations => {
    mutations.forEach(record => {
        console.log(record);
    });
});

observer.observe(page, {
    attributes: true,
    childList: true
});

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KiB', 'MiB' ];

    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

var total_pages = 0;
const MONTH__ABBREVIATION = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

var ajax = new XMLHttpRequest();
var method = "GET";
var url = "./php/data.php";
var asynchronous = true;

ajax.open(method, url, asynchronous);
ajax.send();

ajax.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var data = JSON.parse(this.responseText);
        console.log(data);

        if ((data.length/5) % 1 === 0 ) {
            total_pages = Math.floor((data.length)/5);
        }
        else {
            total_pages = Math.floor((data.length)/5) + 1;
        }

        document.getElementsByClassName('total_pages')[0].innerHTML = `of ${total_pages}`;
        document.getElementById('input_number').setAttribute('max', `${total_pages}`);
        document.getElementsByClassName('to_lastpage')[0].setAttribute('onclick', `this.parentNode.querySelector('#input_number').value = ${total_pages}; page_change();`);
        if (document.querySelector('#input_number').value == total_pages) {
            document.getElementsByClassName('next')[0].setAttribute('disabled', 'disabled');
            document.getElementsByClassName('to_lastpage')[0].setAttribute('disabled', 'disabled');
        }

        var page_html = "";
        var complaint_html = "";
        var info_html = "";
        var file_html = "";
        var fileAttach = "";

        for (var i=0; i < data.length; i++) {
            // data from database
            var firstName = data[i].firstname || "Anonymous";
            var lastName = data[i].lastname;
            var idNum = data[i].idcard_num;
            var phoneNum = data[i].tel_num;
            var address = data[i].address;
            var description = data[i].description;
            var dateTime = data[i].time;
            var fromWhere = data[i].from_where;
            if (data[i].video_src == "") {
                fileAttach = data[i].photo_src;
            }
            else if (data[i].photo_src == "") {
                fileAttach = data[i].video_src;
            }
            else {
                fileAttach = data[i].video_src + ',' + data[i].photo_src;  
            }
            console.log(fileAttach);
            // change dateTime format
            var time = dateTime.split('T')[1];
            var date = dateTime.split('T')[0];
            var day = date.split('-')[2];
            var month;
            if (date.split('-')[1].charAt(0) == '0') {
                month = MONTH__ABBREVIATION[Number(date.split('-')[1].replace("0",""))];
            }
            else {
                month = MONTH__ABBREVIATION[Number(date.split('-')[1])];
            }
            var year = date.split('-')[0];
            
            //change timestamp format
            var timestamp = data[i].timestamp.split(' ');
            var timestampTime = timestamp[1];
            var timestampDay = timestamp[0].split('-').pop();
            var timestampMonth;
            if (timestamp[0].split('-')[1].charAt(0) == '0') {
                timestampMonth = MONTH__ABBREVIATION[Number(timestamp[0].split('-')[1].replace("0",""))];
            }
            else {
                timestampMonth = MONTH__ABBREVIATION[Number(timestamp[0].split('-')[1])];
            }
            var timestampYear = timestamp[0].split('-')[0];
            
            //Status
            var status = data[i].success;
            if (status == 1) {
                status = 'Completed';
            }
            else {
                status = 'Pending';
            }
            
            var limit_item_per_page = Math.floor(i/5);

            if ((i/5) % 1 === 0) {
                page_html = `<div class="page page-${limit_item_per_page + 1}"> </div>`;
                document.getElementsByClassName('page__wrapper')[0].innerHTML += page_html;
            }

            complaint_html = `  <div class="complaint__wrapper item-${i+1}">
                                    <span>Id</span>
                                    <span>#${data[i].id}</span>
                                    <span>Name</span>
                                    <span class="th_lang_pos">${firstName} ${lastName}</span>
                                    <span>Date</span>
                                    <span>${timestampDay} ${timestampMonth} ${timestampYear}</span>
                                    <span>Status</span>
                                    <span>${status}</span>
                                    <button class="extend_info">
                                        <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="chevron-down" class="svg-inline--fa fa-chevron-down fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"></path></svg>
                                    </button>
                                </div>  `;
            document.getElementsByClassName(`page-${limit_item_per_page + 1}`)[0].innerHTML += complaint_html;

            info_html = `   <div class="main__info info-${i+1}">
                                <div class="info__wrapper">
                                    <div class="avatar">
                                        <span class="th_lang_pos">${firstName.charAt(0)}</span>
                                    </div>
                                    <div class="full_name th_lang_pos">${firstName} ${lastName} <br> <span>to Sidewalk.solve</span></div>
                                    <div class="date">
                                        <span>
                                            ${timestampDay} ${timestampMonth} ${timestampYear} ${timestampTime}
                                        </span>
                                    </div>
                                </div>
                                <div class="content__wrapper content-${i+1}">
                                    <div class="inform">
                                        <div>
                                            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="exclamation-triangle" class="svg-inline--fa fa-exclamation-triangle fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z"></path></svg>
                                        </div>
                                        <span><span>Privacy warning:</span> This section includes complainant personal information. Sidewalksolve does not authorize administrators to disclose complainant personal information for other purposes.  </span>
                                    </div>

                                    <label for="first_name">First name</label>
                                    <label for="last_name">Last name</label>
                                    <div class="dinput_field first_name">
                                        <input class="first_name th_lang_pos" value="${firstName}" disabled/>
                                        <button class="clipboard">
                                            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v36c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-36h42c3.3 0 6 2.7 6 6z"></path></svg>
                                        </button>
                                    </div> 
                                    <div class="dinput_field last_name">
                                    <input class="last_name th_lang_pos" value="${lastName}" disabled/>
                                    <button class="clipboard">
                                            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v36c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-36h42c3.3 0 6 2.7 6 6z"></path></svg>
                                    </button>
                                    </div>
                                    
                                    <label for="id_number">Identification card number</label>
                                    <label for="phone_number">Phone number</label>
                                    <div class="dinput_field id_number">
                                        <input class="id_number" value="${idNum}" disabled/>
                                        <button class="clipboard">
                                            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v36c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-36h42c3.3 0 6 2.7 6 6z"></path></svg>
                                        </button>
                                    </div>
                                    <div class="dinput_field phone_number">
                                        <input class="phone_number" value="${phoneNum}" disabled/>
                                        <button class="clipboard">
                                            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v36c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-36h42c3.3 0 6 2.7 6 6z"></path></svg>
                                        </button>
                                    </div>
                                
                                    <label for="address">Address</label>
                                    <textarea class="address th_lang_pos" disabled>${address}</textarea>

                                    <label for="description">Description</label>
                                    <textarea class="description th_lang_pos" disabled>${description}</textarea>

                                    <label for="date">Date</label>
                                    <div class="dinput_field date">
                                        <input class="date" value="${day} ${month} ${year} ${time}" disabled/>
                                        <button class="clipboard">
                                            <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v36c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-36h42c3.3 0 6 2.7 6 6z"></path></svg>
                                        </button>
                                    </div>

                                    <label for="image_upload">Attached image</label>
                                    <!-- ========== file item will come from JavaScript ========== -->
                                </div>
                            </div>  `;
            document.getElementsByClassName(`page-${limit_item_per_page + 1}`)[0].innerHTML += info_html;

            if (fileAttach != null) {
                const INDIVIDUAL__FILESRC = fileAttach.split(',');
                var filePath;
                var fileName;
                var fileSize;
                var fileType;

                async function getResponseHeaders(fileName, filePath, i) {
                    return fetch(`./uploads/${fromWhere}/${filePath}/${fileName}`, { 
                        method: 'GET',
                        mode: 'cors'
                    })
                    .then( function(response) {
                        return [response.headers.get('Content-Length'), response.headers.get('Content-Type'), i];
                    })
                    .then( function(data) {
                        return data;
                    });
                }
                INDIVIDUAL__FILESRC.forEach((element, index) => {   
                    fileName = element.split('/').pop();
                    filePath = element.split('/')[3];

                    getResponseHeaders(fileName, filePath, i)
                        .then((fetch_data) => {
                        console.log(fetch_data);

                        fileName = element.split('/').pop();
                        fileSize = formatBytes(Number(fetch_data[0]), 3);
                        fileType = fetch_data[1];

                        file_html = `   <div class="dinput_field image_upload image_${index + 1}">
                                            <div>
                                                <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="image" class="svg-inline--fa fa-image fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm-6 336H54a6 6 0 0 1-6-6V118a6 6 0 0 1 6-6h404a6 6 0 0 1 6 6v276a6 6 0 0 1-6 6zM128 152c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40zM96 352h320v-80l-87.515-87.515c-4.686-4.686-12.284-4.686-16.971 0L192 304l-39.515-39.515c-4.686-4.686-12.284-4.686-16.971 0L96 304v48z"></path></svg>
                                            </div>
                                            <div>
                                                <span>${fileName}</span>
                                                <span>${fileSize}</span>
                                            </div>
                                            
                                            <button onclick="window.open('${element}');">
                                                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="eye" class="svg-inline--fa fa-eye fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg>
                                            </button>
                                            <a href="${element}" download>
                                                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="file-download" class="svg-inline--fa fa-file-download fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm76.45 211.36l-96.42 95.7c-6.65 6.61-17.39 6.61-24.04 0l-96.42-95.7C73.42 337.29 80.54 320 94.82 320H160v-80c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v80h65.18c14.28 0 21.4 17.29 11.27 27.36zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z"></path></svg>
                                            </a>
                                            <div class="tooltip" data-direction="left">
                                                <div class="tooltip_initiator">
                                                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" class="svg-inline--fa fa-info-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z"></path></svg>
                                                </div>
                                                <div class="tooltip_item">
                                                    <p>
                                                        <span>File name:</span> <span style="word-break: break-all;"> ${fileName}</span><br>
                                                        <span>File size:</span> ${fileSize}<br>
                                                        <span>File type:</span> ${fileType}<br><br>
                                                        This file name is not original. It has been renamed to make it unique.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                        `;
                        document.getElementsByClassName(`content-${fetch_data[2]+1}`)[0].innerHTML += file_html;
                        
                        
                    }); 
                });
            }
            file_html = `<button class="button approve" onmousedown="start(this)" onmouseup="end(this)"  ontouchstart="approveForMobile(this);">Approve</button>`;
                        document.getElementsByClassName(`info-${i+1}`)[0].innerHTML += file_html;
        }
        var arabicPattern = /[\u0e01-\u0e5b]/i;
        var list = document.getElementsByClassName('th_lang_pos');
        for (var i = 0; i < list.length; i++) {
            var lang;
            if (arabicPattern.test(list[i].textContent) || arabicPattern.test(list[i].value)) {
                lang = 'th';
            }
            else {
                lang = 'en';
            }
            list[i].setAttribute('lang', lang);
        }
        console.log(document.getElementsByClassName('page__wrapper')[0].childElementCount);
    }
}

// toggle complainant personal information. Event bubbling

document.querySelector('.page__wrapper').addEventListener('click', function(event) {
    if (event.target.className.toLowerCase() === "extend_info") {
        let parent_className = event.target.parentNode.className;
        let show_info = document.getElementsByClassName(`info-${parent_className.substr(parent_className.lastIndexOf('-') + 1, parent_className.length)}`)[0];

        if(show_info.style.display !== "flex") {
            event.target.children[0].style.transform = "rotate(180deg)";
            event.target.children[0].style.transition = "all .3s ease";

            show_info.style.display = "flex";
        }
        else {
            event.target.children[0].style.transform = "rotate(0deg)";

            show_info.style.display = "none";
        }
    }
    else if (event.target.className.toLowerCase() === "clipboard") {
        event.target.addEventListener('mouseout', () => {
            event.target.innerHTML = `<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v36c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-36h42c3.3 0 6 2.7 6 6z"></path></svg>`
        });
        // navigator clipboard api needs a secure context (https)
        if (navigator.clipboard && window.isSecureContext) {
            let copyText = event.target.parentNode.getElementsByTagName('input')[0];

            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile device

            navigator.clipboard.writeText(copyText.value);

            event.target.innerHTML = `<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M384 112v352c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V112c0-26.51 21.49-48 48-48h80c0-35.29 28.71-64 64-64s64 28.71 64 64h80c26.51 0 48 21.49 48 48zM192 40c-13.255 0-24 10.745-24 24s10.745 24 24 24 24-10.745 24-24-10.745-24-24-24m96 114v-20a6 6 0 0 0-6-6H102a6 6 0 0 0-6 6v20a6 6 0 0 0 6 6h180a6 6 0 0 0 6-6z"></path></svg>`
        }
    }
}, false);

// paginator

var page_btn = document.querySelectorAll('button');
var current_page = document.querySelector('#input_number');

// push the button to select a page

// for (var i=0; i < page_btn.length; i++) {
//     page_btn[i].addEventListener('click', function(e) {
function page_change () {

        if (current_page.value == 1) {
            document.getElementsByClassName('previous')[0].setAttribute('disabled', 'disabled');
            document.getElementsByClassName('to_firstpage')[0].setAttribute('disabled', 'disabled');
        }
        else {
            document.getElementsByClassName('previous')[0].removeAttribute('disabled');
            document.getElementsByClassName('to_firstpage')[0].removeAttribute('disabled');
        }

        if (current_page.value == total_pages) {
            document.getElementsByClassName('next')[0].setAttribute('disabled', 'disabled');
            document.getElementsByClassName('to_lastpage')[0].setAttribute('disabled', 'disabled');
        }
        else {
            document.getElementsByClassName('next')[0].removeAttribute('disabled');
            document.getElementsByClassName('to_lastpage')[0].removeAttribute('disabled');
        }

        let all_pages = document.querySelectorAll('.page');
        all_pages.forEach(element => {
            let pattern = `/(page-${current_page.value})/`;
            let class_name = new RegExp(pattern, "g");
            if (class_name.test(element.className) != true) {
                element.style.display = "none";
            }
        });
        document.getElementsByClassName(`page-${current_page.value}`)[0].style.display = "block";
    };
//     });
// }

var previous_page = 0;

current_page.addEventListener('focus', () => {
    previous_page = current_page.value;
});

// enter the input to select a page

current_page.addEventListener('change', function(e) {
    if (current_page.value > total_pages || current_page.value < 1) {
        current_page.value = previous_page;
    }
    else {
        let all_pages = document.querySelectorAll('.page');
        all_pages.forEach(element => {
            let pattern = `/(page-${e.target.value})/`;
            let class_name = new RegExp(pattern, "g");
            if (class_name.test(element.className) != true) {
                element.style.display = "none";
            }
        });
        document.getElementsByClassName(`page-${e.target.value}`)[0].style.display = "block";
    }
})

// copy input text to clipboard

var clipboard = document.querySelectorAll('.clipboard');
clipboard.forEach(element => {
    element.addEventListener('mouseout', () => {
        element.innerHTML = `<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M336 64h-80c0-35.3-28.7-64-64-64s-64 28.7-64 64H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM192 40c13.3 0 24 10.7 24 24s-10.7 24-24 24-24-10.7-24-24 10.7-24 24-24zm144 418c0 3.3-2.7 6-6 6H54c-3.3 0-6-2.7-6-6V118c0-3.3 2.7-6 6-6h42v36c0 6.6 5.4 12 12 12h168c6.6 0 12-5.4 12-12v-36h42c3.3 0 6 2.7 6 6z"></path></svg>`
    });
    element.addEventListener('click', () => {
        let copyText = element.parentNode.querySelector('input');

        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile device

        navigator.clipboard.writeText(copyText.value);

        element.innerHTML = `<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="clipboard" class="svg-inline--fa fa-clipboard fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M384 112v352c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V112c0-26.51 21.49-48 48-48h80c0-35.29 28.71-64 64-64s64 28.71 64 64h80c26.51 0 48 21.49 48 48zM192 40c-13.255 0-24 10.745-24 24s10.745 24 24 24 24-10.745 24-24-10.745-24-24-24m96 114v-20a6 6 0 0 0-6-6H102a6 6 0 0 0-6 6v20a6 6 0 0 0 6 6h180a6 6 0 0 0 6-6z"></path></svg>`
    });
});
   
var counter;
var countTime = 0;


function start(e) {
    if (countTime < 100) {
       e.innerHTML = 
        `<div class='circle__wrapper'>
            <svg width='35px' height='35px' shape-rendering="geometricPrecision">
                <circle cx="15" cy="15" r="15" shape-rendering="geometricPrecision"></circle>
                <circle cx="15" cy="15" r="15"></circle>
            </svg>
        </div>`; 
    }
    
    counter = setInterval(function() {
        document.documentElement.style.setProperty('--afterWidth', `${countTime*1}`);
        countTime++;
        if (countTime == 100) {
            setSuccess(e);
        }
    }, 10);
    
}

function approveForMobile(e) {
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        setSuccess(e);
    }
}


function end(e) {
    document.documentElement.style.setProperty('--afterWidth', '0');
    countTime = 0;
    e.innerHTML = "Approve";
    clearInterval(counter);
}

function setSuccess(e) {
    e.innerHTML = "<div class='text_complete' style='opacity: 0;'> Completed </div>";
            setTimeout(function(){ document.getElementsByClassName('text_complete')[0].style.opacity = 1; }, 10);
            var defaults = { origin: { y: 0.99 } };

            function fire(particleRatio, opts) {
                confetti(Object.assign({}, defaults, opts, {
                    particleCount: Math.floor(200 * particleRatio)
                }));
            }

            fire(0.25, { spread: 26, startVelocity: 55, });
            fire(0.2, { spread: 60, });
            fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
            fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
            fire(0.1, { spread: 120, startVelocity: 45, });
            
            let infoIndex = Number(e.parentNode.className.split('-').pop());
            console.log(infoIndex);
            let itemIndex = (infoIndex - (5* Math.floor((infoIndex - 1) / 5))) - (2 - (infoIndex - (5* Math.floor((infoIndex - 1) / 5))));
            console.log(itemIndex);
            let itemId = e.parentNode.parentNode.children[itemIndex].children[1].textContent;
            console.log(itemId.replace('#', ''));
            
            $.ajax({
                type: "POST",
                url: "./php/set_success.php",
                data: { Id: Number(itemId.replace('#','')) },
                success: function(data) {
                    console.log(data);
                }
            });
}

document.getElementsByClassName('logout_button')[0].addEventListener('click', () => {
    $.ajax({
        type: "POST",
        url: "./php/admin_logout.php",
        data: { action: "Logout" },
        success: function() {
          window.location.reload(false);   
        }
    });
});


