/**
 * Created by HP ELITEBOOK 840 G5 on 1/6/2021.
 * Written with love by @francnjamb -- Twitter
 */


//make all selectboxes searchable
$('select').select2();
//Initialize Sweet Alert

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000
});

function closeInput(elm) {
    var td = elm.parentNode;
    var value;

    // Work to destroy TinyMCE editor instance
    // Check if element (elm) is a TinyMCE editor instance
    const editorInstance = tinymce.get(elm.id);
    if (editorInstance) {
        value = editorInstance.getContent(); // Retrieve TinyMCE content
        editorInstance.destroy();
        console.log(`TinyMCE Editor Destroyed`);
    } else {
        value = elm.value; //Get Value from a standard textarea / input
    }


    /** Handle Checkbox state */
    var child = td.children[0];
    if (child.type == 'checkbox') {
        value = (child.checked) ? +1 : +0;
    }


    /** Finish handling checkbox state */

    // Remove textarea / input from DOM
    td.removeChild(elm);

    // Update the tds innerHTML with the new (potentially rich) content
    if (child.type == 'checkbox') {
        td.innerHTML = (value === +1) ? 'Yes' : 'No';
    } else {
        td.innerHTML = value;
    }

    const data = td.dataset;
    console.log(`The Data Set`);
    console.table(data);

    /* Check for any request to check authorization of data alteration
     *data-vcheck is the user whose authorization for alteration is being validated
     *data-check holds the value of record owner, we validate against this value
    */


    if (data.check) {
        console.log(`Checking if ${data.vcheck} should be updating a record owned by ${data.check}`);
        if (data.check !== data.vcheck) {
            Toast.fire({
                type: 'error',
                title: 'You are not authorized to update a workflow comment that does not belong to you &#128527; !'
            });
            return;
        } else {
            setTimeout(() => {
                Toast.fire({
                    type: 'success',
                    title: 'Thank you for adding your comment &#128526; .'
                })
            }, 2500);
        }
    }

    // Post Changes
    $.post('./commit', { 'key': data.key, 'name': data.name, 'no': data.no, 'filterKey': data.filterField, 'service': data.service, 'value': value }).done(function (msg) {

        // Update all key dataset values for the row
        if (msg.Key) {
            let parent = td.parentNode;
            parent.querySelectorAll('[data-key]').forEach(elem => elem.dataset.key = msg.Key);
        }

        console.log(`Committing Data.... Result`);
        console.table(msg);


        if (data.validate) // Custom Grid Error Reporting
        {
            let parent = td.parentNode;
            let validationContext = data.validate;
            // check if validate has a comma delimeter(s)
            if (validationContext.indexOf(',') > -1) {
                // Split the sring of comma delimitted string
                validationItems = validationContext.split(',');
                validationItems.forEach((validationItem, key) => {
                    let selector = validationItem.trim();
                    console.log(`Selector: ${selector} -  value: ${msg[validationItem]}`);
                    let validatedElement = parent.querySelector(`.${selector}`);
                    validatedElement.innerHTML = typeof (msg) === 'string' ? `<span class="text-danger">${msg}</span>` : msg[validationItem];
                });
                return true;
            }

            let ClassName = data.validate;
            let validatedElement = parent.querySelector('.' + ClassName);
            const DataKey = data.validate;
            validatedElement.innerHTML = typeof (msg) === 'string' ? `<span class="text-danger">${msg}</span>` : msg[DataKey];
        }



        // Toasting The Outcome
        typemsg = typeof msg;
        console.log(typemsg);
        if (typeof (msg) === 'string') {
            console.log(msg);
            // Fire a sweet alert if you can
            Toast.fire({
                type: 'error',
                title: msg
            })
        } else {

            console.log(msg);
            Toast.fire({
                type: 'success',
                title: msg[data.name] + ' Saved Successfully.'
            })
        }

        // reload if requested from the client
        if (data.reload) {
            setTimeout(() => {
                location.reload();
            }, 500);

        }

    });
}

function addInput(elm, type = false, event) {
    console.log('Event ...');

    if (elm.getElementsByTagName('input').length > 0) return;

    var value = elm.innerHTML;
    console.log(value);
    elm.innerHTML = '';

    var input = document.createElement('input');
    if (type) {
        input.setAttribute('type', type);
    } else {
        input.setAttribute('type', 'text');
    }


    input.style.width = "100%";

    if (type === 'checkbox') {
        input.setAttribute('value', value.trim().toLowerCase() === 'yes' ? 1 : 0);
        if (value.trim().toLowerCase() === 'yes') {
            input.checked = true;
        } else if (value.trim().toLowerCase() === 'no') {
            input.checked = false;
        }
    } else {
        input.setAttribute('value', value.trim());
    }

    input.setAttribute('class', 'form-control');
    input.setAttribute('onBlur', 'closeInput(this)');
    elm.appendChild(input);
    input.focus();
}

