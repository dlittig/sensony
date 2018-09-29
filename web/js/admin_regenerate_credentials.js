const warningMessage = 'If you generate new credentials, the existing ones will be overwritten. The users of this account can\'t login with those credentials anymore.'

$(document).ready(function() {
    const element = $('div.generated_username_edit')

    if(isReady()) {
        const button = $('<button class="btn btn-default pull-right" style="margin-bottom: 15px; margin-right: 15px" type="button"><i class="fa fa-refresh"></i> Generate new credentials</button>')
        $(button).on('click', function () {
            if(confirm(warningMessage)) {
                const credentials = generateRandomCredentials()
                $('[name="user[username]"]').val(credentials.username)
                $('[name="user[mail]"]').val(credentials.username)
                $('[name="user[password]"]').val(credentials.password)
            }
        })
        element.parent().prepend(button)
    }
})

const isReady = function() {
    return window.crypto !== null && window.Uint32Array !== null
}

const generateRandomCredentials = function() {
    const FULL_CHARSET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ?!=/%$.-_,#+@'
    const SMALL_CHARSET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'

    const userArray = new Uint32Array(8)
    const pwArray = new Uint32Array(10)
    window.crypto.getRandomValues(userArray)
    window.crypto.getRandomValues(pwArray)
    const result = {
        username: '',
        password: ''
    }

    for(let i=0; i < userArray.length; i++) {
        result.username += SMALL_CHARSET.charAt(userArray[i] % SMALL_CHARSET.length)
    }

    for(let i=0; i < pwArray.length; i++) {
        result.password += FULL_CHARSET.charAt(pwArray[i] % FULL_CHARSET.length)
    }

    return result
}