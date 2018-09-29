var textFile = null
const makeCredentialFile = function(text) {
    var data = new Blob([text], {type: 'text/plain'})

    // If we are replacing a previously generated file we need to
    // manually revoke the object URL to avoid memory leaks.
    if (textFile !== null) {
        window.URL.revokeObjectURL(textFile)
    }

    textFile = window.URL.createObjectURL(data)

    // returns a URL you can use as a href
    return textFile
}

$(document).ready(function() {
    const element = $('div.generated_password_new, div.generated_password_edit')
    const button = $('<button class="btn btn-success pull-right" type="button" style="margin-bottom: 15px"><i class="fa fa-download"></i> Save credentials</button>')
    $(button).on('click', function() {
        const mail = $('[name="user[mail]"]').val()
        const password = $('[name="user[password]"]').val()

        const link = document.createElement('a');
        link.setAttribute('download', 'credentials.txt');
        link.href = makeCredentialFile('Username: ' + mail + '\r\n' + 'Password: ' + password);
        document.body.appendChild(link);

        // wait for the link to be added to the document
        window.requestAnimationFrame(function () {
            const event = new MouseEvent('click');
            link.dispatchEvent(event);
            document.body.removeChild(link);
        });
    })
    element.append(button)
})