function addTextarea(elm) {
    if (elm.getElementsByTagName('textarea').length > 0) return;

    // FIX: use innerHTML instead of innerText to preserve Rich Text formating
    var value = elm.innerHTML.trim();
    elm.innerHTML = '';

    var input = document.createElement('textarea');
    const uniqueId = 'rte-' + Date.now() + Math.floor(Math.random() * 1000);
    input.setAttribute('id', uniqueId);
    input.setAttribute('rows', 2);
    //FIX: use innerHTML instead of innerText to preserve Rich Text formating 
    input.innerHtml = value;
    input.style.width = "350px";
    input.setAttribute('class', 'form-control');
    // input.setAttribute('onBlur', 'closeInput(this)'); // Invoked with tinymce context
    elm.appendChild(input);

    // *** VITAL: Initialize TinyMCE after textarea is in the DOM
    initializeTinyMCE(uniqueId, value); // Initialize TinyMCE
    // input.focus(); // Delegated to TinyMCE's auto_focus
}



// Get Drop Down Filters

function extractFilters(elm, ClassName) {
    let parent = elm.parentNode;
    let filterValue = parent.querySelector('.' + ClassName).innerText;
    console.log(`Subject Parent Value`);
    console.log(filterValue);
    return filterValue;
}

async function addDropDown(elm, resource, filters = {}) {
    if (elm.getElementsByTagName('input').length > 0) return;

    let processedFilters = null;
    if (Object.entries(filters).length) {
        const content = Object.entries(filters);
        processedFilters = Object.assign(...content.map(([key, val]) => ({ [key]: extractFilters(elm, val) })));

        console.log('Extracted Filters.....................');
        console.log(processedFilters);
        console.log(typeof processedFilters);
    }

    elm.innerHTML = 'Please wait ......';

    const ddContent = await getData(resource, processedFilters);
    console.log(`DD Content:`);
    console.log(ddContent);
    elm.innerHTML = '';

    var select = document.createElement('select');
    select.style.width = "300px";// hack to ensure all dynamic dropdowns default to atleast 300px
    const InitialOption = document.createElement('option');

    InitialOption.innerText = 'Select ...';
    select.appendChild(InitialOption);

    // Prepare the returned data and append it on the select

    for (const [key, value] of Object.entries(ddContent)) {
        // console.log(`${key}: ${value}`);
        const option = document.createElement('option');
        option.value = key;
        option.text = value;

        select.appendChild(option);

    }

    select.setAttribute('class', 'form-control');
    select.setAttribute('onChange', 'closeInput(this)');
    elm.appendChild(select);
    select.focus();
    $('select').select2(); // Add a search functionality to dropdown
}

async function getData(resource, filters) {
    payload = JSON.stringify({ ...filters });
    const res = await fetch(`./${resource}`, {
        method: 'POST',
        headers: new Headers({
            Origin: 'http://localhost:2026/',
            "Content-Type": 'application/json',
            //'Content-Type': 'application/x-www-form-urlencoded'
        }),
        body: payload
    });
    const data = await res.json();
    return data;
}


function JquerifyField(model, fieldName) {
    field = fieldName.toLowerCase();
    const selector = '#' + model + '-' + field;
    return selector;
}

/* Function to do ajax field level updating
* This function automatically updates the key field as it changes on each commit
*/

function globalFieldUpdate(entity, controller = false, fieldName, ev, autoPopulateFields = [], service = false) {
    console.log('Global Field Update was invoked');
    const model = entity.toLowerCase();
    const field = fieldName.toLowerCase();
    const formField = '.field-' + model + '-' + fieldName.toLowerCase();
    const keyField = '#' + model + '-key';
    const NoField = '#' + model + '-no';//leave-no
    const targetField = '#' + model + '-' + field;
    const tget = '#' + model + '-' + field;
    const data = $(targetField).data();
    console.log(`...........................Data........................`);
    console.log(data);

    console.log(targetField);
    const fieldValue = ev.target.value;
    const Key = $(keyField).val();
    const No = $(NoField).val();

    console.log(`My Key is ${Key}`);
    console.log(`My Document No is ${No}`);
    console.log(autoPopulateFields);

    let route = '';
    // If controller is falsy use the model value (entity) as the route
    if (!controller) {
        route = model;
    } else {
        route = controller;
    }
    const url = $('input[name=absolute]').val() + route + '/setfield?field=' + fieldName;
    console.log(`route to use is : ${url}`);


    if (Key && Key.length) {
        $.post(url, { fieldValue: fieldValue, 'Key': Key, 'service': service }).done(function (msg) {

            console.log('Original Result ..................');
            console.log(msg);

            if (msg.error) {
                Toast.fire({
                    type: 'error',
                    title: '<b>Error Msg.</b> ' + msg.error.result
                })
                const parent = document.querySelector(formField);
                requestStateUpdater(parent, 'error', msg.error.result);
                return;
            }

            if (msg.Time_out || msg.Time_In) {
                msg = { ...msg, Start_Time: sanitizeTime(msg.Start_Time), End_Time: sanitizeTime(msg.End_Time) };
            }


            // Populate relevant Fields
            $(keyField).val(msg.Key);
            $(targetField).val(msg[fieldName]);

            // Update DOM values for fields specified in autoPopulatedFields: fields in this array should be exact case and name as specified in Nav Web Service 

            if (autoPopulateFields.length > 0) {
                autoPopulateFields.forEach((field) => {
                    let domSelector = JquerifyField(model, field);

                    console.log(`Field of Corncern is ${field}`);
                    console.log(`Model of Corncern is ${model}`);
                    console.log(`Jquerified field is ${domSelector}`);
                    $(domSelector).val(msg[field]);
                });
            }

            /*******End Field auto Population  */
            if ((typeof msg) === 'string') { // A string is an error
                console.log(`Form Field is: ${formField}`);
                const parent = document.querySelector(formField);

                //Inline status notifier
                //notifyError(formField, msg);
                requestStateUpdater(parent, 'error', msg);
                // Fire a sweet alert if you can

                Toast.fire({
                    type: 'error',
                    title: msg
                })

            } else { // An object represents correct details
                const parent = document.querySelector(formField);
                //Inline status notifier
                //notifySuccess(formField, `${field} Saved Successfully.`);
                requestStateUpdater(parent, 'success');
                // If you can Fire a sweet alert                 

                Toast.fire({
                    type: 'success',
                    title: field + ' Saved Successfully.'
                })

            }
        }, 'json');
    }
    else if (No && No.length) {
        console.log(`We are using document no ${No} to read record to update...`)
        $.post(url, { fieldValue: fieldValue, 'No': No, 'service': service }).done(function (msg) {

            console.log('Original Result ..................');
            console.log(msg);

            if (msg.Start_Time || msg.End_Time) {
                msg = { ...msg, Start_Time: sanitizeTime(msg.Start_Time), End_Time: sanitizeTime(msg.End_Time) };
            }

            console.log('Updated Time Manipulation Result ..................');
            console.log(msg);

            // Populate relevant Fields
            $(keyField).val(msg.Key);
            $(targetField).val(msg[fieldName]);

            // Update DOM values for fields specified in autoPopulatedFields: fields in this array should be exact case and name as specified in Nav Web Service 

            if (autoPopulateFields.length > 0) {
                autoPopulateFields.forEach((field) => {
                    let domSelector = JquerifyField(model, field);

                    console.log(`Field of Corncern is ${field}`);
                    console.log(`Model of Corncern is ${model}`);
                    console.log(`Jquerified field is ${domSelector}`);
                    $(domSelector).val(msg[field]);
                });
            }

            /*******End Field auto Population  */
            if ((typeof msg) === 'string') { // A string is an error
                console.log(`Form Field is: ${formField}`);
                const parent = document.querySelector(formField);

                //Inline status notifier
                //notifyError(formField, msg);
                requestStateUpdater(parent, 'error', msg);
                // Fire a sweet alert if you can

                Toast.fire({
                    type: 'error',
                    title: msg
                })

            } else { // An object represents correct details
                const parent = document.querySelector(formField);
                //Inline status notifier
                //notifySuccess(formField, `${field} Saved Successfully.`);
                requestStateUpdater(parent, 'success');
                // If you can Fire a sweet alert                 

                Toast.fire({
                    type: 'success',
                    title: field + ' Saved Successfully.'
                })

            }
        }, 'json');
    }
    console.log('Checking reload .... ');
    console.log(data.reload);
    // Check for a request to reload
    if (data.reload) {
        setTimeout(() => {
            location.reload();
        }, 2000);
    }

}
function disableSubmit() {
    document.getElementById('submit').setAttribute("disabled", "true");
}

function enableSubmit() {
    document.getElementById('submit').removeAttribute("disabled");
}

function requestStateUpdater(fieldParentNode, notificationType, msg = '') {
    if (notificationType === 'success') {
        let span = document.createElement('span');
        span.classList.add('text');
        span.classList.add('text-success');
        span.classList.add('small');
        span.innerText = 'Data saved successfully';

        fieldParentNode.appendChild(span);

        // clean up the notification elements after 3 seconds
        setTimeout(() => {
            span.remove();
        }, 5000);

    } else if (notificationType === 'error' && msg) {
        let span = document.createElement('span');
        span.classList.add('text');
        span.classList.add('text-danger');
        span.classList.add('small');
        span.innerText = msg;

        fieldParentNode.appendChild(span);

        // clean up the notification elements after 7 seconds
        setTimeout(() => {
            span.remove();
        }, 7000);

        // Reload dom after an error occurs -  2ms before error fades
        setTimeout(() => {
            console.log(`Trying to reload.`);
            location.reload(true)
        }, 5000);

    }
}

// File upload Indicator

function uploadIndicator(fieldParentNode, textContent = false) {
    fieldParentNode = document.querySelector(`${fieldParentNode}`);
    let span = document.createElement('span');
    span.classList.add('text');
    span.classList.add('text-success');
    span.classList.add('small');
    span.classList.add('upload-indicator');
    span.innerText = textContent || 'Uploading please wait ....';
    fieldParentNode.appendChild(span);
}

function removeUploadIndicator() {
    let span = document.querySelector('.upload-indicator');
    if (span) span.remove();
}




// Global Uploader

async function globalUpload(attachmentService = false, entity, fieldName, documentService = false) {
    const formField = '.field-' + entity.toLowerCase() + '-' + fieldName.toLowerCase();
    const model = entity.toLowerCase();
    const key = document.querySelector(`#${model}-id`).value;

    const fileInput = document.querySelector(`#${model}-${fieldName}`);
    let endPoint = './upload/';
    let error = false;
    const navPayload = {
        "Key": key
    }

    // show upload indicator
    uploadIndicator(formField);
    setTimeout(() => {  // clean up the notification elements after 3 seconds
        removeUploadIndicator();
    }, 3000);

    const formData = new FormData();
    formData.append("attachment", fileInput.files[0]);
    formData.append("Key", key);
    formData.append("DocumentService", documentService);

    console.table(formData)
    console.log(fileInput.files);
    // Validate file you are uploading
    let file = fileInput.files[0];
    maxSize = +3;// max file threshold in mbs
    //console.log(file);
    if (!['application/pdf'].includes(file.type)) {
        console.log(`We require only PDFs: ${file.name} is of type: ${file.type}`);
        error = `<div class="text text-danger">We require only PDFs: ${file.name} is of type: ${file.type}</div>`;
        msg = `We require only PDFs: ${file.name} is of type: ${file.type}`;
    }
    else if (file.size > (maxSize * 1024 * 1024)) {
        sizeInMB = (+file.size / (1024 * 1024)).toFixed(2);
        error = `<div class="text text-danger">File size violation : ${file.name} is : ${sizeInMB} Mbs , we require less than ${maxSize} Mb</div>`;
        msg = `We require files less than  ${maxSize} Mb: ${file.name} is : ${sizeInMB} Mbs`;
    } else { // Create request payload and upload
        formData.append('attachments[]', file);
    }

    /* if (error) {
         notifyError(formField, msg);
         //_(`#{model}-${fieldName}`).value = '';
         Toast.fire({
             type: 'error',
             title: error
         })
         return;
     }*/


    try {
        const Request = await fetch(endPoint,
            {
                method: "POST",
                body: formData,
                headers: new Headers({
                    Origin: 'http://localhost:2026/'
                })
            });

        const Response = await Request.json();
        // reset file input
        fileInput.value = '';
        console.log(`File Upload Request`);
        console.log(Response);
        if (Response.status === 'success') {
            uploadIndicator(formField, 'Contract File uploaded successfully');
            setTimeout(() => {  // clean up the notification elements after 3 seconds
                removeUploadIndicator();
                location.reload();
            }, 3000);
        }

    } catch (error) {
        console.log(error);
    }
}

// Create an element selector helper function

function _(element) {
    return document.getElementById(element);
}

// Upload multiple Files
async function globalUploadMultiple(attachmentService, entity, route, documentService = false) {
    const formField = '.field-select_multiple';
    const model = entity.toLowerCase();
    const key = _(`${model}-key`).value;
    const no = document.querySelector(`#${model}-no`).value; //option to assist when key doesnt work
    let endPoint = _('absolute').value + `${route}/upload-multiple`;
    // console.log(endPoint); return;
    const navPayload = {
        "Key": key,
        "Service": attachmentService,
        "documentService": documentService,
        "No": no
    }

    const formData = new FormData();
    // formData.append("attachment", fileInput.files[0]);
    formData.append("Key", key);
    formData.append("No", no);
    formData.append("DocumentService", documentService);
    formData.append("attachmentService", attachmentService);



    let error = '';

    try {
        console.log(Array.from(_('select_multiple').files));
        Array.from(_('select_multiple').files).forEach((file) => {
            console.log(file);
            if (!['application/pdf'].includes(file.type)) {
                console.log(`We require only PDFs: ${file.name} is of type: ${file.type}`);
                error = `<div class="text text-danger">We require only PDFs: ${file.name} is of type: ${file.type}</div>`;
            } else { // Create request payload and upload
                formData.append('attachments[]', file);
            }
        });

        if (error) {
            _('upload-note').innerHTML = error;
            _('select_multiple').value = '';
            Toast.fire({
                type: 'error',
                title: error
            })
            return;
        }

        _('progress_bar').style.display = 'block';
        let ajax_request = new XMLHttpRequest();
        ajax_request.open("post", endPoint); // Initiate request

        // Set headers
        ajax_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajax_request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');


        ajax_request.upload.addEventListener('progress', (e) => {
            let percentCompleted = Math.round((e.loaded / e.total) * 100);
            _('progress_bar_process').style.width = `${percentCompleted}%`;
            _('progress_bar_process').innerHTML = `${percentCompleted}% completed`;
            console.log('progress values-------------------');
            console.log(_('progress_bar_process').innerHTML);
        });

        ajax_request.addEventListener('load', (e) => {
            _('upload-note').innerHTML = `<div class="text text-success">Files uploaded successfully.</div>`;
            _('select_multiple').value = '';
        });

        /** I did 2 requests since XMLHttpRequest would not send metadata and multipart data simultaneously,
         * Reason for using fetch was to send attachment metadata like Key, Webservices etc.
         * XMLHttpRequest has progress and load events while fetch didn't have (Used to measure progress)
         * If these limitations are addressed in future, please update the code to only use one request.
         * @francnjamb -  fnjambi@outlook.com
         */
        const request = ajax_request.send(formData);
        const Requesto = await fetch(endPoint,
            {
                method: "POST",
                body: formData,
                headers: new Headers({
                    Origin: 'http://localhost:2026/'
                })
            });

        const res = await Requesto.json();

        console.log(`Data Request......................`);
        console.log(res);

        if (Requesto.ok) {
            notifySuccess(formField, 'Attachments Uploaded Successfully.');
            Toast.fire({
                type: 'success',
                title: 'Attachment uploaded Successfully.'
            });
            // Reload
            setTimeout(() => {
                console.log(`Trying to reload.`);
                location.reload(true)
            }, 1000)
        } else {
            Toast.fire({
                type: 'error',
                title: 'Attachment could not be uploaded.'
            })
        }


        ajax_request.addEventListener('error', (e) => {
            console.log(`Errors...........`);
            console.log(e);
        });

    } catch (error) {
        console.log(error);
    }
}

function sanitizeTime(timeString) {
    if (!timeString) {
        return false;
    }
    let timeParts = timeString.split(":");
    let res = Number(timeParts[0]) + ":" + Number(timeParts[1]);
    console.log('Converted times : ');
    console.log(res);
    return res;
}

function notifySuccess(parentClassField, message) {
    let parent = document.querySelector(parentClassField);

    console.log('Parent to report success to.....................');
    console.dir(parent);
    let span = document.createElement('span');
    span.classList.add('text');
    span.classList.add('text-success');
    span.classList.add('small');
    span.innerText = message;

    parent.appendChild(span);
}

function notifyError(parentClassField, message) {
    let parent = document.querySelector(parentClassField);

    let span = document.createElement('span');
    span.classList.add('text');
    span.classList.add('text-danger');
    span.classList.add('small');
    span.innerText = message;

    parent.appendChild(span);
    removeNotification(parentClassField, 4500);
}

function removeNotification(parentClassField, timeout = 2000) {
    let parent = document.querySelector(parentClassField);
    let closestSpan = parent.querySelectorAll('span');
    //serverError = form.querySelector('.invalid-feedback');
    console.log(`closest span .....`);
    console.log(closestSpan);
    setTimeout(() => {
        //closestSpan.remove();
        Array.prototype.forEach.call(closestSpan, function (node) {
            node.parentNode.removeChild(node);
        });

        // Remove server error
        //serverError.remove();
    }, timeout);

}



//$('.delete').on('click', function (e) {
// Delegate the event
$(document).on('click', '.delete', function (e) {
    e.preventDefault();
    const $deleteButton = $(this); // Cache the button for later use
    if (confirm('Are you sure about deleting this record..?')) {
        let data = $(this).data();
        let url = $(this).attr('href');
        let Key = data.key;
        let Service = data.service;
        const payload = {
            'Key': Key,
            'Service': Service
        };

        let originalContent = $deleteButton.html(); // Store original content to restore it
        $(this).text('Deleting...');
        $(this).attr('disabled', true);

        const res = fetch(url, {
            method: 'DELETE',
            headers: new Headers({
                Origin: 'http://localhost:8080/',
                "Content-Type": 'application/json',
            }),
            body: JSON.stringify({ ...payload })
        })
            .then(response => {

                // Check for 204 No Content specifically
                if (response.status === 204) {
                    return { result: true, note: 'Record deleted successfully.' }; // Fabricate a success object
                }

                // For other successful responses (e.g., 200 OK with content)
                if (response.ok) {
                    return response.json(); // Parse JSON if content is expected
                }
                // For error responses (e.g., 4xx, 5xx)
                return response.json().then(errorData => {
                    // Parse error message from body if available, then reject
                    return Promise.reject(errorData);
                }).catch(() => {
                    // Fallback if error body is not JSON or not present
                    return Promise.reject({ note: `Server error: ${response.status} ${response.statusText}` });
                });
            })
            .then(result => {
                console.log(result);
                if (result.result) {

                    Toast.fire({
                        type: 'success',
                        title: 'Record deleted successfully.'
                    });

                    // Fade out and remove the closest table row (the <tr> element)
                    $deleteButton.closest("tr").fadeOut(400, function () {
                        $(this).remove(); // Remove the row from the DOM after fade out
                    });

                } else {
                    Toast.fire({
                        type: 'error',
                        title: result.note || 'Failed to delete record.'
                    });

                    $deleteButton.html(originalContent); // Restore original text/icon
                    $deleteButton.attr('disabled', false).removeClass('disabled'); // Re-enable button        
                }
            });

    }

});



// Add a new line
$('.add').on('click', function (e) {
    var closestTable = $(this).closest('table');
    e.preventDefault();
    let url = $(this).attr('href');
    let data = $(this).data(); // object of arrays - strange structure
    payloadContent = Object.entries(data);
    // convert object of arrays into a pure object
    // payload = Object.assign(...payloadContent.map(([key, val]) => ({ [key.replace(/(^\w{1})|(\_+\w{1})/g, letter => letter.toUpperCase())]: val })));
    payload = Object.assign(...payloadContent.map(([key, val]) => ({ [key]: val })));
    console.log(`Formatted payload`);
    console.log(payload);

    let initialLabelText = $(this).text();
    $(this).text('Inserting...');
    $(this).attr('disabled', true);
    const res = fetch(url, {
        method: 'POST',
        headers: new Headers({
            Origin: 'http://localhost:8080/',
            "Content-Type": 'application/json'
        }),
        body: JSON.stringify({ ...payload })
    })
        .then(res => res.json())
        .then(result => {
            console.log(`New Record Results .....`);
            console.log(result);
            if (result.id) {
                // Dynamically Add markup
                if (data.template) {
                    let rowResult = result;
                    console.log(`Intending to add a table row`);
                    console.log(rowResult);
                    addRow(rowResult, data, closestTable);
                }
                Toast.fire({
                    type: 'success',
                    title: 'Record inserted successfully.'
                });

                $(this).text(initialLabelText);

                //check if refresh is set to false and skip below refreshing
                if (data?.refresh === 'off') {
                    $(this).text(initialLabelText);
                    console.log(`refresh is set to false ${data.refresh}`);
                    return;
                }
                if (data?.reload) {
                    setTimeout(() => {
                        location.reload(true);
                    }, 100);
                }

            } else {
                Toast.fire({
                    type: 'error',
                    title: result.note
                });

                setTimeout(() => {
                    location.reload(true);
                }, 1500);
            }
        });

});

// Define a function to dynamically add a table row when a new record is successfully created on ERP
function addRow(rowResult, data, context) {
    console.log('passed data');
    console.table(data);
    var nativeContext = context[0];

    // context is a closest table context -  get the template row
    var templateRow = nativeContext.querySelector('.templateRow');

    // Check if templateRow is a valid node
    if (templateRow instanceof Node && templateRow.nodeType === 1) {
        // Clone the template row
        var newRow = templateRow.cloneNode(true);
        newRow.removeAttribute('id');
        newRow.removeAttribute('style'); // Make it visible
        newRow.setAttribute('data-key', rowResult.id);


        // Get the base service endpoint from the "Add" button's data attribute
        const baseServiceEndpoint = data.endpoint;
        console.log('Base Endpoint for new row:', baseServiceEndpoint);


        // Set data-key attribute on each td of the new row
        var tds = newRow.querySelectorAll('td');
        tds.forEach(td => {
            console.log('Key used is: ' + rowResult.id);
            td.setAttribute('data-key', rowResult.id);
            const dataName = td.getAttribute('data-name');
            console.log(`Data Name: ${dataName}`);
            // Set the innerHTML of the td to the corresponding value from the rowResult
            if (rowResult.hasOwnProperty(dataName)) {
                td.innerHTML = rowResult[dataName] || 'Not Set';
            }

            // Construct the full service URL by appending the new ID
            const fullServiceUrl = baseServiceEndpoint.endsWith('/')
                ? baseServiceEndpoint + rowResult.id
                : baseServiceEndpoint + '/' + rowResult.id;

            // Check if this TD should have a data-service attribute
            // We can assume that if it has an ondblclick, it's editable
            if (td.hasAttribute('ondblclick')) {
                td.setAttribute('data-service', fullServiceUrl);
                console.log('New data-service attribute added:', fullServiceUrl);
            }

            // get td with a button and add a data-key attr with a Key Value
            let link = td.querySelector('a');
            if (link) {
                link.setAttribute('data-key', rowResult.id);
                link.setAttribute('data-service', fullServiceUrl);
            }

        });
        console.log('New row added......');
        console.log(newRow);

        // remove empty data placeholder

        var emptyRow = document.querySelector('.empty-record');
        if (emptyRow instanceof Node && emptyRow.nodeType === 1) {
            emptyRow.remove();
        }

        // Add the new row to the context table body
        var tableBody = nativeContext.querySelector('tbody');
        tableBody.insertBefore(newRow, tableBody.firstChild);

    } else {
        console.error('Template row is not a valid node.');
        Toast.fire({
            type: 'danger',
            title: 'Template row is not a valid node.'
        });
    }
}


function InlineUploadIndicator(form) {
    let formID = form.getAttribute('id');
    let subjectForm = document.getElementById(formID);
    let span = document.createElement('span');
    span.classList.add('text');
    span.classList.add('text-success');
    span.classList.add('small');
    span.classList.add('upload-indicator');
    span.innerText = 'Uploading ....';
    subjectForm.appendChild(span);
}


function notifySuccessInline(form, message) {
    let formID = form.getAttribute('id');
    let subjectForm = document.getElementById(formID);
    let span = document.createElement('span');
    span.classList.add('text');
    span.classList.add('text-success');
    span.classList.add('small');
    span.innerText = message;

    subjectForm.appendChild(span);
    removeNotificationInline(form, 4000);
}

function notifyErrorInline(form, message) {
    let formID = form.getAttribute('id');
    let subjectForm = document.getElementById(formID);
    let span = document.createElement('span');
    span.classList.add('text');
    span.classList.add('text-danger');
    span.classList.add('small');
    span.innerText = message;
    subjectForm.appendChild(span);
    removeNotificationInline(form, 4000);
}

function removeNotificationInline(form, timeout = 2000) {
    let closestSpan = form.querySelectorAll('span');
    serverError = form.querySelector('.invalid-feedback');
    console.log(`closest span .....`);
    console.log(closestSpan);
    setTimeout(() => {
        //closestSpan.remove();
        Array.prototype.forEach.call(closestSpan, function (node) {
            node.parentNode.removeChild(node);
        });

        // Remove server error
        serverError.remove();
    }, timeout);

}


// inline file upload

async function InlineGlobalUpload(attachmentService, entity, fieldName, documentService = false, form = false, endpoint = false) {
    //const formField = '.field-' + entity.toLowerCase() + '-' + fieldName.toLowerCase();
    const formField = '.' + $(form).find('input[type="file"]').parent()[0].classList[1];
    //console.log(formField); return ;
    const model = entity.toLowerCase();
    const key = $(form).find(`#${model}-key`).val()
    const no = $(form).find(`#${model}-no`).val()
    const projectNo = $(form).find(`#${model}-project_no`).val()
    const resourceID = $(form).find(`#${model}-resource_id`).val()
    // const fileInput = document.querySelector(`#${model}-${fieldName}`);
    var endPoint = './upload/';
    if (endpoint !== false) {
        var endPoint = './' + endpoint + '/';
    }
    console.log(`Endopoint: ${endPoint}`);

    let error = false;
    const navPayload = {
        "Key": key,
        "No": no,
        "Service": attachmentService,
        "documentService": documentService
    }

    let formFields = $(form).serializeArray();
    let attachment = $(form).find('input[type="file"]').get(0).files[0];



    // show upload indicator
    InlineUploadIndicator(form);

    const formData = new FormData();
    formData.append("attachment", attachment);
    formData.append("Key", key);
    formData.append("No", no);
    formData.append("DocumentService", documentService);
    formData.append("Resource_ID", resourceID);
    formData.append("Project_No", projectNo);

    // Validate file you are uploading
    let file = attachment;
    console.log(file);
    msg = '';
    maxSize = +5;
    if (!['application/pdf'].includes(file.type)) {
        console.log(`We require only PDFs: ${file.name} is of type: ${file.type}`);
        error = `<div class="text text-danger">We require only PDFs: ${file.name} is of type: ${file.type}</div>`;
        msg = `We require only PDFs: ${file.name} is of type: ${file.type}`;
    } else if (file.size > (maxSize * 1024 * 1024)) {
        sizeInMB = (+file.size / (1024 * 1024)).toFixed(2);
        error = `<div class="text text-danger">File size violation : ${file.name} is : ${sizeInMB} Mbs , we require less than ${maxSize} Mb</div>`;
        msg = `We require files less than  ${maxSize} Mb: ${file.name} is : ${sizeInMB} Mbs`;
    } else { // Create request payload and upload
        formData.append('attachments[]', file);
    }

    if (error) {
        notifyErrorInline(form, msg);
        Toast.fire({
            type: 'error',
            title: error
        })
        return;
    }


    try {
        const Request = await fetch(endPoint,
            {
                method: "POST",
                body: formData,
                headers: new Headers({
                    Origin: 'http://localhost:2024/'
                })
            });

        const Response = await Request.json();
        // reset file input
        $(form).find('input[type="file"]').val('');
        console.log(`File Upload Request`);
        console.log(Response);

        let filePath = Response.filePath;



        // Do a Nav Request
        endPoint = `${endPoint}?Key=${navPayload.Key}&No=${navPayload.No}&Service=${navPayload.Service}&filePath=${filePath}&documentService=${navPayload.documentService}`
        const navReq = await fetch(endPoint, {
            method: "GET",
            headers: new Headers({
                Origin: 'http://localhost:80/'
            })
        });

        const NavResp = await navReq.json();
        console.log(`Nav Request Response`);
        console.log(NavResp);
        console.info(navReq.ok);
        if (navReq.ok) {
            // Remove upload indicator
            removeUploadIndicator();

            notifySuccessInline(form, 'Attachment Uploaded Successfully.');
            Toast.fire({
                type: 'success',
                title: 'Attachment uploaded Successfully.'
            });
            // Reload
            setTimeout(() => {
                console.log(`Trying to reload.`);
                location.reload(true)
            }, 100)
        } else {
            Toast.fire({
                type: 'error',
                title: 'Attachment could not be uploaded.'
            })
        }

    } catch (error) {
        console.log(error);
    }
}

// Event delegation for new Rows, affects tables too

// This new function will handle all event delegation
function initTableEvents(table) {
    // Use event delegation on the table body for dblclick events
    table.addEventListener('dblclick', (event) => {
        // Check if the double-clicked element is a <td>
        const target = event.target.closest('td[data-name]');
        if (!target) return;

        // Based on the data attributes, determine which input function to call
        if (target.hasAttribute('ondblclick')) {
            const dblclickAttr = target.getAttribute('ondblclick');
            // Use a new function to safely execute the on-demand logic
            executeDblclick(target, dblclickAttr);
        }
    });
}

function executeDblclick(elm, attrValue) {
    // Parse the function call from the attribute string
    const regex = /(\w+)\(([^)]*)\)/;
    const match = attrValue.match(regex);
    if (!match) return;

    const functionName = match[1];
    const argsString = match[2].split(',').map(arg => arg.trim());
    const args = argsString.map(arg => {
        // Check if the argument is a string (and strip quotes), otherwise return as is.
        if (arg.startsWith("'") && arg.endsWith("'")) {
            return arg.substring(1, arg.length - 1);
        }
        return arg;
    });

    switch (functionName) {
        case 'addTextarea':
            addTextarea(elm);
            break;
        case 'addInput':
            addInput(elm, args[0]);
            break;
        case 'addDropDown':
            // The `addDropDown` function expects an element, a resource string, and an optional filters object
            // Here we will need to replicate the logic from your existing ondblclick.
            // This is a simplified example. You may need to adjust based on the complexity of your filters.
            const resource = args[0];
            const filters = args[1] ? JSON.parse(args[1]) : {};
            addDropDown(elm, resource, filters);
            break;
        default:
            console.warn(`Function "${functionName}" not supported for event delegation.`);
    }
}

// Global initialization of event delegation
document.addEventListener('DOMContentLoaded', () => {
    const allTables = document.querySelectorAll('table');
    allTables.forEach(table => {
        initTableEvents(table);
    });
});












